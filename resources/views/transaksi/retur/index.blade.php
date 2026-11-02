@extends('layouts.app')

@section('title', 'Data Retur Barang')

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
                <h3 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-rotate-left text-danger me-2"></i> Data Retur Barang</h3>
                <p class="text-muted small mb-0">Pengelolaan pengembalian barang cacat atau tidak sesuai ke vendor.</p>
            </div>
            <a href="/transaksi/retur/create" class="btn btn-danger fw-semibold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Catat Retur Baru
            </a>
        </div>
        
        <div class="table-responsive mt-3">
            <table class="table table-hover align-middle">
                <thead>
                         <tr>
                            <th>No</th>
                            <th>Waktu Retur</th>
                            <th>ID Penerimaan</th>
                            <th>Nama Barang</th>
                            <th>Jumlah Retur</th>
                            <th>Alasan</th>
                            <th>Petugas (User)</th>
                            </tr>
                        </thead>
                        <tbody>
                         @forelse ($returs as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $r->created_at }}</td>
                            <td>#{{ $r->idpenerimaan }}</td>
                            <td>{{ $r->nama_barang }}</td>
                            <td>{{ $r->jumlah }}</td>
                            <td>{{ $r->alasan }}</td>
                            <td>{{ $r->username }}</td>
                     </tr>
                @empty
                <tr><td colspan="7" class="text-center">Belum ada data retur.</td></tr>
                @endforelse
            </tbody>
            </table>
        </div>
    </div>
</div>
@endsection