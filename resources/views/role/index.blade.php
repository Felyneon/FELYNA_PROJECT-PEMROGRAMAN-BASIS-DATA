@extends('layouts.app')

@section('title', 'Kelola Role - Felyneon Store')

@section('content')
<div class="container mt-4">
    
    <!-- NOTIFIKASI BERHASIL (WARNA HIJAU) -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Card Tambah Role -->
    <div class="card shadow p-4 border-0 rounded-4 mb-4">
        <h4 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-user-shield me-2"></i> Manajemen Role Pengguna</h4>
        <p class="text-muted small mb-4">Tambah, ubah, atau hapus peran hak akses pegawai store pakaian.</p>

        <form action="/role" method="POST" class="row g-3 align-items-center">
            @csrf
            <div class="col-md-10">
                <label class="form-label small fw-bold text-secondary">Tambah Role Baru</label>
                <input type="text" name="nama_role" class="form-control" placeholder="Contoh: Kasir Utama, Kepala Gudang..." required>
            </div>
            <div class="col-md-2 d-grid">
                <label class="form-label small fw-bold text-transparent">&nbsp;</label>
                <button type="submit" class="btn btn-primary fw-semibold shadow-sm">
                    <i class="fa-solid fa-plus me-1"></i> Simpan Role
                </button>
            </div>
        </form>
    </div>

    <!-- Card Tabel Data Role -->
    <div class="card shadow p-4 border-0 rounded-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 15%;">ID Role</th>
                        <th style="width: 85%;">Nama Role & Aksi Manajemen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $r)
                    <tr>
                        <td class="fw-bold text-secondary">#{{ $r->idrole }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <!-- Form untuk Edit Nama Role -->
                                <form action="/role/{{ $r->idrole }}" method="POST" class="d-flex gap-2 flex-grow-1 align-items-center">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="nama_role" value="{{ $r->nama_role }}" class="form-control form-control-sm" required>
                                    <!-- Tombol diubah menjadi Edit -->
                                    <button type="submit" class="btn btn-warning btn-sm fw-semibold text-dark text-nowrap shadow-sm">
                                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                                    </button>
                                </form>

                                <!-- Form untuk Hapus Role -->
                                <form action="/role/{{ $r->idrole }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus role ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm fw-semibold text-nowrap shadow-sm">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection