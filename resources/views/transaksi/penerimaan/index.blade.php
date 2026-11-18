@extends('layouts.app')

@section('title', 'Data Penerimaan Barang')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0" style="border-radius:16px"><div class="card-body">
        <div class="d-flex justify-content-between mb-3">
            <div>
                <h3 class="fw-bold">Data Penerimaan Barang</h3>
                <p class="text-muted mb-0">Kelola catatan penerimaan fisik barang dari vendor/supplier.</p>
            </div>
            <a href="{{ url('/transaksi/penerimaan/create') }}" class="btn btn-warning align-self-start">+ Catat Penerimaan Baru</a>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <table class="table table-bordered align-middle">
            <thead class="table-dark">
                <tr><th>No</th><th>ID Penerimaan</th><th>Tanggal Diterima</th><th>Pengadaan</th><th>Vendor</th>
                    <th>Staff Penerima</th><th class="text-center">Jenis</th><th class="text-center">Total Qty Terima</th>
                    <th class="text-end">Total Nilai</th><th class="text-center">Aksi</th></tr>
            </thead>
            <tbody>
            @forelse($penerimaans as $index => $pn)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>#{{ $pn->idpenerimaan }}</td>
                    <td>{{ $pn->tanggal_penerimaan }}</td>
                    <td>#{{ $pn->idpengadaan }}</td>
                    <td class="fw-semibold">{{ $pn->nama_vendor }}</td>
                    <td>{{ $pn->nama_staff_penerima }}</td>
                    <td class="text-center">{{ $pn->total_jenis_barang }}</td>
                    <td class="text-center"><span class="badge bg-success">{{ $pn->total_qty_terima }} Pcs</span></td>
                    <td class="text-end fw-bold text-success">Rp {{ number_format($pn->total_nilai_penerimaan, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <a href="{{ url('/transaksi/penerimaan/'.$pn->idpenerimaan) }}" class="btn btn-sm btn-info text-white">Detail</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" class="text-center text-muted">Belum ada data penerimaan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div>
</div>
@endsection
