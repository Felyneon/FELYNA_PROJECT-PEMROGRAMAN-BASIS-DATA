@extends('layouts.app')

@section('title', 'Data Pengadaan Barang')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h3 class="fw-bold">Data Pengadaan Barang</h3>
                <p class="text-muted mb-0">Kelola pemesanan stok pakaian dari vendor atau supplier.</p>
            </div>
            <a href="{{ url('/transaksi/pengadaan/create') }}" class="btn btn-success align-self-start">+ Buat Pengadaan Baru</a>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr><th>No</th><th>ID</th><th>Waktu</th><th>Vendor</th><th>Pembuat (User)</th>
                    <th class="text-center">Barang</th><th>Status</th><th class="text-end">Total Nilai</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($pengadaans as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $item->idpengadaan }}</td>
                    <td>{{ $item->tanggal_pengadaan }}</td>
                    <td class="fw-semibold">{{ $item->nama_vendor }}</td>
                    <td>{{ $item->nama_pembuat }}</td>
                    <td class="text-center">{{ $item->total_jenis_barang }} jenis / {{ $item->total_qty }} pcs</td>
                    <td>
                        @if($item->status == 'S') <span class="badge bg-success">Selesai</span>
                        @else <span class="badge bg-warning text-dark">Pending (Proses)</span> @endif
                    </td>
                    <td class="text-end fw-bold">Rp {{ number_format($item->total_nilai, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ url('/transaksi/pengadaan/'.$item->idpengadaan) }}" class="btn btn-sm btn-info text-white">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">Belum ada data pengadaan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
