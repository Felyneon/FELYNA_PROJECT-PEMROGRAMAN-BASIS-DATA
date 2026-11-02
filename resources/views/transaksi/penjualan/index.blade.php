@extends('layouts.app')

@section('title', 'Data Transaksi Penjualan')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow p-4 border-0 rounded-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-cart-shopping text-primary me-2"></i> Data Transaksi Penjualan (POS)</h3>
                <p class="text-muted small mb-0">Daftar riwayat penjualan produk pakaian kepada pelanggan.</p>
            </div>
            <a href="/transaksi/penjualan/create" class="btn btn-primary fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Transaksi Baru
            </a>
        </div>
        
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Kasir / User</th>
                        <th>Margin</th>
                        <th>Subtotal</th>
                        <th>PPN (11%)</th>
                        <th>Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penjualans as $index => $p)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $p->created_at }}</td>
                        <td>{{ $p->user->username ?? '-' }}</td>
                        <td>{{ $p->marginPenjualan->persen ?? '-' }}%</td>
                        <td>Rp {{ number_format($p->subtotal_nilai, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->ppn, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($p->total_nilai, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada data transaksi penjualan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection