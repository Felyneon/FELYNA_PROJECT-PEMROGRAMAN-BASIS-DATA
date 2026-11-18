@extends('layouts.app')

@section('title', 'Data Penjualan Kasir - Felyneon Store')

@section('content')
<div class="card shadow p-4 border-0 rounded-4">
    <!-- Header & Tombol Tambah -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1"><i class="fa-solid fa-cart-shopping text-primary me-2"></i> Data Penjualan Kasir (POS)</h3>
            <p class="text-muted small mb-0">Kelola catatan transaksi penjualan pakaian</p>
        </div>
        <a href="/transaksi/penjualan/create" class="btn btn-primary fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-1"></i> Transaksi Baru (POS)
        </a>
    </div>

    <!-- Tabel Data Penjualan -->
    <div class="table-responsive">
        <table class="table table-hover align-middle border">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>ID Penjualan</th>
                    <th>Tanggal Transaksi</th>
                    <th>Kasir</th>
                    <th>Subtotal</th>
                    <th>PPN (11%)</th>
                    <th>Total Nilai</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penjualans as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $item->idpenjualan }}</td>
                    <td>{{ $item->tanggal_transaksi }}</td>
                    <td>
                        <span class="fw-semibold">{{ $item->nama_lengkap_kasir }}</span> 
                        <span class="text-muted small">({{ $item->nama_kasir }})</span>
                    </td>
                    <td>Rp {{ number_format($item->subtotal_nilai, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->ppn, 0, ',', '.') }}</td>
                    <td class="fw-bold text-success">Rp {{ number_format($item->total_nilai, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="/transaksi/penjualan/{{ $item->idpenjualan }}" class="btn btn-sm btn-info text-white shadow-sm">
                            <i class="fa-solid fa-eye me-1"></i> Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Belum ada data transaksi penjualan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection