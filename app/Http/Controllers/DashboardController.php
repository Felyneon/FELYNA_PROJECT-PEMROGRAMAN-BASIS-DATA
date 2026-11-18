<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!session()->has('iduser')) {
            return redirect('/login');
        }

        // View MySQL (sudah memuat satuan, status, dan stok lewat fn_get_sisa_stok)
        $barangs = DB::select("SELECT * FROM view_detail_barang_lengkap ORDER BY idbarang");

        // Batas notifikasi stok rendah: logikanya ada di Blade (frontend), bukan di database
        $batasStok = 10;

        return view('dashboard.index', compact('barangs', 'batasStok'));
    }
}