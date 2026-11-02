<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Models\Retur;
use App\Models\DetailRetur;
use App\Models\Penerimaan;

class ReturController extends Controller
{
    public function index()
    {
        $returs = DB::table('retur as r')
            ->join('user as u', 'u.iduser', '=', 'r.iduser')
            ->join('detail_retur as dr', 'dr.idretur', '=', 'r.idretur')
            ->join('detail_penerimaan as dp', 'dp.iddetail_penerimaan', '=', 'dr.iddetail_penerimaan')
            ->join('barang as b', 'b.idbarang', '=', 'dp.barang_idbarang')
            ->select(
                'r.idretur', 'r.created_at', 'r.idpenerimaan',
                'u.username', 'b.nama as nama_barang', 'dr.jumlah', 'dr.alasan'
            )
            ->orderByDesc('r.idretur')
            ->get();

        return view('transaksi.retur.index', compact('returs'));
    }

    public function create()
    {
        $penerimaans = Penerimaan::all();
        return view('transaksi.retur.create', compact('penerimaans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'idpenerimaan' => 'required',
            'iddetail_penerimaan' => 'required',
            'jumlah' => 'required|integer',
            'alasan' => 'required|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $retur = Retur::create([
                    'created_at' => now(),
                    'idpenerimaan' => $request->idpenerimaan,
                    'iduser' => session('iduser') ?? 1,
                ]);

                // TRIGGER trg_validate_detail_retur : validasi jumlah retur dan stok
                // TRIGGER trg_kartu_stok_retur      : mencatat kartu stok keluar (R)
                DetailRetur::create([
                    'jumlah' => $request->jumlah,
                    'alasan' => $request->alasan,
                    'idretur' => $retur->idretur,
                    'iddetail_penerimaan' => $request->iddetail_penerimaan,
                ]);
            });
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/retur')->with('success', 'Retur barang berhasil dicatat!');
    }
}