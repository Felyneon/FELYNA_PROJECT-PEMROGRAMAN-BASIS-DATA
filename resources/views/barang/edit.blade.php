@extends('layouts.app')

@section('title', 'Edit Barang - Store Pakaian')

@section('content')
<div class="container mt-4">
    <div class="card shadow p-4 border-0" style="max-width: 650px; margin: auto; border-radius: 12px;">
        <h3 class="fw-bold mb-3 text-dark">
            <i class="fa-solid fa-pen-to-square text-warning me-2"></i> Edit Data Produk Pakaian
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
        
        <form action="/barang/{{ $barang->idbarang }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Jenis Pakaian</label>
                <input type="text" name="jenis" value="{{ $barang->jenis }}" class="form-control" placeholder="Contoh: Atasan, Bawahan, Outerwear" required>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Nama Barang</label>
                <input type="text" name="nama" value="{{ $barang->nama }}" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">Satuan</label>
                    <select name="idsatuan" class="form-select" required>
                        @foreach($satuans as $satuan)
                            <option value="{{ $satuan->idsatuan }}" {{ $barang->idsatuan == $satuan->idsatuan ? 'selected' : '' }}>
                                {{ $satuan->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold text-secondary">Ukuran (Size)</label>
                    <input type="text" name="ukuran" value="{{ $barang->ukuran }}" class="form-control" placeholder="Contoh: All Size, M, L, XL">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Harga Jual (Rp)</label>
                <input type="number" name="harga" value="{{ $barang->harga }}" class="form-control" required>
            </div>
            <div class="mb-3">
    <label class="form-label small fw-bold text-secondary">Status</label>
    <select name="status" class="form-select" required>
        <option value="1" {{ $barang->status == 1 ? 'selected' : '' }}>Aktif</option>
        <option value="0" {{ $barang->status == 0 ? 'selected' : '' }}>Nonaktif</option>
    </select>
</div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary">Deskripsi Produk</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan detail bahan dan kenyamanan produk...">{{ $barang->deskripsi }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-secondary">Upload Foto Produk Baru</label>
                <input type="file" name="gambar" class="form-control">
                @if($barang->gambar)
                    <div class="mt-2">
                        <small class="text-muted">Foto saat ini:</small><br>
                        <img src="{{ asset($barang->gambar) }}" alt="Preview" class="img-thumbnail mt-1" style="max-height: 80px;">
                    </div>
                @endif
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                </button>
                <a href="/dashboard" class="btn btn-secondary px-4 fw-semibold">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection