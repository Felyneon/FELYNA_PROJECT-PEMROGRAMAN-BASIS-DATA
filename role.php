<?php
// 1. KONEKSI DATABASE MYSQL (Raw SQL / MySQLi)
$host = "localhost";
$user = "root";
$pass = "codingsikil!1029"; // Sesuaikan password database Anda
$db   = "db_project"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

$pesan_sukses = "";
$pesan_error = "";

// 2. LOGIKA PROSES CUD ROLE (CREATE, UPDATE, DELETE)

// A. TAMBAH ROLE (CREATE)
if (isset($_POST['proses_tambah_role'])) {
    $nama_role = mysqli_real_escape_string($koneksi, $_POST['nama_role']);
    
    $query_insert = "INSERT INTO role (nama_role) VALUES ('$nama_role')";
    if (mysqli_query($koneksi, $query_insert)) {
        $pesan_sukses = "Role berhasil ditambahkan!";
    } else {
        $pesan_error = "Gagal menambah role: " . mysqli_error($koneksi);
    }
}

// B. UPDATE ROLE (UPDATE)
if (isset($_POST['proses_update_role'])) {
    $idrole    = intval($_POST['idrole']);
    $nama_role = mysqli_real_escape_string($koneksi, $_POST['nama_role']);
    
    $query_update = "UPDATE role SET nama_role='$nama_role' WHERE idrole='$idrole'";
    if (mysqli_query($koneksi, $query_update)) {
        $pesan_sukses = "Role berhasil diperbarui!";
    } else {
        $pesan_error = "Gagal memperbarui role: " . mysqli_error($koneksi);
    }
}

// C. HAPUS ROLE (DELETE)
if (isset($_GET['hapus'])) {
    $idrole = intval($_GET['hapus']);
    $query_delete = "DELETE FROM role WHERE idrole='$idrole'";
    if (mysqli_query($koneksi, $query_delete)) {
        $pesan_sukses = "Role berhasil dihapus!";
    } else {
        $pesan_error = "Gagal menghapus role: " . mysqli_error($koneksi);
    }
}

// 3. AMBIL DATA ROLE DARI DATABASE
$query_tampil = "SELECT * FROM role ORDER BY idrole DESC";
$result = mysqli_query($koneksi, $query_tampil);

// Panggil Header yang sudah terhubung
include 'header.php';
?>

<!-- NOTIFIKASI -->
<?php if (!empty($pesan_sukses)) : ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?php echo $pesan_sukses; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($pesan_error)) : ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $pesan_error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- KONTEN UTAMA: KELOLA ROLE -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-shield-lock text-primary me-2"></i> Kelola Role Pengguna
        </h5>
        <button class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahRole">
            <i class="bi bi-plus-lg me-1"></i> Tambah Role
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase fs-7 text-secondary">
                    <tr>
                        <th class="ps-4" style="width: 10%;">ID Role</th>
                        <th>Nama Role</th>
                        <th class="text-end pe-4" style="width: 25%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-muted">#<?php echo $row['idrole']; ?></td>
                                <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama_role']); ?></td>
                                <td class="text-end pe-4">
                                    <!-- Tombol Edit Modal -->
                                    <button class="btn btn-warning btn-sm text-white px-2 py-1 shadow-sm me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalEditRole<?php echo $row['idrole']; ?>">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <!-- Tombol Hapus -->
                                    <a href="role.php?hapus=<?php echo $row['idrole']; ?>" 
                                       class="btn btn-danger btn-sm px-2 py-1 shadow-sm" 
                                       onclick="return confirm('Yakin ingin menghapus role ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>

                            <!-- MODAL EDIT ROLE -->
                            <div class="modal fade" id="modalEditRole<?php echo $row['idrole']; ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow rounded-4">
                                        <form action="role.php" method="POST">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Edit Role</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="idrole" value="<?php echo $row['idrole']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Nama Role</label>
                                                    <input type="text" class="form-control" name="nama_role" value="<?php echo htmlspecialchars($row['nama_role']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="proses_update_role" class="btn btn-warning text-white rounded-pill px-4">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">Belum ada data role.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH ROLE -->
<div class="modal fade" id="modalTambahRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form action="role.php" method="POST">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Role Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Role</label>
                        <input type="text" class="form-control" name="nama_role" placeholder="Contoh: Admin, Kasir, Manager..." required>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="proses_tambah_role" class="btn btn-dark rounded-pill px-4">Simpan Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Panggil Footer
include 'footer.php';
?>

 <!-- JS Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>