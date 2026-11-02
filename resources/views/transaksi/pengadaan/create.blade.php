@extends('layouts.app')

@section('title', 'Buat Pengadaan Barang')

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0 rounded-4" style="max-width: 700px; margin: auto;">
        <h3 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-file-invoice-dollar text-success me-2"></i> Form Pengadaan Barang</h3>
        
        <form action="/transaksi/pengadaan" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Pilih Vendor / Supplier</label>
                <select name="vendor_idvendor" class="form-select" required>
                    <option value="">-- Pilih Vendor --</option>
                    @foreach($vendors as $v)
                        <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }} ({{ $v->keterangan_badan_hukum }})</option>
                    @endforeach
                </select>
            </div>

            <hr>
            <h5 class="fw-bold text-secondary">Item Barang</h5>
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Barang</label>
                <select name="idbarang[0]" class="form-select" required>
                    <option value="">-- Pilih Barang --</option>
                    @foreach($barangs as $b)
                        <option value="{{ $b->idbarang }}">{{ $b->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">Jumlah (Qty)</label>
                    <input type="number" name="jumlah[0]" class="form-control" value="1" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">Harga Satuan (Rp)</label>
                    <input type="number" name="harga_satuan[0]" class="form-control" placeholder="Contoh: 70000" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4 fw-semibold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Pengadaan
                </button>
                <a href="/transaksi/pengadaan" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection