<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarginPenjualan;

class MarginPenjualanController extends Controller
{
    public function index()
    {
        $margins = MarginPenjualan::with('user')->get();
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

        MarginPenjualan::create([
            'persen' => $request->persen,
            'status' => $request->status,
            'iduser' => session('iduser') ?? 1,
        ]);

        return redirect('/margin')->with('success', 'Margin penjualan baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $margin = MarginPenjualan::findOrFail($id);
        return view('margin.edit', compact('margin'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'persen' => 'required|numeric',
            'status' => 'required|in:0,1',
        ]);

        $margin = MarginPenjualan::findOrFail($id);
        $margin->update([
            'persen' => $request->persen,
            'status' => $request->status,
        ]);

        return redirect('/margin')->with('success', 'Margin penjualan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $margin = MarginPenjualan::findOrFail($id);
        $margin->delete();

        return redirect('/margin')->with('success', 'Margin penjualan berhasil dihapus!');
    }
}
