<?php
// register.php - Halaman Pendaftaran Akun Baru
require_once 'bootstrap.php';

$pesan_error = "";
$pesan_sukses = "";

// Jika tombol register ditekan
if (isset($_POST['proses_register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $idrole   = intval($_POST['idrole']);

    if (!empty($username) && !empty($password) && $idrole > 0) {
        // Cek apakah username sudah ada di database (Raw SQL)
        $cek_user = mysqli_query($koneksi, "SELECT * FROM user WHERE username = '$username'");
        
        if (mysqli_num_rows($cek_user) > 0) {
            $pesan_error = "Username sudah terpakai, silakan gunakan username lain!";
        } else {
            // Amankan password menggunakan password_hash
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            
            // Simpan user baru ke tabel user (Raw SQL)
            $query_insert = "INSERT INTO user (username, password, idrole) VALUES ('$username', '$password_hashed', '$idrole')";
            
            if (mysqli_query($koneksi, $query_insert)) {
                $pesan_sukses = "Registrasi berhasil! Silakan login.";
            } else {
                $pesan_error = "Gagal mendaftarkan akun: " . mysqli_error($koneksi);
            }
        }
    } else {
        $pesan_error = "Semua kolom wajib diisi!";
    }
}

// Ambil data role dari database untuk pilihan dropdown (Raw SQL)
$result_role = mysqli_query($koneksi, "SELECT * FROM role");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Felyneon Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-register { width: 100%; max-width: 420px; border: none; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
    </style>
</head>
<body>

<div class="card card-register p-4 bg-white my-4">
    <div class="text-center mb-4">
        <div class="bg-dark text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
            <i class="bi bi-person-plus fs-3"></i>
        </div>
        <h4 class="fw-bold text-dark">Registrasi AkunMu!</h4>
        <p class="text-muted small">Lengkapi Data Kamu ya!</p>
    </div>

    <?php if (!empty($pesan_error)) : ?>
        <div class="alert alert-danger py-2 small" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $pesan_error; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($pesan_sukses)) : ?>
        <div class="alert alert-success py-2 small" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i> <?php echo $pesan_sukses; ?>
            <div class="mt-2">
                <a href="login.php" class="btn btn-sm btn-success w-100 fw-semibold">Kamu sudah masuk ke halaman login</a>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($pesan_sukses)) : ?>
    <form action="register.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold small">Username</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control" name="username" placeholder="Buat username..." required autocomplete="off">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label fw-semibold small">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" name="password" placeholder="Buat password..." required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold small">Pilih Role</label>
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-shield-lock"></i></span>
                <select class="form-select" name="idrole" required>
                    <option value="">Pilih Role</option>
                    <?php while ($r = mysqli_fetch_assoc($result_role)) : ?>
                        <option value="<?php echo $r['idrole']; ?>"><?php echo htmlspecialchars($r['nama_role']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        <button type="submit" name="proses_register" class="btn btn-dark w-100 py-2 fw-semibold rounded-pill shadow-sm mb-3">
            Daftar Sekarang <i class="bi bi-arrow-right ms-1"></i>
        </button>
    </form>
    <?php endif; ?>

    <div class="text-center text-muted small mt-2">
        Sudah punya akun? <a href="login.php" class="text-decoration-none fw-semibold text-dark">Login di sini</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>