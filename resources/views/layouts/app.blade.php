<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Felyneon Store')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome untuk Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; display: flex; flex-direction: column; min-height: 100vh; }
        .navbar-custom { background: linear-gradient(135deg, #3b71ca, #1d4ed8); }
        .main-content { flex: 1; padding: 30px 0; }
        .card-custom { border: none; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        footer { background-color: #1e293b; color: #cbd5e1; }
    </style>
</head>
<body>

    <!-- HEADER / NAVBAR KONSISTEN -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm px-4">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="/dashboard"><i class="fa-solid fa-store me-2"></i>Felyneon Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                
                <!-- MENU KIRI: Hanya muncul jika user sudah login -->
                <ul class="navbar-nav me-auto">
        @if(session('iduser'))
            <li class="nav-item">
                <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="/dashboard">
                    <i class="fa-solid fa-chart-pie me-1"></i> Dashboard Barang
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->is('role*') ? 'active' : '' }}" href="/role">
                    <i class="fa-solid fa-user-shield me-1"></i> Kelola Role
                </a>
            </li>
            <li class="nav-item">
    <a class="nav-link {{ request()->is('margin*') ? 'active' : '' }}" href="/margin">
        <i class="fa-solid fa-percent me-1"></i> Kelola Margin
    </a>
</li>
            <!-- PINDAHKAN KESINI: Menu Manajemen Transaksi agar berdampingan dengan Role -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle {{ request()->is('transaksi*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-cash-register me-1"></i> Manajemen Transaksi
                </a>
                <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item" href="/transaksi/penjualan"><i class="fa-solid fa-cart-shopping me-2 text-primary"></i> Penjualan (POS)</a></li>
                    <li><a class="dropdown-item" href="/transaksi/pengadaan"><i class="fa-solid fa-file-invoice-dollar me-2 text-success"></i> Pengadaan Barang</a></li>
                    <li><a class="dropdown-item" href="/transaksi/penerimaan"><i class="fa-solid fa-boxes-packing me-2 text-warning"></i> Penerimaan Barang</a></li>
                    <li><a class="dropdown-item" href="/transaksi/retur"><i class="fa-solid fa-rotate-left me-2 text-danger"></i> Retur Barang</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="/transaksi/kartu-stok"><i class="fa-solid fa-warehouse me-2 text-info"></i> Kartu Stok Gudang</a></li>
                </ul>
            </li>
        @endif
    </ul>
                <!-- MENU KANAN -->
                <div class="d-flex align-items-center">
                    @if(session('iduser'))
                        <!-- Jika sudah login, tampilkan nama (bisa diklik ke profil) dan tombol logout -->
                        <a href="/profile" class="text-white text-decoration-none me-3 small fw-semibold" style="cursor: pointer;">
                            <i class="fa-solid fa-user-circle me-1"></i> {{ session('username') }}
                        </a>
                        <form action="/logout" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</button>
                        </form>
                    @else
                        <!-- Jika belum login (seperti di halaman register/login), tombol menu disembunyikan bersih -->
                        <a href="/login" class="text-white text-decoration-none small fw-semibold">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- KONTEN DINAMIS -->
    <main class="main-content container">
        @yield('content')
    </main>

    <!-- FOOTER KONSISTEN -->
    <footer class="text-center py-3 mt-auto shadow-sm">
        <div class="container">
            <small>&copy; 2026 Store Pakaian Project Management. All rights reserved.</small>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>