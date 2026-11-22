<?php

namespace App\Http\Controllers;

use App\Support\AksesKasir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

/**
 * Status penjualan (kolom status_bayar):
 *   D = Draft / belum dibayar  -> boleh diubah & dihapus
 *   B = Dibayar                -> terkunci
 * Kasir hanya bisa melihat & mengubah transaksi MILIKNYA sendiri.
 * Manager (izin 'penjualan.lihat_semua' lewat '*') bisa melihat semua.
 */
class PenjualanController extends Controller
{
    /** Ambil nota + pastikan pemiliknya benar. 404 jika tidak ada, 403 jika milik orang lain. */
    private function ambilNota($id): object
    {
        $rows = DB::select("SELECT * FROM view_nota_penjualan WHERE idpenjualan = ?", [$id]);
        if (empty($rows)) {
            abort(404);
        }
        $p = $rows[0];

        if (!AksesKasir::bisa('penjualan.lihat_semua') && (int) $p->iduser !== (int) session('iduser')) {
            abort(403, 'Anda hanya dapat mengakses transaksi penjualan milik akun Anda sendiri.');
        }
        return $p;
    }

    private function tolakJikaSudahDibayar(object $p, string $aksi)
    {
        if ($p->status_bayar !== 'D') {
            return redirect('/transaksi/penjualan/' . $p->idpenjualan)
                ->with('error', "Transaksi sudah dibayar, tidak dapat $aksi.");
        }
        return null;
    }

    public function index()
    {
        if (AksesKasir::bisa('penjualan.lihat_semua')) {
            $penjualans = DB::select("SELECT * FROM view_laporan_penjualan_kasir ORDER BY idpenjualan DESC");
        } else {
            $penjualans = DB::select(
                "SELECT * FROM view_laporan_penjualan_kasir WHERE iduser = ? ORDER BY idpenjualan DESC",
                [session('iduser')]
            );
        }
        return view('transaksi.penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $barangs = DB::select("SELECT * FROM view_stok_barang WHERE status_barang = 1 ORDER BY nama_barang");

        $margin = DB::select("SELECT idmargin_penjualan, persen FROM margin_penjualan WHERE status = 1 LIMIT 1");
        $margin = $margin[0] ?? null;

        return view('transaksi.penjualan.create', compact('barangs', 'margin'));
    }

    /** Transaksi baru selalu berstatus DRAFT (D). Stok langsung dicadangkan oleh trigger. */
    public function store(Request $request)
    {
        $request->validate([
            'idbarang'   => 'required|array|min:1',
            'idbarang.*' => 'required|integer',
            'jumlah'     => 'required|array',
            'jumlah.*'   => 'required|integer|min:1',
        ]);

        $iduser   = session('iduser');
        $idmargin = DB::select("SELECT fn_get_margin_aktif() AS idmargin")[0]->idmargin;
        if (!$idmargin) {
            return back()->withInput()->with('error', 'Belum ada margin aktif. Hubungi manager.');
        }

        $items = [];
        foreach ($request->idbarang as $i => $idbarang) {
            $items[$idbarang] = ($items[$idbarang] ?? 0) + (int) $request->jumlah[$i];
        }

        foreach ($items as $idbarang => $jumlah) {
            $row = DB::select("SELECT nama_barang, sisa_stok, status_barang FROM view_stok_barang WHERE idbarang = ?", [$idbarang]);
            if (empty($row) || $row[0]->status_barang != 1) {
                return back()->withInput()->with('error', 'Ada barang yang tidak aktif / tidak ditemukan.');
            }
            if ($row[0]->sisa_stok < $jumlah) {
                return back()->withInput()->with('error', 'Stok ' . $row[0]->nama_barang . ' tidak cukup (sisa ' . $row[0]->sisa_stok . ').');
            }
        }

        try {
            DB::insert("
                INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, status_bayar, iduser, idmargin_penjualan)
                VALUES (0, 0, 0, 'D', ?, ?)
            ", [$iduser, $idmargin]);
            $idpenjualan = DB::getPdo()->lastInsertId();

            foreach ($items as $idbarang => $jumlah) {
                DB::insert("
                    INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
                    VALUES (0, ?, 0, ?, ?)
                ", [$jumlah, $idpenjualan, $idbarang]);
            }
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/penjualan/' . $idpenjualan)
            ->with('success', 'Draft transaksi dibuat. Periksa isinya, lalu klik Bayar untuk menyelesaikan.');
    }

    public function show($id)
    {
        $penjualan = $this->ambilNota($id);
        $details   = DB::select("SELECT * FROM view_detail_penjualan WHERE idpenjualan = ? ORDER BY nama_barang", [$id]);

        return view('transaksi.penjualan.show', compact('penjualan', 'details'));
    }

    /** UPDATE bersyarat: hanya milik sendiri DAN masih draft */
    public function edit($id)
    {
        $penjualan = $this->ambilNota($id);
        if ($tolak = $this->tolakJikaSudahDibayar($penjualan, 'diubah')) {
            return $tolak;
        }

        $details = DB::select("
            SELECT d.*, s.sisa_stok
            FROM view_detail_penjualan d
            JOIN view_stok_barang s ON s.idbarang = d.idbarang
            WHERE d.idpenjualan = ?
            ORDER BY d.nama_barang
        ", [$id]);

        return view('transaksi.penjualan.edit', compact('penjualan', 'details'));
    }

    public function update(Request $request, $id)
    {
        $penjualan = $this->ambilNota($id);
        if ($tolak = $this->tolakJikaSudahDibayar($penjualan, 'diubah')) {
            return $tolak;
        }

        $request->validate([
            'idbarang'   => 'required|array|min:1',
            'idbarang.*' => 'required|integer',
            'jumlah'     => 'required|array',
            'jumlah.*'   => 'required|integer|min:0',
        ]);

        // jumlah lama per barang
        $lama = [];
        foreach (DB::select("SELECT idbarang, jumlah FROM view_detail_penjualan WHERE idpenjualan = ?", [$id]) as $d) {
            $lama[$d->idbarang] = (int) $d->jumlah;
        }

        $baru = [];
        foreach ($request->idbarang as $i => $idbarang) {
            if (isset($lama[$idbarang])) {
                $baru[$idbarang] = (int) $request->jumlah[$i];
            }
        }

        if (count(array_filter($baru, fn ($j) => $j > 0)) === 0) {
            return back()->with('error', 'Minimal 1 barang harus tersisa. Untuk membatalkan seluruh transaksi, gunakan tombol Hapus.');
        }

        // Jika jumlah dinaikkan, stok harus cukup untuk selisihnya
        foreach ($baru as $idbarang => $jml) {
            $selisih = $jml - $lama[$idbarang];
            if ($selisih > 0) {
                $row = DB::select("SELECT nama_barang, sisa_stok FROM view_stok_barang WHERE idbarang = ?", [$idbarang]);
                if (empty($row) || $row[0]->sisa_stok < $selisih) {
                    return back()->with('error', 'Stok ' . ($row[0]->nama_barang ?? 'barang') . ' tidak cukup untuk menambah jumlah.');
                }
            }
        }

        try {
            foreach ($baru as $idbarang => $jml) {
                if ($jml === $lama[$idbarang]) {
                    continue;
                }
                if ($jml === 0) {
                    // Trigger mengembalikan stok & menghitung ulang total
                    DB::delete("DELETE FROM detail_penjualan WHERE penjualan_idpenjualan = ? AND idbarang = ?", [$id, $idbarang]);
                } else {
                    // Trigger menghitung ulang subtotal, total header, dan selisih kartu stok
                    DB::update("UPDATE detail_penjualan SET jumlah = ? WHERE penjualan_idpenjualan = ? AND idbarang = ?", [$jml, $id, $idbarang]);
                }
            }
        } catch (QueryException $e) {
            return back()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/penjualan/' . $id)->with('success', 'Draft transaksi berhasil diperbarui.');
    }

    /** Proses pembayaran: draft -> dibayar (setelah ini terkunci) */
    public function bayar($id)
    {
        $penjualan = $this->ambilNota($id);
        if ($tolak = $this->tolakJikaSudahDibayar($penjualan, 'dibayar ulang')) {
            return $tolak;
        }

        DB::update("UPDATE penjualan SET status_bayar = 'B', dibayar_at = NOW() WHERE idpenjualan = ? AND status_bayar = 'D'", [$id]);

        return redirect('/transaksi/penjualan/' . $id)->with('success', 'Pembayaran diproses. Transaksi selesai dan tidak dapat diubah lagi.');
    }

    /** DELETE bersyarat: hanya milik sendiri DAN pembayaran belum diproses */
    public function destroy($id)
    {
        $penjualan = $this->ambilNota($id);
        if ($tolak = $this->tolakJikaSudahDibayar($penjualan, 'dihapus')) {
            return $tolak;
        }

        try {
            // Hapus detail dulu: trigger mengembalikan stok ke kartu stok
            DB::delete("DELETE FROM detail_penjualan WHERE penjualan_idpenjualan = ?", [$id]);
            DB::delete("DELETE FROM penjualan WHERE idpenjualan = ? AND status_bayar = 'D'", [$id]);
        } catch (QueryException $e) {
            return back()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/penjualan')->with('success', 'Draft transaksi dihapus dan stok dikembalikan.');
    }

    /** Stored Procedure sp_laporan_penjualan_berdasarkan_user (khusus manager) */
    public function laporan(Request $request)
    {
        $users  = DB::select("SELECT iduser, username FROM `user` ORDER BY iduser");
        $iduser = $request->query('iduser');

        $data = $iduser
            ? DB::select('CALL sp_laporan_penjualan_berdasarkan_user(?)', [$iduser])
            : [];

        return view('sp_function.laporan', compact('users', 'iduser', 'data'));
    }
}