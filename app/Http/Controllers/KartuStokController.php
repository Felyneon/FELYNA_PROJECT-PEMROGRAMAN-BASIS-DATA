<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuStokController extends Controller
{
    public function index(Request $request)
    {
        $barang   = DB::select("SELECT idbarang, nama FROM barang ORDER BY nama");
        $idbarang = $request->query('idbarang');

        // Stored Procedure: riwayat mutasi kartu stok satu barang
        $riwayat = $idbarang
            ? DB::select('CALL sp_riwayat_kartu_stok_barang(?)', [$idbarang])
            : [];

        // Notifikasi stok rendah dihitung di Blade dari data ini (bukan di database)
        $stokBarang = DB::select("SELECT * FROM view_stok_barang WHERE status_barang = 1 ORDER BY sisa_stok ASC");
        $batasStok  = 10;

        return view('transaksi.kartu_stok.index', compact('barang', 'riwayat', 'idbarang', 'stokBarang', 'batasStok'));
    }

    // Method stokRendah() + route-nya dihapus: sp_stok_barang_rendah sudah dibuang
    // karena notifikasi stok pindah ke frontend.
}
