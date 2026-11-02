@extends('layouts.app')

@section('title', 'Data Pengadaan Barang')

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
                <h3 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-invoice-dollar text-success me-2"></i> Data Pengadaan Barang</h3>
                <p class="text-muted small mb-0">Kelola pemesanan stok pakaian dari vendor atau supplier.</p>
            </div>
            <a href="/transaksi/pengadaan/create" class="btn btn-success fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Buat Pengadaan Baru
            </a>
        </div>
        
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Vendor</th>
                        <th>Pembuat (User)</th>
                        <th>Status</th>
                        <th>Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengadaans as $index => $pg)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $pg->timestamp }}</td>
                        <td>{{ $pg->vendor->nama_vendor ?? '-' }}</td>
                        <td>{{ $pg->user->username ?? '-' }}</td>
                        <td>
                            @if($pg->status == 'P')
                                <span class="badge bg-warning text-dark">Pending (Proses)</span>
                            @else
                                <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td class="fw-bold text-success">Rp {{ number_format($pg->total_nilai, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data pengadaan barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection