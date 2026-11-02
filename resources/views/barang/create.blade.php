@extends('layouts.app')

@section('title', 'Tambah Barang Baru - Felyneon Store')

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0" style="max-width: 650px; margin: auto; border-radius: 12px;">
        <h3 class="fw-bold mb-3 text-dark">
            <i class="fa-solid fa-circle-plus text-success me-2"></i> Tambah Produk Pakaian Baru
        </h3>
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $pesan)
                <li>{{ $pesan }}</li>
            @endforeach
        </ul>
    </div>
@endif
        
        <form action="/barang" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Jenis Pakaian</label>
                <input type="text" name="jenis" class="form-control" placeholder="Contoh: Atasan, Bawahan, Outerwear" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Nama Barang</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Kaos Oversized Cotton Combed" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">Satuan</label>
                    <select name="idsatuan" class="form-select" required>
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($satuans as $satuan)
                            <option value="{{ $satuan->idsatuan }}">{{ $satuan->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">Ukuran (Size)</label>
                    <input type="text" name="ukuran" class="form-control" placeholder="Contoh: All Size, M, L, XL">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Harga Jual (Rp)</label>
                <input type="number" name="harga" class="form-control" placeholder="Contoh: 85000" required>
            </div>
            <div class="mb-3">
    <label class="form-label small fw-bold text-secondary">Status</label>
    <select name="status" class="form-select" required>
        <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ old('status', 1) == 0 ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Deskripsi Produk</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan detail bahan dan kenyamanan produk..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Upload Foto Produk</label>
                <input type="file" name="gambar" class="form-control">
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success px-4 fw-semibold shadow-sm">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Barang Baru
                </button>
                <a href="/dashboard" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection