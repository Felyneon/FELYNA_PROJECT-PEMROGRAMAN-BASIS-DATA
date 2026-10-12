
<?php
// bootstrap.php - Pusat Inisialisasi Sistem, Session & Koneksi Database
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database (Raw SQL / MySQLi)
$host = "localhost";
$user = "root";
$pass = "codingsikil!1029"; // Sesuaikan password database kamu
$db   = "db_project"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>