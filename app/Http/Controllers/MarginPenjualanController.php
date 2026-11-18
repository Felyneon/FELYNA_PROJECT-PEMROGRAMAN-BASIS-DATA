<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class MarginPenjualanController extends Controller
{
    public function index()
    {
        $margins = DB::select("SELECT * FROM view_margin ORDER BY idmargin_penjualan");
        return view('margin.index', compact('margins'));
    }

    public function create()
    {
        return view('margin.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'persen' => 'required|numeric',
            'status' => 'required|in:0,1',
        ]);

        // Margin baru selalu masuk sebagai nonaktif dulu
        DB::insert("INSERT INTO margin_penjualan (persen, status, iduser) VALUES (?, 0, ?)", [
            $request->persen,
            session('iduser') ?? 1,
        ]);
        $id = DB::getPdo()->lastInsertId();

        // Jika diminta aktif, SP menonaktifkan yang lain => hanya 1 margin aktif
        if ($request->status == 1) {
            DB::statement("CALL sp_aktifkan_margin(?)", [$id]);
        }

        return redirect('/margin')->with('success', 'Margin penjualan baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $rows = DB::select("SELECT * FROM margin_penjualan WHERE idmargin_penjualan = ?", [$id]);
        if (empty($rows)) {
            abort(404);
        }
        $margin = $rows[0];

        return view('margin.edit', compact('margin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'persen' => 'required|numeric',
            'status' => 'required|in:0,1',
        ]);

        DB::update("UPDATE margin_penjualan SET persen = ? WHERE idmargin_penjualan = ?", [
            $request->persen, $id,
        ]);

        // Status hanya bisa berubah menjadi AKTIF (yang lain otomatis nonaktif).
        // Menonaktifkan margin aktif tanpa pengganti ditolak: penjualan butuh 1 margin aktif.
        if ($request->status == 1) {
            DB::statement("CALL sp_aktifkan_margin(?)", [$id]);
        }

        return redirect('/margin')->with('success', 'Margin penjualan berhasil diperbarui!');
    }

    /** Tombol "Aktifkan" di daftar margin */
    public function aktifkan($id)
    {
        DB::statement("CALL sp_aktifkan_margin(?)", [$id]);
        return redirect('/margin')->with('success', 'Margin berhasil diaktifkan. Margin lain otomatis nonaktif.');
    }

    public function destroy($id)
    {
        try {
            // Margin aktif tidak boleh dihapus
            $hapus = DB::delete("DELETE FROM margin_penjualan WHERE idmargin_penjualan = ? AND status = 0", [$id]);
        } catch (QueryException $e) {
            return redirect('/margin')->with('error', 'Margin sudah dipakai transaksi penjualan, tidak bisa dihapus.');
        }

        if ($hapus === 0) {
            return redirect('/margin')->with('error', 'Margin yang sedang aktif tidak bisa dihapus. Aktifkan margin lain dulu.');
        }

        return redirect('/margin')->with('success', 'Margin penjualan berhasil dihapus!');
    }
}