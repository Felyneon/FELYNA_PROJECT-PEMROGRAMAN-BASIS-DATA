<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Felyneon Store</title>
    <!-- Menggunakan Bootstrap 5 CSS agar instan dan rapi -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Desain Background Hero sama dengan halaman login */
        .hero-section {
            background: url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&q=80&w=1920') no-repeat center center;
            background-size: cover;
            position: relative;
            min-height: 82vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 0; /* ruang atas-bawah karena form registrasi lebih panjang */
        }
        /* Efek gelap transparan tipis di atas background */
        .hero-section::before {
            content: "";
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.25);
        }
        /* Kotak Card Register Melayang di Tengah */
        .login-card {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 6px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 380px;
            padding: 35px;
        }
        .btn-custom-sign {
            background-color: #3b71ca;
            border: none;
        }
        .btn-custom-sign:hover {
            background-color: #335ca8;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100 bg-light">

    <!-- 1. HEADER / NAVBAR -->
    <nav class="navbar navbar-dark bg-primary px-4 shadow-sm" style="background-color: #3b71ca !important;">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 fw-bold">FELYNEON STORE</span>
            <a href="/login" class="text-white fw-semibold small text-decoration-none">LOGIN</a>
        </div>
    </nav>

    <!-- 2. MAIN HERO SECTION & REGISTER FORM -->
    <div class="hero-section flex-grow-1">
        <div class="login-card">
            <div class="text-center mb-4">
                <!-- Icon Logo Kecil di atas tulisan Welcome -->
                <div class="mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="#3b71ca" class="bi bi-person-circle" viewBox="0 0 16 16">
                      <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                      <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                    </svg>
                </div>
                <h4 class="fw-semibold text-secondary mb-0">Create Account</h4>
            </div>

            {{-- Pesan error validasi / username sudah dipakai --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form Register (rute POST /register) -->
            <form action="/register" method="POST">
                @csrf
                <div class="mb-3">
                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}" class="form-control bg-light border-secondary-subtle" placeholder="Nama Lengkap *" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="username" value="{{ old('username') }}" class="form-control bg-light border-secondary-subtle" placeholder="User Name *" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control bg-light border-secondary-subtle" placeholder="Password * (min. 6 karakter)" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password_confirmation" class="form-control bg-light border-secondary-subtle" placeholder="Ulangi Password *" required>
                </div>
                <button type="submit" class="btn btn-custom-sign text-white w-100 py-2 fw-bold shadow-sm">SIGN UP</button>
            </form>

            <p class="text-center text-muted small mt-3 mb-0">
                Sudah punya akun? <a href="/login" class="text-decoration-none">Login</a>
            </p>
        </div>
    </div>

    <!-- 3. FOOTER -->
    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <small>&copy; 2026 Store Pakaian Project. All rights reserved.</small>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>