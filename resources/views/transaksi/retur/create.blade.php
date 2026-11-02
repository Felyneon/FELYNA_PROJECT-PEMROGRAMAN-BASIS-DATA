@extends('layouts.app')

@section('title', 'Catat Retur Barang')
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif
@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0 rounded-4" style="max-width: 650px; margin: auto;">
        <h3 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-rotate-left text-danger me-2"></i> Form Retur Barang ke Vendor</h3>
        
        <form action="/transaksi/retur" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Pilih ID Penerimaan</label>
                <select name="idpenerimaan" class="form-select" required>
                    <option value="">-- Pilih Penerimaan --</option>
                    @foreach($penerimaans as $pn)
                        <option value="{{ $pn->idpenerimaan }}">Penerimaan #{{ $pn->idpenerimaan }} (Vendor: {{ $pn->pengadaan->vendor->nama_vendor ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">ID Detail Penerimaan</label>
                <input type="number" name="iddetail_penerimaan" class="form-control" placeholder="Masukkan ID Detail Penerimaan" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Jumlah Retur</label>
                <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Alasan Retur</label>
                <textarea name="alasan" class="form-control" rows="3" placeholder="Tuliskan alasan pengembalian barang..." required></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger px-4 fw-semibold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Retur
                </button>
                <a href="/transaksi/retur" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection