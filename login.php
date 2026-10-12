<?php
// login.php
require_once 'bootstrap.php';

$pesan_error = "";

// Jika tombol login ditekan
if (isset($_POST['proses_login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // 1. Cek apakah username benar-benar ada di database (Raw SQL dengan JOIN ke role)
        $query = "SELECT u.*, r.nama_role FROM user u JOIN role r ON u.idrole = r.idrole WHERE u.username = '$username'";
        $result = mysqli_query($koneksi, $query);

        // Jika username ditemukan di database
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            // 2. Verifikasi password yang di-input dengan password ter-hash di database
           if (password_verify($password, $row['password'])) {
                // Password cocok! Set session login dengan key 'username' dan 'role'
                $_SESSION['username'] = $row['username'];
                $_SESSION['role']     = $row['nama_role']; // Pastikan pakai 'role' jika header.php memanggil 'role'
                
                // Arahkan ke halaman utama/dashboard barang
                header("Location: index.php?page=barang");
                exit;
            
            } else {
                $pesan_error = "Password yang kamu masukkan salah!";
            }
        } else {
            // Jika username tidak ditemukan di database (Belum register)
            $pesan_error = "Username belum terdaftar! Silakan daftar akun terlebih dahulu.";
        }
    } else {
        $pesan_error = "Username dan password tidak boleh kosong!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Store Project</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
    body { 
        font-family: 'Inter', sans-serif; 
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); 
        min-height: 100vh; 
        margin: 0; /* <-- Tambahkan di sini */
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .card-login { width: 100%; max-width: 400px; border: none; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
</style>
</head>
<body>

<div class="card card-login p-4 bg-white">
    <div class="text-center mb-4">
        <div class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
            <i class="bi bi-shop fs-3"></i>
        </div>
        <h4 class="fw-bold text-dark">Felyneon Store</h4>
        <p class="text-muted small">Silakan login untuk mengakses sistem</p>
    </div>

    <?php if (!empty($pesan_error)) : ?>
        <div class="alert alert-danger py-2 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $pesan_error; ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold small">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Masukkan username..." required autocomplete="off">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold small">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Masukkan password..." required>
            </div>
        </div>
        <button type="submit" name="proses_login" class="btn btn-dark w-100 py-2 fw-semibold rounded-pill shadow-sm mb-3">
            Masuk ke Sistem <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>

    <div class="text-center text-muted small">
        Belum punya akun? <a href="register.php" class="text-decoration-none fw-semibold text-dark">Daftar di sini</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>