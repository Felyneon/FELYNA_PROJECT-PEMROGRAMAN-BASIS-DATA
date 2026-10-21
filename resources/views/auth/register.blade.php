<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Felyneon Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <nav class="navbar navbar-dark bg-primary px-4 shadow-sm" style="background: linear-gradient(135deg, #3b71ca, #1d4ed8);">
        <span class="navbar-brand mb-0 h1 fw-bold">Felyneon Store</span>
    </nav>

    <div class="flex-grow-1 d-flex align-items-center justify-content: center py-5">
        <div class="card shadow border-0 p-4" style="width: 100%; max-width: 420px; border-radius: 12px;">
            <h3 class="text-center fw-bold mb-1 text-dark">Buat Akun Baru</h3>
            <p class="text-center text-muted small mb-4">Daftar untuk akses manajemen store</p>
            <form action="/register" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Buat username" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Pilih Role Pekerja</label>
                    <select name="idrole" class="form-select" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->idrole }}">{{ $role->nama_role }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-success w-100 py-2 fw-bold shadow-sm">DAFTAR SEKARANG</button>
            </form>
            <div class="text-center mt-3">
                <small class="text-muted">Sudah punya akun? <a href="/login" class="text-decoration-none fw-bold">Login di sini</a></small>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <small>&copy; 2026 Store Pakaian Project.</small>
    </footer>
</body>
</html>