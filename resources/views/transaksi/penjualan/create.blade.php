@extends('layouts.app')

@section('title', 'Tambah Transaksi Penjualan')
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0 rounded-4" style="max-width: 700px; margin: auto;">
        <h3 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-cart-shopping text-primary me-2"></i> Form Transaksi Penjualan</h3>
        
        <form action="/transaksi/penjualan" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Pilih Margin Penjualan</label>
                <select name="idmargin_penjualan" class="form-select" required>
                    <option value="">-- Pilih Margin --</option>
                    @foreach($margins as $m)
                        <option value="{{ $m->idmargin_penjualan }}">{{ $m->persen }}%</option>
                    @endforeach
                </select>
            </div>

            <hr>
            <h5 class="fw-bold text-secondary">Pilih Produk</h5>
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Barang</label>
                <select name="idbarang[0]" class="form-select" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $b)
                        <option value="{{ $b->idbarang }}">{{ $b->nama }} (Rp {{ number_format($b->harga, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Jumlah Beli (Qty)</label>
                <input type="number" name="jumlah[0]" class="form-control" value="1" min="1" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 fw-semibold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Penjualan
                </button>
                <a href="/transaksi/penjualan" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection