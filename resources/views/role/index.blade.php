@extends('layouts.app')

@section('title', 'Kelola Role - Store Pakaian')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h3 class="fw-bold text-dark"><i class="fa-solid fa-user-gear text-primary me-2"></i> Manajemen Role Pengguna</h3>
        <p class="text-muted small">Tambah, ubah, atau hapus peran hak akses pegawai store pakaian.</p>
    </div>
</div>

<!-- Form Tambah Role -->
<div class="card card-custom p-4 bg-white mb-4">
    <h5 class="fw-semibold mb-3 text-secondary">Tambah Role Baru</h5>
    <form action="/role" method="POST">
        @csrf
        <div class="input-group">
            <input type="text" name="nama_role" class="form-control" placeholder="Contoh: Kasir Utama, Kepala Gudang..." required>
            <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fa-solid fa-plus me-1"></i> Simpan Role</button>
        </div>
    </form>
</div>

<!-- Tabel Daftar Role -->
<div class="card card-custom p-3 bg-white">
    <div class="card-body">
        <table class="table table-striped align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="py-3">ID Role</th>
                    <th class="py-3">Nama Role / Hak Akses</th>
                    <th class="py-3 text-center">Aksi Manajemen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $role)
                <tr>
                    <td class="fw-bold text-muted">#{{ $role->idrole }}</td>
                    <td class="fw-semibold text-dark">{{ $role->nama_role }}</td>
                    <td class="text-center">
                        <!-- Form Update Sederhana / Inline -->
                        <form action="/role/{{ $role->idrole }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="text" name="nama_role" value="{{ $role->nama_role }}" class="form-control form-control-sm d-inline w-50 me-1" required>
                            <button type="submit" class="btn btn-warning btn-sm text-dark px-2"><i class="fa-solid fa-pen"></i> Update</button>
                        </form>
                        <!-- Form Delete -->
                        <form action="/role/{{ $role->idrole }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah yakin ingin menghapus role ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm px-2"><i class="fa-solid fa-trash"></i> Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection