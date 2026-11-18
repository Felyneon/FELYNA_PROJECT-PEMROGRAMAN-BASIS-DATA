<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PengadaanController extends Controller
{
    /** Daftar pengadaan (view_daftar_pengadaan) */
    public function index()
    {
        $pengadaans = DB::select("SELECT * FROM view_daftar_pengadaan ORDER BY idpengadaan DESC");
        return view('transaksi.pengadaan.index', compact('pengadaans'));
    }

    /** Form: hanya vendor yang dipilih (dropdown); barang dipilih di baris detail */
    public function create()
    {
        $vendors = DB::select("SELECT *, fn_status_badan_hukum_vendor(idvendor) AS keterangan_badan_hukum FROM vendor ORDER BY nama_vendor");
        $barangs = DB::select("SELECT * FROM view_stok_barang WHERE status_barang = 1 ORDER BY nama_barang");

        return view('transaksi.pengadaan.create', compact('vendors', 'barangs'));
    }

    /**
     * Header pengadaan dibuat dengan nilai 0. Harga detail diambil dari harga barang
     * oleh trigger trg_calc_sub_detail_pengadaan, lalu trg_update_header_pengadaan
     * (CALL sp_update_total_pengadaan) mengisi subtotal, PPN, dan total header.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vendor_idvendor' => 'required',
            'idbarang'        => 'required|array|min:1',
            'idbarang.*'      => 'required|integer',
            'jumlah'          => 'required|array',
            'jumlah.*'        => 'required|integer|min:1',
        ]);

        $iduser = session('iduser') ?? 1;

        // Gabungkan baris yang memilih barang sama
        $items = [];
        foreach ($request->idbarang as $i => $idbarang) {
            $items[$idbarang] = ($items[$idbarang] ?? 0) + (int) $request->jumlah[$i];
        }

        try {
            DB::insert("
                INSERT INTO pengadaan (`timestamp`, user_iduser, status, vendor_idvendor, subtotal_nilai, ppn, total_nilai)
                VALUES (NOW(), ?, 'P', ?, 0, 0, 0)
            ", [$iduser, $request->vendor_idvendor]);

            $idpengadaan = DB::getPdo()->lastInsertId();

            foreach ($items as $idbarang => $jumlah) {
                // harga_satuan & sub_total sengaja 0: diisi trigger
                DB::insert("
                    INSERT INTO detail_pengadaan (harga_satuan, jumlah, sub_total, idbarang, idpengadaan)
                    VALUES (0, ?, 0, ?, ?)
                ", [$jumlah, $idbarang, $idpengadaan]);
            }
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/pengadaan/' . $idpengadaan)->with('success', 'Pengadaan berhasil dibuat. Berikut notanya.');
    }

    /** Nota pengadaan: siapa, kapan, vendor, barang apa, qty, harga, subtotal */
    public function show($id)
    {
        $rows = DB::select("SELECT * FROM view_nota_pengadaan WHERE idpengadaan = ?", [$id]);
        if (empty($rows)) {
            abort(404);
        }
        $pengadaan = $rows[0];

        $details = DB::select("SELECT * FROM view_detail_pengadaan WHERE idpengadaan = ? ORDER BY nama_barang", [$id]);

        return view('transaksi.pengadaan.show', compact('pengadaan', 'details'));
    }

    /** Stored Procedure sp_laporan_pengadaan_vendor */
    public function laporanVendor(Request $request)
    {
        $vendors  = DB::select("SELECT idvendor, nama_vendor FROM vendor ORDER BY idvendor");
        $idvendor = $request->query('idvendor');

        $data = $idvendor
            ? DB::select('CALL sp_laporan_pengadaan_vendor(?)', [$idvendor])
            : [];

        return view('sp_function.laporan_vendor', compact('vendors', 'idvendor', 'data'));
    }
}