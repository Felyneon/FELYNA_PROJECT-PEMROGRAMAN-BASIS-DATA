<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PenerimaanController extends Controller
{
    public function index()
    {
        $penerimaans = DB::select("SELECT * FROM view_rekap_penerimaan ORDER BY idpenerimaan DESC");
        return view('transaksi.penerimaan.index', compact('penerimaans'));
    }

    /** Pilih pengadaan berstatus 'P', lalu semua barang di pengadaan itu muncul */
    public function create(Request $request)
    {
        $pengadaans = DB::select("SELECT * FROM view_daftar_pengadaan WHERE status = 'P' ORDER BY idpengadaan DESC");

        $selectedPengadaan = null;
        $detailPengadaans  = [];

        if ($request->filled('idpengadaan')) {
            $idp  = $request->idpengadaan;
            $rows = DB::select("SELECT * FROM view_nota_pengadaan WHERE idpengadaan = ? AND status = 'P'", [$idp]);
            $selectedPengadaan = $rows[0] ?? null;

            if ($selectedPengadaan) {
                $detailPengadaans = DB::select("SELECT * FROM view_detail_pengadaan WHERE idpengadaan = ? ORDER BY nama_barang", [$idp]);
            }
        }

        return view('transaksi.penerimaan.create', compact('pengadaans', 'selectedPengadaan', 'detailPengadaans'));
    }

    /**
     * Header penerimaan + detail per barang. Per barang boleh input harga baru.
     * Trigger: sub_total_terima, kartu stok masuk, dan update harga barang (jika harga berbeda).
     * Setelah itu CALL sp_selesaikan_pengadaan -> status pengadaan jadi 'S'.
     */
    public function store(Request $request)
    {
        $request->validate([
            'idpengadaan'           => 'required',
            'idbarang'              => 'required|array|min:1',
            'idbarang.*'            => 'required|integer',
            'jumlah_terima'         => 'required|array',
            'jumlah_terima.*'       => 'required|integer|min:0',
            'harga_satuan_terima'   => 'required|array',
            'harga_satuan_terima.*' => 'required|integer|min:0',
        ]);

        $idpengadaan = $request->idpengadaan;
        $iduser      = session('iduser') ?? 1;

        // Pengadaan hanya boleh diterima sekali
        $cek = DB::select("SELECT status FROM pengadaan WHERE idpengadaan = ?", [$idpengadaan]);
        if (empty($cek) || $cek[0]->status !== 'P') {
            return back()->withInput()->with('error', 'Pengadaan ini sudah diterima atau tidak ditemukan.');
        }

        // Hanya barang dengan jumlah terima > 0 yang dicatat
        $items = [];
        foreach ($request->idbarang as $i => $idbarang) {
            $jml = (int) $request->jumlah_terima[$i];
            if ($jml > 0) {
                $items[] = [$idbarang, $jml, (int) $request->harga_satuan_terima[$i]];
            }
        }
        if (empty($items)) {
            return back()->withInput()->with('error', 'Isi jumlah terima minimal untuk satu barang.');
        }

        try {
            DB::insert("INSERT INTO penerimaan (created_at, status, idpengadaan, iduser) VALUES (NOW(), 'S', ?, ?)", [
                $idpengadaan, $iduser,
            ]);
            $idpenerimaan = DB::getPdo()->lastInsertId();

            foreach ($items as [$idbarang, $jml, $harga]) {
                // sub_total_terima 0: dihitung trigger
                DB::insert("
                    INSERT INTO detail_penerimaan (idpenerimaan, barang_idbarang, jumlah_terima, harga_satuan_terima, sub_total_terima)
                    VALUES (?, ?, ?, ?, 0)
                ", [$idpenerimaan, $idbarang, $jml, $harga]);
            }

            DB::statement("CALL sp_selesaikan_pengadaan(?)", [$idpengadaan]);
        } catch (QueryException $e) {
            return back()->withInput()->with('error', $e->errorInfo[2] ?? 'Terjadi kesalahan database.');
        }

        return redirect('/transaksi/penerimaan/' . $idpenerimaan)->with('success', 'Penerimaan dicatat, stok bertambah. Berikut notanya.');
    }

    /** Nota penerimaan */
    public function show($id)
    {
        $rows = DB::select("SELECT * FROM view_nota_penerimaan WHERE idpenerimaan = ?", [$id]);
        if (empty($rows)) {
            abort(404);
        }
        $penerimaan = $rows[0];

        $details = DB::select("SELECT * FROM view_detail_penerimaan WHERE idpenerimaan = ? ORDER BY nama_barang", [$id]);

        return view('transaksi.penerimaan.show', compact('penerimaan', 'details'));
    }
}