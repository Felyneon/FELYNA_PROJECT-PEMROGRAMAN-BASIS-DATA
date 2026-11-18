@extends('layouts.app')

@section('title', 'Data Retur Barang')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h3 class="fw-bold">Data Retur Barang</h3>
                <p class="text-muted mb-0">Pengelolaan pengembalian barang cacat atau tidak sesuai ke vendor.</p>
            </div>
            <a href="{{ url('/transaksi/retur/create') }}" class="btn btn-danger align-self-start">+ Catat Retur Baru</a>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <table class="table align-middle">
            <thead>
                <tr><th>No</th><th>Waktu Retur</th><th>ID Penerimaan</th><th>Nama Barang</th>
                    <th>Jumlah Retur</th><th>Alasan</th><th>Petugas (User)</th></tr>
            </thead>
            <tbody>
            @forelse($returs as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $r->tanggal_retur }}</td>
                    <td>#{{ $r->idpenerimaan }}</td>
                    <td>{{ $r->nama_barang }}</td>
                    <td>{{ $r->jumlah }}</td>
                    <td>{{ $r->alasan }}</td>
                    <td>{{ $r->nama_petugas }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted">Belum ada data retur.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
