<?php

namespace App\Http\Controllers;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Models\Satuan;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar katalog barang pakaian
     */
    public function index()
    {
        // Mengambil seluruh data barang beserta relasi satuannya
           $barangs = Barang::with('satuan')
        ->selectRaw('*, fn_get_sisa_stok(idbarang) AS stok_terkini')
        ->get();

    return view('barang.index', compact('barangs'));

        // Atau jika berbentuk API JSON:
        // return response()->json($barangs);
    }
    /**
     * Menampilkan detail satu barang
     */
    public function show($id)
    {
        $barang = Barang::with('satuan')->findOrFail($id);
        return view('barang.show', compact('barang'));
    }

    /**
     * Menampilkan form edit barang
     */
    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $satuans = \App\Models\Satuan::all();
        return view('barang.edit', compact('barang', 'satuans'));
    }

    /**
     * Memproses update data barang ke database
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required',
            'nama' => 'required',
            'harga' => 'required|integer',
            'idsatuan' => 'required|exists:satuan,idsatuan',
            'deskripsi' => 'nullable|string',
            'ukuran' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            
        ]);

        $barang = Barang::findOrFail($id);

        $data = [
            'jenis' => $request->jenis,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'idsatuan' => $request->idsatuan,
            'deskripsi' => $request->deskripsi,
            'ukuran' => $request->ukuran,
            'status' => $request->status,
        ];

        // Proses Upload Gambar Produk Asli
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar && file_exists(public_path($barang->gambar))) {
                unlink(public_path($barang->gambar));
            }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/barang'), $filename);
            
            $data['gambar'] = 'uploads/barang/' . $filename;
        }

        $barang->update($data);

        return redirect('/dashboard')->with('success', 'Data produk pakaian berhasil diperbarui!');
    }

    /**
     * Menghapus data barang
     */
    //public function destroy($id)
    //{
        //Barang::destroy($id);
        //return redirect('/dashboard')->with('success', 'Data barang berhasil dihapus!');
    //}
    public function destroy($id)
{
    Barang::where('idbarang', $id)->update(['status' => 0]);
    return redirect('/dashboard')->with('success', 'Barang dinonaktifkan.');
}
    // Menampilkan form tambah barang
    public function create()
    {
        $satuans = Satuan::all();
        return view('barang.create', compact('satuans'));
    }

    // Menyimpan data barang baru ke database
    public function store(Request $request)
    {
    $request->validate([
        'jenis' => 'required',
        'nama' => 'required',
        'harga' => 'required|integer',
        'idsatuan' => 'required|exists:satuan,idsatuan',
        'deskripsi' => 'nullable|string',
        'ukuran' => 'nullable|string',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        
    ]);

    $data = [
        'jenis' => $request->jenis,
        'nama' => $request->nama,
        'harga' => $request->harga,
        'idsatuan' => $request->idsatuan,
        'deskripsi' => $request->deskripsi,
        'ukuran' => $request->ukuran,
        'status' => 1
    ];

    // Proses upload gambar jika ada
    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/barang'), $filename);
        $data['gambar'] = 'uploads/barang/' . $filename;
    }

        Barang::create($data);

        return redirect('/dashboard')->with('success', 'Produk pakaian baru berhasil ditambahkan!');
    }
}