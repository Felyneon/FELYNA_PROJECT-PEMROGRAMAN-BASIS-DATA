<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index()
    {
        $barangs = DB::select("SELECT * FROM view_detail_barang_lengkap ORDER BY idbarang");
        return view('barang.index', compact('barangs'));
    }

    public function show($id)
    {
        $rows = DB::select("
            SELECT b.*, s.nama_satuan, fn_get_sisa_stok(b.idbarang) AS stok_terkini
            FROM barang b
            JOIN satuan s ON b.idsatuan = s.idsatuan
            WHERE b.idbarang = ?
        ", [$id]);

        if (empty($rows)) {
            abort(404);
        }
        $barang = $rows[0];

        // Catatan: di view barang/show.blade.php ganti $barang->satuan->nama_satuan
        // menjadi $barang->nama_satuan (tidak ada relasi Eloquent lagi).
        return view('barang.show', compact('barang'));
    }

    public function create()
    {
        $satuans = DB::select("SELECT * FROM satuan ORDER BY nama_satuan");
        return view('barang.create', compact('satuans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis'     => 'required',
            'nama'      => 'required',
            'harga'     => 'required|integer',
            'idsatuan'  => 'required|exists:satuan,idsatuan',
            'deskripsi' => 'nullable|string',
            'ukuran'    => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/barang'), $filename);
            $gambar = 'uploads/barang/' . $filename;
        }

        // Barang baru otomatis aktif (status = 1)
        DB::insert("
            INSERT INTO barang (jenis, nama, harga, idsatuan, deskripsi, ukuran, gambar, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ", [
            $request->jenis, $request->nama, $request->harga, $request->idsatuan,
            $request->deskripsi, $request->ukuran, $gambar,
        ]);

        return redirect('/dashboard')->with('success', 'Produk pakaian baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $rows = DB::select("SELECT * FROM barang WHERE idbarang = ?", [$id]);
        if (empty($rows)) {
            abort(404);
        }
        $barang  = $rows[0];
        $satuans = DB::select("SELECT * FROM satuan ORDER BY nama_satuan");

        return view('barang.edit', compact('barang', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis'     => 'required',
            'nama'      => 'required',
            'harga'     => 'required|integer',
            'idsatuan'  => 'required|exists:satuan,idsatuan',
            'deskripsi' => 'nullable|string',
            'ukuran'    => 'nullable|string',
            'gambar'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $rows = DB::select("SELECT gambar FROM barang WHERE idbarang = ?", [$id]);
        if (empty($rows)) {
            abort(404);
        }

        $gambarBaru = null;
        if ($request->hasFile('gambar')) {
            $lama = $rows[0]->gambar;
            if ($lama && file_exists(public_path($lama))) {
                unlink(public_path($lama));
            }
            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/barang'), $filename);
            $gambarBaru = 'uploads/barang/' . $filename;
        }

        // Status TIDAK diubah di sini (jangan sampai jadi NULL kalau form tidak mengirim status).
        // Aktif/nonaktif lewat tombol toggle di dashboard.
        // gambar = COALESCE(?, gambar): kalau tidak upload baru, gambar lama dipertahankan.
        DB::update("
            UPDATE barang
            SET jenis = ?, nama = ?, harga = ?, idsatuan = ?, deskripsi = ?, ukuran = ?,
                gambar = COALESCE(?, gambar)
            WHERE idbarang = ?
        ", [
            $request->jenis, $request->nama, $request->harga, $request->idsatuan,
            $request->deskripsi, $request->ukuran, $gambarBaru, $id,
        ]);

        return redirect('/dashboard')->with('success', 'Data produk pakaian berhasil diperbarui!');
    }

    /**
     * Aktifkan / nonaktifkan barang (pengganti Delete).
     * Memanggil Stored Procedure sp_toggle_status_barang.
     */
    public function toggle($id)
    {
        DB::statement("CALL sp_toggle_status_barang(?)", [$id]);
        return redirect('/dashboard')->with('success', 'Status barang berhasil diubah.');
    }
}