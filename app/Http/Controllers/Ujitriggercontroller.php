<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;

class UjiTriggerController extends Controller
{
    public function index(Request $request)
    {
        $db = DB::connection()->getDatabaseName();

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
        $target = ['PROCEDURE' => 4, 'FUNCTION' => 4, 'TRIGGER' => 22];

        // ---------- Uji otomatis trigger (hanya saat tombol ditekan / POST) ----------
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
                $pesanSiap = 'Uji dibatalkan: belum ada trigger di database. Jalankan TRIGGER_DB_PROJECT_FIX.sql dulu.';
            } elseif (!Schema::hasTable('log_aktivitas') || !Schema::hasTable('log_perubahan_harga')) {
                $pesanSiap = 'Uji dibatalkan: tabel log_aktivitas / log_perubahan_harga belum ada.';
            } else {
                [$hasilUji, $kartuBaru, $logBaru] = $this->jalankanUji();
            }
        }

        // ---------- Data terbaru (untuk memeriksa hasil transaksi nyata dari web) ----------
        $kartuTerakhir = DB::table('kartu_stok as k')
            ->join('barang as b', 'b.idbarang', '=', 'k.idbarang')
            ->select('k.idkartu_stok', 'b.nama', 'k.jenis_transaksi', 'k.masuk', 'k.keluar',
                     'k.stock', 'k.idtransaksi', 'k.created_at')
            ->orderByDesc('k.idkartu_stok')
            ->limit(10)
            ->get();

        $logAktivitas = Schema::hasTable('log_aktivitas')
            ? DB::table('log_aktivitas')->orderByDesc('idlog')->limit(10)->get()
            : collect();
        $logHarga = Schema::hasTable('log_perubahan_harga')
            ? DB::table('log_perubahan_harga')->orderByDesc('idlog')->limit(10)->get()
            : collect();

        return view('trigger.uji_trigger', compact(
            'routines', 'triggers', 'jumlah', 'target',
            'hasilUji', 'kartuBaru', 'logBaru', 'pesanSiap',
            'kartuTerakhir', 'logAktivitas', 'logHarga'
            // return view('trigger.uji_trigger', compact('rutinitas', 'triggers')); 
// Sesuaikan variabel data yang ingin kamu kirim ke view sp_function.uji_trigger
        ));
    }

    /**
     * Menjalankan rangkaian transaksi uji di dalam transaction, lalu ROLLBACK.
     * Tidak ada data yang tersisa di database setelah uji selesai.
     */
    private function jalankanUji(): array
    {
        $hasil = [];
        $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');
        $tambah = function (string $langkah, string $trigger, string $aktual, string $harapan, bool $lulus) use (&$hasil) {
            $hasil[] = compact('langkah', 'trigger', 'aktual', 'harapan', 'lulus');
        };

        $iduser   = DB::table('user')->min('iduser');
        $idvendor = DB::table('vendor')->min('idvendor');
        $idbarang = DB::table('barang')->min('idbarang');
        $idmargin = DB::table('margin_penjualan')->where('status', 1)->min('idmargin_penjualan');

        if (!$iduser || !$idvendor || !$idbarang || !$idmargin) {
            $tambah('Persiapan', '-', 'Data master (user, vendor, barang, margin aktif) belum lengkap', 'data master terisi', false);
            return [$hasil, [], []];
        }

        $maxKartu    = (int) DB::table('kartu_stok')->max('idkartu_stok');
        $maxLogAkt   = (int) DB::table('log_aktivitas')->max('idlog');
        $maxLogHarga = (int) DB::table('log_perubahan_harga')->max('idlog');

        $stok = fn () => (int) DB::selectOne('SELECT fn_get_sisa_stok(?) AS s', [$idbarang])->s;

        $stokAwal  = null;
        $kartuBaru = [];
        $logBaru   = [];

        DB::beginTransaction();
        try {
            $stokAwal = $stok();

            // ===== 1-2. PENGADAAN =====
            $idpengadaan = DB::table('pengadaan')->insertGetId([
                'user_iduser' => $iduser, 'status' => 'P', 'vendor_idvendor' => $idvendor,
                'subtotal_nilai' => 0, 'ppn' => 0, 'total_nilai' => 0,
            ]);
            DB::table('detail_pengadaan')->insert([
                'harga_satuan' => 100000, 'jumlah' => 10, 'sub_total' => 0,
                'idbarang' => $idbarang, 'idpengadaan' => $idpengadaan,
            ]);
            $dp = DB::table('detail_pengadaan')->where('idpengadaan', $idpengadaan)->first();
            $hd = DB::table('pengadaan')->where('idpengadaan', $idpengadaan)->first();

            $tambah(
                '1. Detail pengadaan 10 x Rp 100.000 (sub_total diisi 0)',
                'trg_calc_sub_detail_pengadaan',
                'sub_total = ' . $rp($dp->sub_total),
                'sub_total = ' . $rp(1000000),
                (int) $dp->sub_total === 1000000
            );
            $tambah(
                '2. Header pengadaan dihitung ulang',
                'trg_update_header_pengadaan',
                'subtotal ' . $rp($hd->subtotal_nilai) . ', ppn ' . $rp($hd->ppn) . ', total ' . $rp($hd->total_nilai),
                'subtotal ' . $rp(1000000) . ', ppn ' . $rp(110000) . ', total ' . $rp(1110000),
                (int) $hd->subtotal_nilai === 1000000 && (int) $hd->ppn === 110000 && (int) $hd->total_nilai === 1110000
            );

            // ===== 3-4. PENERIMAAN =====
            $idpenerimaan = DB::table('penerimaan')->insertGetId([
                'status' => 'S', 'idpengadaan' => $idpengadaan, 'iduser' => $iduser,
            ]);
            DB::table('detail_penerimaan')->insert([
                'idpenerimaan' => $idpenerimaan, 'barang_idbarang' => $idbarang,
                'jumlah_terima' => 10, 'harga_satuan_terima' => 100000, 'sub_total_terima' => 0,
            ]);
            $dt = DB::table('detail_penerimaan')->where('idpenerimaan', $idpenerimaan)->first();
            $iddetailterima = $dt->iddetail_penerimaan;

            $tambah(
                '3. Detail penerimaan 10 pcs (sub_total_terima diisi 0)',
                'trg_calc_sub_detail_penerimaan',
                'sub_total_terima = ' . $rp($dt->sub_total_terima),
                'sub_total_terima = ' . $rp(1000000),
                (int) $dt->sub_total_terima === 1000000
            );

            $stokTerima = $stok();
            $kartuM = DB::table('kartu_stok')->where('idbarang', $idbarang)->orderByDesc('idkartu_stok')->first();
            $tambah(
                '4. Stok masuk dari penerimaan 10 pcs',
                'trg_kartu_stok_penerimaan',
                "stok $stokAwal menjadi $stokTerima, jenis kartu stok {$kartuM->jenis_transaksi}",
                'stok ' . $stokAwal . ' menjadi ' . ($stokAwal + 10) . ', jenis M',
                $stokTerima === $stokAwal + 10 && $kartuM->jenis_transaksi === 'M'
            );

            // ===== 5-7. PENJUALAN =====
            $idpenjualan = DB::table('penjualan')->insertGetId([
                'subtotal_nilai' => 0, 'ppn' => 0, 'total_nilai' => 0,
                'iduser' => $iduser, 'idmargin_penjualan' => $idmargin,
            ]);
            DB::table('detail_penjualan')->insert([
                'harga_satuan' => 0, 'jumlah' => 4, 'subtotal' => 0,
                'penjualan_idpenjualan' => $idpenjualan, 'idbarang' => $idbarang,
            ]);
            $dj = DB::table('detail_penjualan')->where('penjualan_idpenjualan', $idpenjualan)->first();
            $hargaFn  = (int) DB::selectOne('SELECT fn_hitung_harga_jual(?, ?) AS h', [$idbarang, $idmargin])->h;
            $subtotal = $hargaFn * 4;
            $ppn      = intdiv($subtotal * 11 + 50, 100);   // 11% dibulatkan seperti ROUND() MySQL
            $hj = DB::table('penjualan')->where('idpenjualan', $idpenjualan)->first();

            $tambah(
                '5. Detail penjualan 4 pcs (harga_satuan diisi 0)',
                'trg_before_detail_penjualan + fn_hitung_harga_jual',
                'harga_satuan ' . $rp($dj->harga_satuan) . ', subtotal ' . $rp($dj->subtotal),
                'harga_satuan ' . $rp($hargaFn) . ', subtotal ' . $rp($subtotal),
                (int) $dj->harga_satuan === $hargaFn && (int) $dj->subtotal === $subtotal
            );
            $tambah(
                '6. Header penjualan dihitung ulang',
                'trg_after_detail_penjualan',
                'subtotal ' . $rp($hj->subtotal_nilai) . ', ppn ' . $rp($hj->ppn) . ', total ' . $rp($hj->total_nilai),
                'subtotal ' . $rp($subtotal) . ', ppn ' . $rp($ppn) . ', total ' . $rp($subtotal + $ppn),
                (int) $hj->subtotal_nilai === $subtotal && (int) $hj->ppn === $ppn && (int) $hj->total_nilai === $subtotal + $ppn
            );

            $stokJual = $stok();
            $kartuK = DB::table('kartu_stok')->where('idbarang', $idbarang)->orderByDesc('idkartu_stok')->first();
            $tambah(
                '7. Stok keluar dari penjualan 4 pcs',
                'trg_after_detail_penjualan',
                "stok $stokTerima menjadi $stokJual, jenis {$kartuK->jenis_transaksi}, keluar {$kartuK->keluar}",
                'stok ' . $stokTerima . ' menjadi ' . ($stokTerima - 4) . ', jenis K, keluar 4',
                $stokJual === $stokTerima - 4 && $kartuK->jenis_transaksi === 'K' && (int) $kartuK->keluar === 4
            );

            // ===== 8. RETUR =====
            $idretur = DB::table('retur')->insertGetId([
                'created_at' => now(), 'idpenerimaan' => $idpenerimaan, 'iduser' => $iduser,
            ]);
            DB::table('detail_retur')->insert([
                'jumlah' => 2, 'alasan' => 'Uji trigger',
                'idretur' => $idretur, 'iddetail_penerimaan' => $iddetailterima,
            ]);
            $stokRetur = $stok();
            $kartuR = DB::table('kartu_stok')->where('idbarang', $idbarang)->orderByDesc('idkartu_stok')->first();
            $tambah(
                '8. Retur 2 pcs ke vendor',
                'trg_validate_detail_retur + trg_kartu_stok_retur',
                "stok $stokJual menjadi $stokRetur, jenis {$kartuR->jenis_transaksi}",
                'stok ' . $stokJual . ' menjadi ' . ($stokJual - 2) . ', jenis R',
                $stokRetur === $stokJual - 2 && $kartuR->jenis_transaksi === 'R'
            );

            // ===== 9. VALIDASI RETUR BERLEBIH (harus ditolak) =====
            try {
                DB::table('detail_retur')->insert([
                    'jumlah' => 999, 'alasan' => 'Uji tolak',
                    'idretur' => $idretur, 'iddetail_penerimaan' => $iddetailterima,
                ]);
                $tambah('9. Retur 999 pcs (melebihi jumlah diterima)', 'trg_validate_detail_retur (SIGNAL)',
                    'TIDAK ditolak', 'ditolak dengan pesan error', false);
            } catch (QueryException $e) {
                $pesan = $e->errorInfo[2] ?? $e->getMessage();
                $tambah('9. Retur 999 pcs (melebihi jumlah diterima)', 'trg_validate_detail_retur (SIGNAL)',
                    'ditolak: ' . $pesan, 'ditolak: "Gagal Retur: ..."', str_contains($pesan, 'Gagal Retur'));
            }

            // ===== 10. VALIDASI STOK PENJUALAN (harus ditolak) =====
            try {
                DB::table('detail_penjualan')->insert([
                    'harga_satuan' => 0, 'jumlah' => 999999, 'subtotal' => 0,
                    'penjualan_idpenjualan' => $idpenjualan, 'idbarang' => $idbarang,
                ]);
                $tambah('10. Jual 999.999 pcs (melebihi stok)', 'trg_before_detail_penjualan (SIGNAL)',
                    'TIDAK ditolak', 'ditolak dengan pesan error', false);
            } catch (QueryException $e) {
                $pesan = $e->errorInfo[2] ?? $e->getMessage();
                $tambah('10. Jual 999.999 pcs (melebihi stok)', 'trg_before_detail_penjualan (SIGNAL)',
                    'ditolak: ' . $pesan, 'ditolak: "Gagal Jual: stok tidak mencukupi..."', str_contains($pesan, 'Gagal Jual'));
            }

            // ===== 11. LOG PERUBAHAN HARGA =====
            $hargaLama = (int) DB::table('barang')->where('idbarang', $idbarang)->value('harga');
            DB::table('barang')->where('idbarang', $idbarang)->update(['harga' => $hargaLama + 1000]);
            $lh = DB::table('log_perubahan_harga')->where('idlog', '>', $maxLogHarga)->first();
            $tambah(
                '11. Harga barang dinaikkan Rp 1.000',
                'trg_log_update_harga_barang + trg_log_barang_update',
                $lh ? 'harga_lama ' . $rp($lh->harga_lama) . ', harga_baru ' . $rp($lh->harga_baru) : 'tidak ada baris log',
                'harga_lama ' . $rp($hargaLama) . ', harga_baru ' . $rp($hargaLama + 1000),
                $lh !== null && (int) $lh->harga_lama === $hargaLama && (int) $lh->harga_baru === $hargaLama + 1000
            );

            // Ambil jejak yang dibuat trigger SEBELUM rollback
            $kartuBaru = DB::table('kartu_stok')->where('idkartu_stok', '>', $maxKartu)
                ->orderBy('idkartu_stok')->get()->all();
            $logBaru = DB::table('log_aktivitas')->where('idlog', '>', $maxLogAkt)
                ->orderBy('idlog')->get()->all();
        } catch (\Throwable $e) {
            $tambah('Error tak terduga', '-', $e->getMessage(), 'tidak ada error', false);
        } finally {
            DB::rollBack();
        }

        // ===== 12. PASTIKAN ROLLBACK BERHASIL =====
        if ($stokAwal !== null) {
            $sisaKartu = DB::table('kartu_stok')->where('idkartu_stok', '>', $maxKartu)->count();
            $stokAkhir = $stok();
            $tambah(
                '12. Rollback: semua data uji dibatalkan',
                '-',
                "stok $stokAkhir, baris kartu stok uji tersisa $sisaKartu",
                "stok $stokAwal, tidak ada baris tersisa",
                $stokAkhir === $stokAwal && $sisaKartu === 0
            );
        }

        return [$hasil, $kartuBaru, $logBaru];
    }
}