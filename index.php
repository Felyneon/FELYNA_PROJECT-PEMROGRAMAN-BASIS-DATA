<?php
// Panggil bootstrap (session & koneksi otomatis aktif)
require_once 'bootstrap.php';

// Cek apakah user sudah login, jika belum arahkan ke login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Logika Logout
if (isset($_GET['page']) && $_GET['page'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}

$pesan_sukses = "";
$pesan_error = "";
$page = isset($_GET['page']) ? $_GET['page'] : 'barang';

// ==========================================
// LOGIKA CUD: BARANG
// ==========================================
if ($page == 'barang') {
    if (isset($_POST['proses_tambah'])) {
        $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $jenis    = mysqli_real_escape_string($koneksi, $_POST['jenis']);
        $idsatuan = intval($_POST['idsatuan']);
        $harga    = intval($_POST['harga']);
        $status   = 1;

        $query = "INSERT INTO barang (jenis, nama, idsatuan, status, harga) VALUES ('$jenis', '$nama', '$idsatuan', '$status', '$harga')";
        if (mysqli_query($koneksi, $query)) {
            $pesan_sukses = "Barang berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menambah barang: " . mysqli_error($koneksi);
        }
    }
    if (isset($_POST['proses_update'])) {
        $idbarang = intval($_POST['idbarang']);
        $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
        $jenis    = mysqli_real_escape_string($koneksi, $_POST['jenis']);
        $idsatuan = intval($_POST['idsatuan']);
        $harga    = intval($_POST['harga']);

        $query = "UPDATE barang SET nama='$nama', jenis='$jenis', idsatuan='$idsatuan', harga='$harga' WHERE idbarang='$idbarang'";
        if (mysqli_query($koneksi, $query)) {
            $pesan_sukses = "Data barang berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui barang: " . mysqli_error($koneksi);
        }
    }
    if (isset($_GET['hapus'])) {
        $idbarang = intval($_GET['hapus']);
        $query = "DELETE FROM barang WHERE idbarang='$idbarang'";
        if (mysqli_query($koneksi, $query)) {
            $pesan_sukses = "Barang berhasil dihapus!";
        } else {
            $pesan_error = "Gagal menghapus barang: " . mysqli_error($koneksi);
        }
    }
}

// ==========================================
// LOGIKA CUD: ROLE
// ==========================================
if ($page == 'role') {
    if (isset($_POST['proses_tambah_role'])) {
        $nama_role = mysqli_real_escape_string($koneksi, $_POST['nama_role']);
        $query = "INSERT INTO role (nama_role) VALUES ('$nama_role')";
        if (mysqli_query($koneksi, $query)) {
            $pesan_sukses = "Role berhasil ditambahkan!";
        } else {
            $pesan_error = "Gagal menambah role: " . mysqli_error($koneksi);
        }
    }
    if (isset($_POST['proses_update_role'])) {
        $idrole    = intval($_POST['idrole']);
        $nama_role = mysqli_real_escape_string($koneksi, $_POST['nama_role']);
        $query = "UPDATE role SET nama_role='$nama_role' WHERE idrole='$idrole'";
        if (mysqli_query($koneksi, $query)) {
            $pesan_sukses = "Role berhasil diperbarui!";
        } else {
            $pesan_error = "Gagal memperbarui role: " . mysqli_error($koneksi);
        }
    }
    if (isset($_GET['hapus_role'])) {
        $idrole = intval($_GET['hapus_role']);
        $query = "DELETE FROM role WHERE idrole='$idrole'";
        if (mysqli_query($koneksi, $query)) {
            $pesan_sukses = "Role berhasil dihapus!";
        } else {
            $pesan_error = "Gagal menghapus role: " . mysqli_error($koneksi);
        }
    }
}

// Panggil Header
include 'header.php';
?>

<!-- NOTIFIKASI ALERT -->
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



<!-- 3. TAMPILAN KONTEN HALAMAN BARANG -->

<?php if ($page == 'barang') : 
    $result = mysqli_query($koneksi, "SELECT b.*, s.nama_satuan FROM barang b LEFT JOIN satuan s ON b.idsatuan = s.idsatuan ORDER BY b.idbarang DESC");
    $result_satuan = mysqli_query($koneksi, "SELECT * FROM satuan");
    $result_satuan_edit = mysqli_query($koneksi, "SELECT * FROM satuan");
?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-box-seam text-primary me-2"></i>Data Barang
            </h5>
            <button class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-lg me-1"></i> Tambah Barang
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Nama Barang</th>
                            <th>Jenis</th>
                            <th>Satuan</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted"><?php echo $row['idbarang']; ?></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama']); ?></td>
                                    <td><span class="badge bg-secondary px-2 py-1"><?php echo $row['jenis']; ?></span></td>
                                    <td><?php echo htmlspecialchars($row['nama_satuan'] ?? '-'); ?></td>
                                    <td class="fw-semibold text-success">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 1) : ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">Aktif</span>
                                        <?php else : ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1 rounded-pill">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-warning btn-sm text-white px-2 py-1 shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?php echo $row['idbarang']; ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <a href="index.php?page=barang&hapus=<?php echo $row['idbarang']; ?>" class="btn btn-danger btn-sm px-2 py-1 shadow-sm" onclick="return confirm('Yakin ingin menghapus barang ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>

                                <!-- MODAL EDIT BARANG -->
                                <div class="modal fade" id="modalEdit<?php echo $row['idbarang']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form action="index.php?page=barang" method="POST">
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold">Edit Barang</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start">
                                                    <input type="hidden" name="idbarang" value="<?php echo $row['idbarang']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Nama Barang</label>
                                                        <input type="text" class="form-control" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Jenis</label>
                                                        <input type="text" class="form-control" name="jenis" value="<?php echo htmlspecialchars($row['jenis']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Satuan</label>
                                                        <select class="form-select" name="idsatuan" required>
                                                            <?php 
                                                            mysqli_data_seek($result_satuan_edit, 0);
                                                            while ($sat = mysqli_fetch_assoc($result_satuan_edit)) : 
                                                                $selected = ($sat['idsatuan'] == $row['idsatuan']) ? 'selected' : '';
                                                            ?>
                                                                <option value="<?php echo $sat['idsatuan']; ?>" <?php echo $selected; ?>><?php echo $sat['nama_satuan']; ?></option>
                                                            <?php endwhile; ?>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Harga (Rp)</label>
                                                        <input type="number" class="form-control" name="harga" value="<?php echo $row['harga']; ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="proses_update" class="btn btn-warning text-white rounded-pill px-4">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data barang.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH BARANG -->
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <form action="index.php?page=barang" method="POST">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Barang Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Barang</label>
                            <input type="text" class="form-control" name="nama" placeholder="Masukkan nama barang..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis</label>
                            <input type="text" class="form-control" name="jenis" placeholder="Masukkan jenis..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Satuan</label>
                            <select class="form-select" name="idsatuan" required>
                                <option value="">-- Pilih Satuan --</option>
                                <?php while ($sat = mysqli_fetch_assoc($result_satuan)) : ?>
                                    <option value="<?php echo $sat['idsatuan']; ?>"><?php echo $sat['nama_satuan']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Harga (Rp)</label>
                            <input type="number" class="form-control" name="harga" placeholder="Contoh: 10000" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="proses_tambah" class="btn btn-dark rounded-pill px-4">Simpan Barang</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


      <!4. TAMPILAN KONTEN HALAMAN ROLE>

<?php elseif ($page == 'role') : 
    $result_role = mysqli_query($koneksi, "SELECT * FROM role ORDER BY idrole DESC");
?>
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-0">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="bi bi-shield-lock text-primary me-2"></i> Kelola Role
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
                        <?php if (mysqli_num_rows($result_role) > 0) : ?>
                            <?php while ($row = mysqli_fetch_assoc($result_role)) : ?>
                                <tr>
                                    <td class="ps-4 fw-semibold text-muted">#<?php echo $row['idrole']; ?></td>
                                    <td class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama_role']); ?></td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-warning btn-sm text-white px-2 py-1 shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#modalEditRole<?php echo $row['idrole']; ?>">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                        <a href="index.php?page=role&hapus_role=<?php echo $row['idrole']; ?>" class="btn btn-danger btn-sm px-2 py-1 shadow-sm" onclick="return confirm('Yakin ingin menghapus role ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>

                                <!-- MODAL EDIT ROLE -->
                                <div class="modal fade" id="modalEditRole<?php echo $row['idrole']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow rounded-4">
                                            <form action="index.php?page=role" method="POST">
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
                            <tr><td colspan="3" class="text-center py-4 text-muted">Belum ada data role.</td></tr>
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
                <form action="index.php?page=role" method="POST">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Tambah Role Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Role</label>
                            <input type="text" class="form-control" name="nama_role" placeholder="Contoh: Admin, Kasir..." required>
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
<?php endif; ?>

<?php 
// Panggil Footer
include 'footer.php';
?>