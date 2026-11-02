@extends('layouts.app')

@section('title', 'Catat Penerimaan Barang')
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0 rounded-4" style="max-width: 600px; margin: auto;">
        <h3 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-boxes-packing text-warning me-2"></i> Catat Penerimaan Barang</h3>
        
        <form action="/transaksi/penerimaan" method="POST">
            @csrf
            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Pilih Pengadaan (Status Pending)</label>
                <select name="idpengadaan" class="form-select" required>
                    <option value="">-- Pilih ID Pengadaan --</option>
                    @foreach($pengadaans as $pg)
                        <option value="{{ $pg->idpengadaan }}">ID: #{{ $pg->idpengadaan }} - Vendor: {{ $pg->vendor->nama_vendor ?? '-' }} (Total: Rp {{ number_format($pg->total_nilai, 0, ',', '.') }})</option>
                    @endforeach
                </select>
                <small class="text-muted">Penerimaan akan otomatis mencatat stok masuk ke gudang.</small>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-warning fw-semibold shadow-sm text-dark px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Proses Penerimaan
                </button>
                <a href="/transaksi/penerimaan" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection