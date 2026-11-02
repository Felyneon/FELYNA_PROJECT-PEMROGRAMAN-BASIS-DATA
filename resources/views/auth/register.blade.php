@extends('layouts.app')

@section('title', 'Buat Akun Baru - Felyneon Store')

@section('content')
<div class="container mt-5">
    <!-- Tambahkan row justify-content-center 
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <!-- Card Form Register -->
            <div class="card shadow p-4 border-0 rounded-4">
                <h3 class="fw-bold mb-1 text-dark">Buat Akun Baru</h3>
                <p class="text-muted small mb-4">Daftar untuk akses manajemen store</p>

                <form action="/register" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Username</label>
                        <input type="text" name="username" class="form-control" placeholder="Buat username" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>

                    <div class="mb-4">
                          <label class="form-label small fw-bold text-muted">Pilih Role Pekerja</label>
                    <select name="idrole" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->idrole }}">{{ $role->nama_role }}</option>
                        @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fw-bold py-2 mb-3">DAFTAR SEKARANG</button>
                    
                    <div class="text-center small text-muted">
                        Sudah punya akun? <a href="/login" class="text-decoration-none fw-semibold">Login di sini</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection