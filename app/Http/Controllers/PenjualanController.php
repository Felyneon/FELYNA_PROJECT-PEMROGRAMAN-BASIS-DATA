<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PenjualanController extends Controller
{
    public function index()
    {
        $penjualans = DB::select("SELECT * FROM view_laporan_penjualan_kasir ORDER BY idpenjualan DESC");
        return view('transaksi.penjualan.index', compact('penjualans'));
    }

    public function create()
    {
        $barangs = DB::select("SELECT * FROM view_stok_barang WHERE status_barang = 1 ORDER BY nama_barang");

        // Hanya 1 margin aktif; dipakai untuk menampilkan estimasi harga di form
        $margin = DB::select("SELECT idmargin_penjualan, persen FROM margin_penjualan WHERE status = 1 LIMIT 1");
        $margin = $margin[0] ?? null;

        return view('transaksi.penjualan.create', compact('barangs', 'margin'));
    }

    /**
     * id margin diambil otomatis oleh function fn_get_margin_aktif().
     * harga_satuan & subtotal detail dihitung trigger (fn_hitung_harga_jual, fn_hitung_subtotal),
     * header ikut terisi lewat sp_update_total_penjualan, kartu stok keluar oleh trigger.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idbarang'   => 'required|array|min:1',
            'idbarang.*' => 'required|integer',
            'jumlah'     => 'required|array',
            'jumlah.*'   => 'required|integer|min:1',
        ]);

        $iduser = session('iduser') ?? 1;

        $idmargin = DB::select("SELECT fn_get_margin_aktif() AS idmargin")[0]->idmargin;
        if (!$idmargin) {
            return back()->withInput()->with('error', 'Belum ada margin aktif. Aktifkan satu margin di menu Kelola Margin.');
        }

        // Gabungkan baris barang yang sama
        $items = [];
        foreach ($request->idbarang as $i => $idbarang) {
            $items[$idbarang] = ($items[$idbarang] ?? 0) + (int) $request->jumlah[$i];
        }

        // Pengaman sisi aplikasi (bukan di database): stok cukup & barang aktif.
        // Validasi utama ada di form (atribut max), ini hanya jaga-jaga request manipulasi.
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
                INSERT INTO penjualan (subtotal_nilai, ppn, total_nilai, iduser, idmargin_penjualan)
                VALUES (0, 0, 0, ?, ?)
            ", [$iduser, $idmargin]);
            $idpenjualan = DB::getPdo()->lastInsertId();

            foreach ($items as $idbarang => $jumlah) {
                // harga_satuan & subtotal 0: diisi trigger lewat function
                DB::insert("
                    INSERT INTO detail_penjualan (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, idbarang)
                    VALUES (0, ?, 0, ?, ?)
                ", [$jumlah, $idpenjualan, $idbarang]);
            }
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/penjualan/' . $idpenjualan)->with('success', 'Transaksi berhasil. Berikut struknya.');
    }

    /** Struk penjualan */
    public function show($id)
    {
        $rows = DB::select("SELECT * FROM view_nota_penjualan WHERE idpenjualan = ?", [$id]);
        if (empty($rows)) {
            abort(404);
        }
        $penjualan = $rows[0];

        $details = DB::select("SELECT * FROM view_detail_penjualan WHERE idpenjualan = ? ORDER BY nama_barang", [$id]);

        return view('transaksi.penjualan.show', compact('penjualan', 'details'));
    }

    /** Stored Procedure sp_laporan_penjualan_berdasarkan_user */
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