<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ReturController extends Controller
{
    public function index()
    {
        $returs = DB::select("SELECT * FROM view_daftar_retur ORDER BY idretur DESC");
        return view('transaksi.retur.index', compact('returs'));
    }

    /** Pilih penerimaan -> tampil barang yang masih bisa diretur (sisa_bisa_retur) */
    public function create(Request $request)
    {
        $penerimaans = DB::select("SELECT * FROM view_rekap_penerimaan ORDER BY idpenerimaan DESC");

        $selectedPenerimaan = null;
        $details = [];

        if ($request->filled('idpenerimaan')) {
            $rows = DB::select("SELECT * FROM view_nota_penerimaan WHERE idpenerimaan = ?", [$request->idpenerimaan]);
            $selectedPenerimaan = $rows[0] ?? null;

            if ($selectedPenerimaan) {
                $details = DB::select(
                    "SELECT * FROM view_detail_penerimaan WHERE idpenerimaan = ? AND sisa_bisa_retur > 0",
                    [$request->idpenerimaan]
                );
            }
        }

        return view('transaksi.retur.create', compact('penerimaans', 'selectedPenerimaan', 'details'));
    }

    /** Tanpa DB::transaction. Kartu stok 'R' dicatat trigger trg_kartu_stok_retur. */
    public function store(Request $request)
    {
        $request->validate([
            'idpenerimaan'        => 'required',
            'iddetail_penerimaan' => 'required',
            'jumlah'              => 'required|integer|min:1',
            'alasan'              => 'required|string',
        ]);

        // Pengaman sisi aplikasi: jumlah retur tidak boleh melebihi sisa yang bisa diretur
        $row = DB::select("SELECT sisa_bisa_retur FROM view_detail_penerimaan WHERE iddetail_penerimaan = ? AND idpenerimaan = ?", [
            $request->iddetail_penerimaan, $request->idpenerimaan,
        ]);
        if (empty($row) || $request->jumlah > $row[0]->sisa_bisa_retur) {
            return back()->withInput()->with('error', 'Jumlah retur melebihi sisa yang bisa diretur.');
        }

        try {
            DB::insert("INSERT INTO retur (created_at, idpenerimaan, iduser) VALUES (NOW(), ?, ?)", [
                $request->idpenerimaan, session('iduser') ?? 1,
            ]);
            $idretur = DB::getPdo()->lastInsertId();

            DB::insert("INSERT INTO detail_retur (jumlah, alasan, idretur, iddetail_penerimaan) VALUES (?, ?, ?, ?)", [
                $request->jumlah, $request->alasan, $idretur, $request->iddetail_penerimaan,
            ]);
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/retur')->with('success', 'Retur barang berhasil dicatat!');
    }
}