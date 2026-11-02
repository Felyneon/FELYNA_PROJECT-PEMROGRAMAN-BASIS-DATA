@extends('layouts.app')

@section('title', 'Data Penerimaan Barang')

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
                <h3 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-boxes-packing text-warning me-2"></i> Data Penerimaan Barang</h3>
                <p class="text-muted small mb-0">Catat penerimaan fisik barang dari vendor dan masukkan otomatis ke stok.</p>
            </div>
            <a href="/transaksi/penerimaan/create" class="btn btn-warning fw-semibold shadow-sm text-dark">
                <i class="fa-solid fa-plus me-1"></i> Catat Penerimaan Baru
            </a>
        </div>
        
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Waktu Terima</th>
                        <th>Vendor Asal</th>
                        <th>Status</th>
                        <th>Penerima (User)</th>
                        <th>Total Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penerimaans as $index => $pn)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $pn->created_at }}</td>
                        <td>{{ $pn->pengadaan->vendor->nama_vendor ?? '-' }}</td>
                        <td><span class="badge bg-success">Diterima & Stok Bertambah</span></td>
                        <td>{{ $pn->user->username ?? '-' }}</td>
                        <td>Rp {{ number_format($pn->total_nilai_penerimaan, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data penerimaan barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
