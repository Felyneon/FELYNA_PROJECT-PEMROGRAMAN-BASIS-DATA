<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Store Pakaian')</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome untuk Icon (Opsional, untuk tombol aksi) -->
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
            <a class="navbar-brand fw-bold" href="/dashboard"><i class="fa-solid fa-store me-2"></i>Store Pakaian</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="/dashboard"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard Barang</a></li>
                    <li class="nav-item"><a class="nav-link" href="/role"><i class="fa-solid fa-user-shield me-1"></i> Kelola Role</a></li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3 small"><i class="fa-solid fa-user-circle me-1"></i> {{ session('username') ?? 'Guest' }}</span>
                    <form action="/logout" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-light btn-sm text-danger fw-bold"><i class="fa-solid fa-right-from-bracket me-1"></i> Logout</button>
                    </form>
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