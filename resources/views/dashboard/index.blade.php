@extends('layouts.app')

@section('title', 'Dashboard - Master Barang')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="col-md-6">
         <h3 class="fw-bold text-dark"><i class="fa-solid fa-boxes-stacked text-primary me-2"></i> Dashboard Master Barang</h3>
        <p class="text-muted small mb-0">Kelola katalog produk pakaian dan stok store dengan mudah.</p>
    </div>
    <!-- Tombol Insert / Tambah Barang -->
    <a href="/barang/create" class="btn btn-success fw-semibold shadow-sm">
        <i class="fa-solid fa-plus me-1"></i> Tambah Barang Baru
    </a>
</div>

<div class="card card-custom p-3 bg-white">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3">No</th>
                        <th class="py-3">Jenis Pakaian</th>
                        <th class="py-3">Nama Barang</th>
                        <th class="py-3">Satuan</th>
                        <th class="py-3">Harga Jual</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
        @foreach($barangs as $index => $item)
        <tr>
            <td class="fw-semibold">{{ $index + 1 }}</td>
            <td><span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $item->jenis }}</span></td>
            <td class="fw-bold text-dark">{{ $item->nama }}</td>
            <td>{{ $item->satuan->nama_satuan ?? '-' }}</td>
            <td class="text-success fw-bold">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
            <td class="text-center">
                <!-- Tombol Detail -->
                <a href="/barang/{{ $item->idbarang }}" class="btn btn-info btn-sm text-white px-2 py-1 me-1" title="Detail">
                    <i class="fa-solid fa-eye"></i> Details
                </a>

                <!-- Tombol Edit -->
                <a href="/barang/{{ $item->idbarang }}/edit" class="btn btn-warning btn-sm text-dark px-2 py-1 me-1" title="Edit">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>

                <!-- Form Tombol Delete -->
                <form action="/barang/{{ $item->idbarang }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah yakin ingin menghapus barang ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-2 py-1" title="Hapus">
                        <i class="fa-solid fa-trash"></i> Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
            </table>
        </div>
    </div>
</div>
@endsection