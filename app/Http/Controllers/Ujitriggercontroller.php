<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class UjiTriggerController extends Controller
{
    // Jumlah objek yang diharapkan setelah revisi
    // PROCEDURE: sp_update_total_pengadaan, sp_update_total_penjualan, sp_selesaikan_pengadaan,
    //            sp_toggle_status_barang, sp_aktifkan_margin, sp_laporan_penjualan_berdasarkan_user,
    //            sp_laporan_pengadaan_vendor, sp_riwayat_kartu_stok_barang
    // FUNCTION : fn_get_sisa_stok, fn_hitung_harga_jual, fn_hitung_subtotal, fn_hitung_ppn,
    //            fn_get_margin_aktif, fn_hitung_total_penerimaan, fn_status_badan_hukum_vendor
    private const TARGET = ['PROCEDURE' => 8, 'FUNCTION' => 7, 'TRIGGER' => 21];

    public function index(Request $request)
    {
        $db = DB::selectOne('SELECT DATABASE() AS nama')->nama;

        // ---------- Status objek database yang benar-benar ada ----------
        $routines = DB::select(
            'SELECT ROUTINE_TYPE AS jenis, ROUTINE_NAME AS nama
             FROM information_schema.ROUTINES
             WHERE ROUTINE_SCHEMA = ?
             ORDER BY ROUTINE_TYPE, ROUTINE_NAME',
            [$db]
        );
        $triggers = DB::select(
            'SELECT TRIGGER_NAME AS nama, EVENT_OBJECT_TABLE AS tabel,
                    ACTION_TIMING AS waktu, EVENT_MANIPULATION AS aksi
             FROM information_schema.TRIGGERS
             WHERE TRIGGER_SCHEMA = ?
             ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION',
            [$db]
        );

        $jumlah = [
            'PROCEDURE' => collect($routines)->where('jenis', 'PROCEDURE')->count(),
            'FUNCTION'  => collect($routines)->where('jenis', 'FUNCTION')->count(),
            'TRIGGER'   => count($triggers),
        ];
        $target = self::TARGET;

        // ---------- Uji otomatis (hanya saat tombol ditekan / POST) ----------
        $hasilUji  = null;
        $kartuBaru = [];
        $logBaru   = [];
        $pesanSiap = null;

        if ($request->isMethod('post')) {
            $engine = DB::selectOne(
                "SELECT ENGINE AS engine FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'kartu_stok'",
                [$db]
            );

            if (!$engine || strtoupper((string) $engine->engine) !== 'INNODB') {
                $pesanSiap = 'Uji dibatalkan: tabel harus bermesin InnoDB agar rollback bekerja.';
            } elseif ($jumlah['TRIGGER'] === 0) {
                $pesanSiap = 'Uji dibatalkan: belum ada trigger di database. Jalankan TRIGGER_DB_PROJECT_3.sql dulu.';
            } elseif (!$this->tabelAda($db, 'log_aktivitas') || !$this->tabelAda($db, 'log_perubahan_harga')) {
                $pesanSiap = 'Uji dibatalkan: tabel log_aktivitas / log_perubahan_harga belum ada.';
            } else {
                [$hasilUji, $kartuBaru, $logBaru] = $this->jalankanUji();
            }
        }

        // ---------- Data terbaru (hasil transaksi nyata dari web) ----------
        $kartuTerakhir = DB::select(
            'SELECT k.idkartu_stok, b.nama, k.jenis_transaksi, k.masuk, k.keluar,
                    k.stock, k.idtransaksi, k.created_at
             FROM kartu_stok k
             JOIN barang b ON b.idbarang = k.idbarang
             ORDER BY k.idkartu_stok DESC
             LIMIT 10'
        );
        $logAktivitas = $this->tabelAda($db, 'log_aktivitas')
            ? DB::select('SELECT * FROM log_aktivitas ORDER BY idlog DESC LIMIT 10') : [];
        $logHarga = $this->tabelAda($db, 'log_perubahan_harga')
            ? DB::select('SELECT * FROM log_perubahan_harga ORDER BY idlog DESC LIMIT 10') : [];

        return view('trigger.uji_trigger', compact(
            'routines', 'triggers', 'jumlah', 'target',
            'hasilUji', 'kartuBaru', 'logBaru', 'pesanSiap',
            'kartuTerakhir', 'logAktivitas', 'logHarga'
        ));
    }

    private function tabelAda(string $db, string $tabel): bool
    {
        return DB::selectOne(
            'SELECT COUNT(*) AS n FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
            [$db, $tabel]
        )->n > 0;
    }

    /** Ambil satu nilai (kolom pertama baris pertama) dengan raw SQL */
    private function nilai(string $sql, array $bind = [])
    {
        $row = DB::selectOne($sql, $bind);
        return $row ? array_values((array) $row)[0] : null;
    }

    /**
     * Menjalankan rangkaian transaksi uji di dalam transaction, lalu ROLLBACK.
     * Transaction HANYA dipakai di halaman uji ini agar data uji tidak tersisa;
     * seluruh proses bisnis aplikasi (pengadaan, penerimaan, dst.) tidak memakainya.
     */
    private function jalankanUji(): array
    {
        $hasil = [];
        $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
        $tambah = function (string $langkah, string $trigger, string $aktual, string $harapan, bool $lulus) use (&$hasil) {
            $hasil[] = compact('langkah', 'trigger', 'aktual', 'harapan', 'lulus');
        };

        $iduser   = $this->nilai('SELECT MIN(iduser) FROM `user`');
        $idvendor = $this->nilai('SELECT MIN(idvendor) FROM vendor');
        $idbarang = $this->nilai('SELECT MIN(idbarang) FROM barang WHERE status = 1 AND harga > 0');
        $idmargin = $this->nilai('SELECT fn_get_margin_aktif()');

        if (!$iduser || !$idvendor || !$idbarang || !$idmargin) {
            $tambah('Persiapan', '-', 'Data master (user, vendor, barang aktif, margin aktif) belum lengkap',
                'data master terisi dan ada 1 margin aktif', false);
            return [$hasil, [], []];
        }

        $maxKartu    = (int) $this->nilai('SELECT COALESCE(MAX(idkartu_stok), 0) FROM kartu_stok');
        $maxLogAkt   = (int) $this->nilai('SELECT COALESCE(MAX(idlog), 0) FROM log_aktivitas');
        $maxLogHarga = (int) $this->nilai('SELECT COALESCE(MAX(idlog), 0) FROM log_perubahan_harga');

        $stok  = fn () => (int) $this->nilai('SELECT fn_get_sisa_stok(?)', [$idbarang]);
        $harga = fn () => (int) $this->nilai('SELECT harga FROM barang WHERE idbarang = ?', [$idbarang]);

        $stokAwal  = null;
        $hargaAwal = $harga();
        $kartuBaru = [];
        $logBaru   = [];

        DB::beginTransaction();
        try {
            $stokAwal = $stok();

            // ===== 1-2. PENGADAAN: harga otomatis dari barang, header otomatis =====
            DB::insert("INSERT INTO pengadaan (user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
                        VALUES (?, 'P', ?, 0, 0, 0)", [$iduser, $idvendor]);
            $idpengadaan = DB::getPdo()->lastInsertId();

            DB::insert("INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
                        VALUES (0, 10, 0, ?, ?)", [$idbarang, $idpengadaan]);

            $dp = DB::selectOne('SELECT * FROM detail_pengadaan WHERE idpengadaan = ?', [$idpengadaan]);
            $hd = DB::selectOne('SELECT * FROM pengadaan WHERE idpengadaan = ?', [$idpengadaan]);

            $subP = $hargaAwal * 10;
            $ppnP = intdiv($subP * 11 + 50, 100);
            $tambah(
                '1. Detail pengadaan 10 pcs (harga_satuan & sub_total diisi 0)',
                'trg_calc_sub_detail_pengadaan + fn_hitung_subtotal',
                'harga_satuan ' . $rp($dp->harga_satuan) . ', sub_total ' . $rp($dp->sub_total),
                'harga_satuan ' . $rp($hargaAwal) . ' (harga barang), sub_total ' . $rp($subP),
                (int) $dp->harga_satuan === $hargaAwal && (int) $dp->sub_total === $subP
            );
            $tambah(
                '2. Header pengadaan dihitung ulang',
                'trg_update_header_pengadaan + sp_update_total_pengadaan + fn_hitung_ppn',
                'subtotal ' . $rp($hd->subtotal_nilai) . ', ppn ' . $rp($hd->ppn) . ', total ' . $rp($hd->total_nilai),
                'subtotal ' . $rp($subP) . ', ppn ' . $rp($ppnP) . ', total ' . $rp($subP + $ppnP),
                (int) $hd->subtotal_nilai === $subP && (int) $hd->ppn === $ppnP && (int) $hd->total_nilai === $subP + $ppnP
            );

            // ===== 3-6. PENERIMAAN dengan harga dari vendor naik Rp 5.000 =====
            $hargaTerima = $hargaAwal + 5000;
            DB::insert("INSERT INTO penerimaan (status, idpengadaan, iduser) VALUES ('S', ?, ?)", [$idpengadaan, $iduser]);
            $idpenerimaan = DB::getPdo()->lastInsertId();

            DB::insert("INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
                        VALUES (?, ?, 10, ?, 0)", [$idpenerimaan, $idbarang, $hargaTerima]);
            $dt = DB::selectOne('SELECT * FROM detail_penerimaan WHERE idpenerimaan = ?', [$idpenerimaan]);
            $iddetailterima = $dt->iddetail_penerimaan;

            $tambah(
                '3. Detail penerimaan 10 pcs, harga terima ' . $rp($hargaTerima) . ' (sub_total_terima diisi 0)',
                'trg_calc_sub_detail_penerimaan + fn_hitung_subtotal',
                'sub_total_terima = ' . $rp($dt->sub_total_terima),
                'sub_total_terima = ' . $rp($hargaTerima * 10),
                (int) $dt->sub_total_terima === $hargaTerima * 10
            );

            $stokTerima = $stok();
            $kartuM = DB::selectOne('SELECT * FROM kartu_stok WHERE idbarang = ? ORDER BY idkartu_stok DESC LIMIT 1', [$idbarang]);
            $tambah(
                '4. Stok masuk dari penerimaan 10 pcs',
                'trg_kartu_stok_penerimaan',
                "stok $stokAwal menjadi $stokTerima, jenis kartu stok {$kartuM->jenis_transaksi}",
                'stok ' . $stokAwal . ' menjadi ' . ($stokAwal + 10) . ', jenis M',
                $stokTerima === $stokAwal + 10 && $kartuM->jenis_transaksi === 'M'
            );

            $hargaSetelahTerima = $harga();
            $lh = DB::selectOne('SELECT * FROM log_perubahan_harga WHERE idlog > ? ORDER BY idlog ASC LIMIT 1', [$maxLogHarga]);
            $tambah(
                '5. Harga di penerimaan berbeda: harga barang ikut berubah + tercatat di log',
                'trg_kartu_stok_penerimaan (UPDATE barang) + trg_log_update_harga_barang',
                'harga barang ' . $rp($hargaSetelahTerima) . ($lh ? ', log ' . $rp($lh->harga_lama) . ' menjadi ' . $rp($lh->harga_baru) : ', tidak ada log'),
                'harga barang ' . $rp($hargaTerima) . ', log ' . $rp($hargaAwal) . ' menjadi ' . $rp($hargaTerima),
                $hargaSetelahTerima === $hargaTerima && $lh !== null
                    && (int) $lh->harga_lama === $hargaAwal && (int) $lh->harga_baru === $hargaTerima
            );

            DB::statement('CALL sp_selesaikan_pengadaan(?)', [$idpengadaan]);
            $statusP = $this->nilai('SELECT status FROM pengadaan WHERE idpengadaan = ?', [$idpengadaan]);
            $tambah('6. Pengadaan yang sudah diterima menjadi Selesai', 'sp_selesaikan_pengadaan',
                "status pengadaan = $statusP", 'status pengadaan = S', $statusP === 'S');

            // ===== 7. HANYA 1 MARGIN AKTIF =====
            $lain = $this->nilai('SELECT MIN(idmargin_penjualan) FROM margin_penjualan WHERE idmargin_penjualan <> ?', [$idmargin]);
            $target = $lain ?: $idmargin;
            DB::statement('CALL sp_aktifkan_margin(?)', [$target]);
            $jmlAktif = (int) $this->nilai('SELECT COUNT(*) FROM margin_penjualan WHERE status = 1');
            $aktifId  = (int) $this->nilai('SELECT fn_get_margin_aktif()');
            $tambah(
                "7. Aktifkan margin #$target: margin lain otomatis nonaktif",
                'sp_aktifkan_margin + fn_get_margin_aktif',
                "margin aktif = $jmlAktif, id aktif = $aktifId",
                "margin aktif = 1, id aktif = $target",
                $jmlAktif === 1 && $aktifId === (int) $target
            );
            DB::statement('CALL sp_aktifkan_margin(?)', [$idmargin]);   // kembalikan untuk langkah berikutnya

            // ===== 8-10. PENJUALAN =====
            DB::insert('INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
                        VALUES (0, 0, 0, ?, fn_get_margin_aktif())', [$iduser]);
            $idpenjualan = DB::getPdo()->lastInsertId();

            DB::insert('INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
                        VALUES (0, 4, 0, ?, ?)', [$idpenjualan, $idbarang]);

            $dj = DB::selectOne('SELECT * FROM detail_penjualan WHERE penjualan_idpenjualan = ?', [$idpenjualan]);
            $hj = DB::selectOne('SELECT * FROM penjualan WHERE idpenjualan = ?', [$idpenjualan]);
            $hargaFn  = (int) $this->nilai('SELECT fn_hitung_harga_jual(?, ?)', [$idbarang, $idmargin]);
            $subtotal = $hargaFn * 4;
            $ppn      = intdiv($subtotal * 11 + 50, 100);

            $tambah(
                '8. Penjualan: margin aktif terisi otomatis, harga & subtotal dari function',
                'fn_get_margin_aktif + trg_before_detail_penjualan + fn_hitung_harga_jual + fn_hitung_subtotal',
                'margin #' . $hj->idmargin_penjualan . ', harga_satuan ' . $rp($dj->harga_satuan) . ', subtotal ' . $rp($dj->subtotal),
                'margin #' . $idmargin . ', harga_satuan ' . $rp($hargaFn) . ', subtotal ' . $rp($subtotal),
                (int) $hj->idmargin_penjualan === (int) $idmargin && (int) $dj->harga_satuan === $hargaFn && (int) $dj->subtotal === $subtotal
            );
            $tambah(
                '9. Header penjualan dihitung ulang',
                'trg_after_detail_penjualan + sp_update_total_penjualan',
                'subtotal ' . $rp($hj->subtotal_nilai) . ', ppn ' . $rp($hj->ppn) . ', total ' . $rp($hj->total_nilai),
                'subtotal ' . $rp($subtotal) . ', ppn ' . $rp($ppn) . ', total ' . $rp($subtotal + $ppn),
                (int) $hj->subtotal_nilai === $subtotal && (int) $hj->ppn === $ppn && (int) $hj->total_nilai === $subtotal + $ppn
            );

            $stokJual = $stok();
            $kartuK = DB::selectOne('SELECT * FROM kartu_stok WHERE idbarang = ? ORDER BY idkartu_stok DESC LIMIT 1', [$idbarang]);
            $tambah(
                '10. Stok keluar dari penjualan 4 pcs',
                'trg_after_detail_penjualan',
                "stok $stokTerima menjadi $stokJual, jenis {$kartuK->jenis_transaksi}, keluar {$kartuK->keluar}",
                'stok ' . $stokTerima . ' menjadi ' . ($stokTerima - 4) . ', jenis K, keluar 4',
                $stokJual === $stokTerima - 4 && $kartuK->jenis_transaksi === 'K' && (int) $kartuK->keluar === 4
            );

            // ===== 11. RETUR =====
            DB::insert('INSERT INTO retur (created_at, idpenerimaan, iduser) VALUES (NOW(), ?, ?)', [$idpenerimaan, $iduser]);
            $idretur = DB::getPdo()->lastInsertId();
            DB::insert("INSERT INTO detail_retur (jumlah, alasan, idretur, iddetail_penerimaan) VALUES (2, 'Uji trigger', ?, ?)",
                [$idretur, $iddetailterima]);

            $stokRetur = $stok();
            $kartuR = DB::selectOne('SELECT * FROM kartu_stok WHERE idbarang = ? ORDER BY idkartu_stok DESC LIMIT 1', [$idbarang]);
            $tambah(
                '11. Retur 2 pcs ke vendor',
                'trg_kartu_stok_retur',
                "stok $stokJual menjadi $stokRetur, jenis {$kartuR->jenis_transaksi}",
                'stok ' . $stokJual . ' menjadi ' . ($stokJual - 2) . ', jenis R',
                $stokRetur === $stokJual - 2 && $kartuR->jenis_transaksi === 'R'
            );

            // ===== 12. NONAKTIFKAN BARANG =====
            $statusAwal = (int) $this->nilai('SELECT status FROM barang WHERE idbarang = ?', [$idbarang]);
            DB::statement('CALL sp_toggle_status_barang(?)', [$idbarang]);
            $statusBaru = (int) $this->nilai('SELECT status FROM barang WHERE idbarang = ?', [$idbarang]);
            $logToggle = DB::selectOne("SELECT * FROM log_aktivitas WHERE idlog > ? AND nama_tabel = 'barang' ORDER BY idlog DESC LIMIT 1", [$maxLogAkt]);
            $tambah(
                '12. Barang dinonaktifkan lewat tombol toggle',
                'sp_toggle_status_barang + trg_log_barang_update',
                "status $statusAwal menjadi $statusBaru" . ($logToggle ? ', tercatat di log_aktivitas' : ', tidak tercatat di log'),
                'status ' . $statusAwal . ' menjadi ' . (1 - $statusAwal) . ', tercatat di log_aktivitas',
                $statusBaru === 1 - $statusAwal && $logToggle !== null
            );
            DB::statement('CALL sp_toggle_status_barang(?)', [$idbarang]);   // kembalikan

            // ===== 13. LOG PERUBAHAN HARGA MANUAL =====
            $hargaSebelum = $harga();
            DB::update('UPDATE barang SET harga = ? WHERE idbarang = ?', [$hargaSebelum + 1000, $idbarang]);
            $lh2 = DB::selectOne('SELECT * FROM log_perubahan_harga WHERE idlog > ? ORDER BY idlog DESC LIMIT 1', [$maxLogHarga]);
            $tambah(
                '13. Harga barang diubah manual +Rp 1.000',
                'trg_log_update_harga_barang + trg_log_barang_update',
                $lh2 ? 'harga_lama ' . $rp($lh2->harga_lama) . ', harga_baru ' . $rp($lh2->harga_baru) : 'tidak ada baris log',
                'harga_lama ' . $rp($hargaSebelum) . ', harga_baru ' . $rp($hargaSebelum + 1000),
                $lh2 !== null && (int) $lh2->harga_lama === $hargaSebelum && (int) $lh2->harga_baru === $hargaSebelum + 1000
            );

            // Ambil jejak yang dibuat trigger SEBELUM rollback
            $kartuBaru = DB::select('SELECT * FROM kartu_stok WHERE idkartu_stok > ? ORDER BY idkartu_stok', [$maxKartu]);
            $logBaru   = DB::select('SELECT * FROM log_aktivitas WHERE idlog > ? ORDER BY idlog', [$maxLogAkt]);
        } catch (QueryException $e) {
            $tambah('Error database', '-', $e->errorInfo[2] ?? $e->getMessage(), 'tidak ada error', false);
        } catch (\Throwable $e) {
            $tambah('Error tak terduga', '-', $e->getMessage(), 'tidak ada error', false);
        } finally {
            DB::rollBack();
        }

        // ===== 14. PASTIKAN ROLLBACK BERHASIL =====
        if ($stokAwal !== null) {
            $sisaKartu = (int) $this->nilai('SELECT COUNT(*) FROM kartu_stok WHERE idkartu_stok > ?', [$maxKartu]);
            $stokAkhir = $stok();
            $hargaAkhir = $harga();
            $margAktif = (int) $this->nilai('SELECT fn_get_margin_aktif()');
            $tambah(
                '14. Rollback: semua data uji dibatalkan',
                '-',
                "stok $stokAkhir, harga " . $rp($hargaAkhir) . ", margin aktif #$margAktif, kartu stok uji tersisa $sisaKartu",
                "stok $stokAwal, harga " . $rp($hargaAwal) . ", margin aktif #$idmargin, tidak ada baris tersisa",
                $stokAkhir === $stokAwal && $hargaAkhir === $hargaAwal && $margAktif === (int) $idmargin && $sisaKartu === 0
            );
        }

        return [$hasil, $kartuBaru, $logBaru];
    }
}