<?php

namespace App\Http\Controllers;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar katalog barang pakaian
     */
    public function index()
    {
        // Mengambil seluruh data barang beserta relasi satuannya
        $barangs = Barang::with('satuan')->get();

        // Jika kamu menggunakan Blade View Laravel:
        return view('barang.index', compact('barangs'));

        // Atau jika berbentuk API JSON:
        // return response()->json($barangs);
    }
}