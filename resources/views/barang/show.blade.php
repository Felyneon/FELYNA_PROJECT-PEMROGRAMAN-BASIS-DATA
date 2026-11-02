@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0" style="max-width: 850px; margin: auto; border-radius: 12px;">
        <h3 class="fw-bold mb-4 text-dark">
            <i class="fa-solid fa-circle-info text-primary me-2"></i> Detail Produk Pakaian
        </h3>
        
        <div class="row g-4 align-items-center">
            <!-- Kolom Gambar Produk Asli -->
            <div class="col-md-5 text-center">
                <img src="{{ $barang->gambar ? asset($barang->gambar) : 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&q=80&w=400' }}" 
                     alt="{{ $barang->nama }}" 
                     class="img-fluid rounded shadow-sm border" 
                     style="max-height: 320px; width: 100%; object-fit: cover;">
            </div>

            <!-- Kolom Informasi Detail -->
            <div class="col-md-7">
                <h4 class="fw-bold text-dark mb-2">{{ $barang->nama }}</h4>
                <div class="mb-3">
                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1 me-2">Jenis: {{ $barang->jenis }}</span>
                    <span class="badge bg-dark text-white px-2 py-1">Size: {{ $barang->ukuran ?? 'All Size' }}</span>
                </div>
                
                <p class="mb-2 text-muted small"><strong>Satuan:</strong> {{ $barang->satuan->nama_satuan ?? '-' }}</p>
                <h4 class="text-success fw-bold mb-3">Rp {{ number_format($barang->harga, 0, ',', '.') }}</h4>
                
                <hr class="text-muted">
                
                <div class="mb-4">
                    <strong class="text-secondary small d-block mb-1">Deskripsi Produk:</strong>
                    <p class="text-muted small mb-0" style="line-height: 1.6;">
                        {{ $barang->deskripsi ?? 'Belum ada deskripsi untuk produk ini.' }}
                    </p>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="/barang/{{ $barang->idbarang }}/edit" class="btn btn-warning btn-sm text-dark px-3 fw-semibold">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Produk Ini
                    </a>
                    <a href="/dashboard" class="btn btn-secondary btn-sm px-3 fw-semibold">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection