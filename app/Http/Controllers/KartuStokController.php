<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KartuStok;
use Illuminate\Support\Facades\DB; // Pastikan facade DB di-import

class KartuStokController extends Controller
{
    public function index(Request $request)
{
    $barang   = DB::table('barang')->select('idbarang', 'nama')->orderBy('nama')->get();
    $idbarang = $request->query('idbarang');
    $riwayat  = $idbarang
        ? DB::select('CALL sp_riwayat_kartu_stok_barang(?)', [$idbarang])
        : [];

    return view('transaksi.kartu_stok.index', compact('barang', 'riwayat', 'idbarang'));
}

// SP: sp_stok_barang_rendah
public function stokRendah(Request $request)
{
    $batas = (int) $request->query('batas', 10);
    $data  = DB::select('CALL sp_stok_barang_rendah(?)', [$batas]);

    return view('transaksi.kartu_stok.stok_rendah', compact('data', 'batas'));
}
}

