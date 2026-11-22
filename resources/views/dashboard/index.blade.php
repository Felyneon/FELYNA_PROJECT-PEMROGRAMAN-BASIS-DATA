{{-- Ganti 'layouts.app' jika nama layout kamu berbeda --}}
@extends('layouts.app')

@section('content')
@php
    // NOTIFIKASI STOK RENDAH: logika di frontend, bukan di database
    $rendah = collect($barangs)->filter(fn($b) => $b->status == 1 && $b->stok_terkini <= $batasStok);
@endphp
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h2 class="fw-bold">Dashboard Master Barang</h2>
            <p class="text-muted mb-0">Kelola katalog produk pakaian</p>
        </div>
        @akses('barang.create')
            <a href="{{ url('/barang/create') }}" class="btn btn-success">+ Tambah Barang Baru</a>
        @endakses
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

    @if(!empty($q))
        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <span>Hasil pencarian "<strong>{{ $q }}</strong>": {{ count($barangs) }} barang ditemukan.</span>
            <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-outline-primary">Reset</a>
        </div>
    @endif

    @if($rendah->count() > 0 && \App\Support\AksesKasir::bisa('pengadaan.create'))
        <div class="alert alert-warning">
            <strong>Stok menipis (≤ {{ $batasStok }}), segera lakukan pengadaan:</strong>
            @foreach($rendah as $b)
                <span class="badge bg-danger ms-1">{{ $b->nama_barang }}: {{ $b->stok_terkini }} {{ $b->nama_satuan }}</span>
            @endforeach
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius:16px">
        <div class="card-body">
            <table class="table align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th><th>Jenis Pakaian</th><th>Nama Barang</th><th>Satuan</th>
                        <th>Harga Dasar</th><th>Stok</th><th>Status</th><th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($barangs as $i => $b)
                    <tr class="{{ $b->status == 1 ? '' : 'table-secondary text-muted' }}">
                        <td>{{ $i + 1 }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $b->jenis }}</span></td>
                        <td class="fw-bold">{{ $b->nama_barang }}</td>
                        <td>{{ $b->nama_satuan }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($b->harga_dasar, 0, ',', '.') }}</td>
                        <td>
                            {{ $b->stok_terkini }}
                            @if($b->status == 1 && $b->stok_terkini <= $batasStok)
                                <span class="badge bg-danger">Stok rendah</span>
                            @endif
                        </td>
                        <td>
                            @if($b->status == 1) <span class="badge bg-success">Aktif</span>
                            @else <span class="badge bg-secondary">Nonaktif</span> @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ url('/barang/'.$b->idbarang) }}" class="btn btn-sm btn-info text-white">Details</a>
                            @akses('barang.update')
                                <a href="{{ url('/barang/'.$b->idbarang.'/edit') }}" class="btn btn-sm btn-warning">Edit</a>
                            @endakses
                            @akses('barang.status')
                            <form action="{{ url('/barang/'.$b->idbarang.'/toggle') }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('{{ $b->status == 1 ? 'Nonaktifkan' : 'Aktifkan' }} barang ini?')">
                                @csrf
                                @if($b->status == 1)
                                    <button class="btn btn-sm btn-danger">Nonaktifkan</button>
                                @else
                                    <button class="btn btn-sm btn-success">Aktifkan</button>
                                @endif
                            </form>
                            @endakses
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
