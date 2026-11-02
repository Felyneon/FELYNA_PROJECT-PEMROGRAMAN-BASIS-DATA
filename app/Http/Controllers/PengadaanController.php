<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengadaan;
use App\Models\DetailPengadaan;
use App\Models\Vendor;
use App\Models\Barang;

use Illuminate\Support\Facades\DB;


class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaans = Pengadaan::with(['user', 'vendor'])->get();
        return view('transaksi.pengadaan.index', compact('pengadaans'));
    }

    public function create()
    {
        // FUNCTION fn_status_badan_hukum_vendor: kode P/C/U menjadi teks lengkap
        $vendors = Vendor::selectRaw('*, fn_status_badan_hukum_vendor(idvendor) AS keterangan_badan_hukum')->get();
        $barangs = Barang::all();
        return view('transaksi.pengadaan.create', compact('vendors', 'barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vendor_idvendor' => 'required',
            'idbarang' => 'required|array',
            'jumlah' => 'required|array',
            'harga_satuan' => 'required|array',
        ]);

        $subtotal = 0;
        $items = [];

        foreach ($request->idbarang as $index => $idbarang) {
            $qty = $request->jumlah[$index];
            $harga = $request->harga_satuan[$index];
            $sub = $qty * $harga;
            $subtotal += $sub;

            $items[] = [
                'idbarang' => $idbarang,
                'jumlah' => $qty,
                'harga_satuan' => $harga,
                'sub_total' => $sub,
            ];
        }

        $ppn = $subtotal * 0.11;
        $total = $subtotal + $ppn;

        $pengadaan = Pengadaan::create([
            'user_iduser' => session('iduser') ?? 1,
            'status' => 'P',
            'vendor_idvendor' => $request->vendor_idvendor,
            'subtotal_nilai' => $subtotal,
            'ppn' => $ppn,
            'total_nilai' => $total,
        ]);

        foreach ($items as $item) {
            $item['idpengadaan'] = $pengadaan->idpengadaan;
            DetailPengadaan::create($item);
        }

        return redirect('/transaksi/pengadaan')->with('success', 'Data pengadaan barang berhasil dibuat!');
    }

    // STORED PROCEDURE sp_laporan_pengadaan_vendor
    public function laporanVendor(Request $request)
    {
        $vendors  = DB::table('vendor')->select('idvendor', 'nama_vendor')->orderBy('idvendor')->get();
        $idvendor = $request->query('idvendor');
        $data     = $idvendor
            ? DB::select('CALL sp_laporan_pengadaan_vendor(?)', [$idvendor])
            : [];

        return view('sp_function.laporan_vendor', compact('vendors', 'idvendor', 'data'));
    }
}