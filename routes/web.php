<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;

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