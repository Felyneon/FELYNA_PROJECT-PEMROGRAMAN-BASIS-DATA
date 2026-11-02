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

Route::get('/', function () {
    return view('welcome');
});

// Rute untuk katalog barang pakaian
Route::get('/barang', [BarangController::class, 'index']);

// Rute untuk riwayat dan detail transaksi penjualan
Route::get('/penjualan', [PenjualanController::class, 'index']);
Route::get('/penjualan/{id}', [PenjualanController::class, 'show']);

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Main Pages (Protected by simple session check)
Route::get('/dashboard', [DashboardController::class, 'index']);

// Role CRUD Routes
Route::resource('role', RoleController::class);

// Rute CRUD Barang
Route::get('/barang/create', [BarangController::class, 'create']);
Route::post('/barang', [BarangController::class, 'store']);
Route::get('/barang/{id}', [BarangController::class, 'show']);
Route::get('/barang/{id}/edit', [BarangController::class, 'edit']);
Route::put('/barang/{id}', [BarangController::class, 'update']);
Route::delete('/barang/{id}', [BarangController::class, 'destroy']);

// Pastikan rute ini ada di dalam routes/web.php
Route::get('/profile', [ProfileController::class, 'index']);
Route::put('/profile/update', [ProfileController::class, 'update']);

Route::prefix('transaksi')->group(function () {
    // Penjualan
    Route::get('/penjualan', [PenjualanController::class, 'index']);
    Route::get('/penjualan/create', [PenjualanController::class, 'create']);
    Route::post('/penjualan', [PenjualanController::class, 'store']);

    // Pengadaan
    Route::get('/pengadaan', [PengadaanController::class, 'index']);
    Route::get('/pengadaan/create', [PengadaanController::class, 'create']);
    Route::post('/pengadaan', [PengadaanController::class, 'store']);

    // Penerimaan
    Route::get('/penerimaan', [PenerimaanController::class, 'index']);
    Route::get('/penerimaan/create', [PenerimaanController::class, 'create']);
    Route::post('/penerimaan', [PenerimaanController::class, 'store']);

    // Retur
    Route::get('/retur', [ReturController::class, 'index']);
    Route::get('/retur/create', [ReturController::class, 'create']);
    Route::post('/retur', [ReturController::class, 'store']);

    // Kartu Stok
    Route::get('/kartu-stok', [KartuStokController::class, 'index']);
});

Route::resource('margin', MarginPenjualanController::class);

//Route::middleware('auth')->group(function () {
Route::group([], function () {

Route::get('/uji-sp-function', [UjiSpFunctionController::class, 'index']);
Route::get('/transaksi/kartu-stok', [KartuStokController::class, 'index']);
Route::get('/transaksi/kartu-stok/stok-rendah', [KartuStokController::class, 'stokRendah']);
Route::get('/transaksi/penjualan/laporan', [PenjualanController::class, 'laporan']);
Route::get('/transaksi/pengadaan/laporan-vendor', [PengadaanController::class, 'laporanVendor']);
});

use App\Http\Controllers\Ujitriggercontroller;

Route::get('/uji-trigger', [Ujitriggercontroller::class, 'index']);