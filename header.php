<?php
// Pastikan user sudah login, jika belum lempar ke halaman login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'barang';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Manajemen Toko</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
    </style>
</head>
<body>

<!-- Sidebar / Offcanvas Menu Navigasi -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
    <div class="offcanvas-header bg-dark text-white">
        <h5 class="offcanvas-title fw-bold" id="sidebarMenuLabel"><i class="bi bi-grid-fill me-2"></i>Menu Navigasi</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-3 bg-light border-bottom mb-2">
            <small class="text-muted d-block">Login sebagai:</small>
            <strong class="text-dark fs-6"><?php echo $_SESSION['username']; ?></strong>
            <span class="badge bg-primary d-inline-block mt-1"><?php echo $_SESSION['role']; ?></span>
        </div>
        <div class="list-group list-group-flush rounded-0">
            <a href="index.php?page=barang" class="list-group-item list-group-item-action py-3 <?php echo ($page == 'barang') ? 'active' : ''; ?>">
                <i class="bi bi-box-seam me-2"></i> Data Barang
            </a>
            <a href="index.php?page=role" class="list-group-item list-group-item-action py-3 <?php echo ($page == 'role') ? 'active' : ''; ?>">
                <i class="bi bi-shield-lock me-2"></i> Kelola Role
            </a>
            <a href="index.php?page=logout" class="list-group-item list-group-item-action py-3 text-danger mt-3 border-top">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </div>
    </div>
</div>

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu">
            <i class="bi bi-list fs-5"></i>
        </button>
        <a class="navbar-brand fw-bold" href="index.php?page=barang"><i class="bi bi-shop me-2"></i>FELYNEON STORE</a>
        <div class="ms-auto text-white d-flex align-items-center">
            <span class="small me-3 d-none d-md-inline">Halo, <strong><?php echo $_SESSION['username']; ?></strong> (<?php echo $_SESSION['role']; ?>)</span>
            <a href="index.php?page=logout" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</a>
        </div>
    </div>
</nav>

<!-- Container Utama -->
<div class="container my-4">