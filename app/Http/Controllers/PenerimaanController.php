<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Penerimaan;
use App\Models\Pengadaan;
use App\Models\DetailPengadaan;
use App\Models\DetailPenerimaan;

class PenerimaanController extends Controller
{
    public function index()
    {
        // FUNCTION fn_hitung_total_penerimaan: total nilai dihitung oleh MySQL
        $penerimaans = Penerimaan::with(['pengadaan.vendor', 'user'])
            ->selectRaw('*, fn_hitung_total_penerimaan(idpenerimaan) AS total_nilai_penerimaan')
            ->get();

        return view('transaksi.penerimaan.index', compact('penerimaans'));
    }

    public function create()
    {
        $pengadaans = Pengadaan::where('status', 'P')->get();
        return view('transaksi.penerimaan.create', compact('pengadaans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idpengadaan' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $penerimaan = Penerimaan::create([
                    'status' => 'S',
                    'idpengadaan' => $request->idpengadaan,
                    'iduser' => session('iduser') ?? 1,
                ]);

                $detailPengadaans = DetailPengadaan::where('idpengadaan', $request->idpengadaan)->get();

                foreach ($detailPengadaans as $dp) {
                    // TRIGGER trg_calc_sub_detail_penerimaan : menghitung sub_total_terima
                    // TRIGGER trg_kartu_stok_penerimaan      : mencatat kartu stok masuk (M)
                    DetailPenerimaan::create([
                        'idpenerimaan' => $penerimaan->idpenerimaan,
                        'barang_idbarang' => $dp->idbarang,
                        'jumlah_terima' => $dp->jumlah,
                        'harga_satuan_terima' => $dp->harga_satuan,
                        'sub_total_terima' => $dp->sub_total,
                    ]);
                }

                Pengadaan::where('idpengadaan', $request->idpengadaan)->update(['status' => 'S']);
            });
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/penerimaan')->with('success', 'Penerimaan barang berhasil dicatat dan stok bertambah!');
    }
}