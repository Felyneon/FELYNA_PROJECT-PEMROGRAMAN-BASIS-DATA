@extends('layouts.app')

@section('title', 'Profil Pengguna - Store Pakaian')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card card-custom border-0 shadow p-4 bg-white">
                <h4 class="fw-bold mb-3 text-secondary"><i class="fa-solid fa-user-gear me-2"></i> Pengaturan Profil Pengguna</h4>
                
                <form action="/profile/update" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Username</label>
                        <input type="text" class="form-control bg-light" value="{{ $user->username }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ $user->nama_lengkap }}" placeholder="Masukkan nama lengkap">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">No. Hape</label>
                        <input type="text" name="no_hape" class="form-control" value="{{ $user->no_hape }}" placeholder="Contoh: 081234567890">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" placeholder="alamat.email@domain.com">
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">Help Center / Catatan Bantuan</label>
                        <input type="text" name="help_center" class="form-control" value="{{ $user->help_center }}" placeholder="Catatan atau informasi pusat bantuan terkait akun">
                    </div>

                    <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan
                    </button>
                    <a href="/dashboard" class="btn btn-secondary px-3 fw-semibold">Kembali ke Dashboard</a>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection