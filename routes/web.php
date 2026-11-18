<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\MarginPenjualanController;
use App\Http\Controllers\UjiSpFunctionController;
use App\Http\Controllers\Ujitriggercontroller;

Route::get('/', function () {
    return view('welcome');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (dicek session di controller)
Route::get('/dashboard', [DashboardController::class, 'index']);

// Role
Route::resource('role', RoleController::class);

// Profile
Route::get('/profile', [ProfileController::class, 'index']);
Route::put('/profile/update', [ProfileController::class, 'update']);

// Barang (tanpa DELETE: diganti toggle aktif/nonaktif)
Route::get('/barang', [BarangController::class, 'index']);
Route::get('/barang/create', [BarangController::class, 'create']);
Route::post('/barang', [BarangController::class, 'store']);
Route::get('/barang/{id}', [BarangController::class, 'show'])->whereNumber('id');
Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->whereNumber('id');
Route::put('/barang/{id}', [BarangController::class, 'update'])->whereNumber('id');
Route::post('/barang/{id}/toggle', [BarangController::class, 'toggle'])->whereNumber('id');

// Margin (resource + aktifkan: hanya 1 margin aktif)
Route::post('/margin/{id}/aktifkan', [MarginPenjualanController::class, 'aktifkan'])->whereNumber('id');
Route::resource('margin', MarginPenjualanController::class);

// Transaksi
Route::prefix('transaksi')->group(function () {

    // Penjualan: laporan & create SEBELUM {id}
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::get('/penjualan/create', [PenjualanController::class, 'create']);
    Route::get('/penjualan/laporan', [PenjualanController::class, 'laporan']);
    Route::post('/penjualan', [PenjualanController::class, 'store']);
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->whereNumber('id');

    // Pengadaan
    Route::get('/pengadaan', [PengadaanController::class, 'index']);
    Route::get('/pengadaan/create', [PengadaanController::class, 'create']);
    Route::get('/pengadaan/laporan-vendor', [PengadaanController::class, 'laporanVendor']);
    Route::post('/pengadaan', [PengadaanController::class, 'store']);
    Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])->whereNumber('id');

    // Penerimaan
    Route::get('/penerimaan', [PenerimaanController::class, 'index']);
    Route::get('/penerimaan/create', [PenerimaanController::class, 'create']);
    Route::post('/penerimaan', [PenerimaanController::class, 'store']);
    Route::get('/penerimaan/{id}', [PenerimaanController::class, 'show'])->whereNumber('id');

    // Retur
    Route::get('/retur', [ReturController::class, 'index']);
    Route::get('/retur/create', [ReturController::class, 'create']);
    Route::post('/retur', [ReturController::class, 'store']);

    // Kartu stok (route stok-rendah dihapus: notifikasi pindah ke frontend)
    Route::get('/kartu-stok', [KartuStokController::class, 'index']);
});

// Halaman uji
Route::get('/uji-sp-function', [UjiSpFunctionController::class, 'index']);
Route::get('/uji-trigger', [Ujitriggercontroller::class, 'index']);

// Rute lama untuk riwayat penjualan (tetap dipertahankan)
Route::get('/penjualan', [PenjualanController::class, 'index']);
Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->whereNumber('id');