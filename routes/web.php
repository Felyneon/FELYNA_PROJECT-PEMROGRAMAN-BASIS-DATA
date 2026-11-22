<?php
// Pengganti routes/web.php. Setiap route diberi middleware 'akses:<izin>'.

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
use App\Http\Controllers\UjiTriggerController;


Route::get('/', function () {
    return view('welcome');
});

// ---------- Auth (tanpa login) ----------
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ---------- Hanya perlu login ----------
Route::middleware('akses')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
});

// ---------- Dashboard & Data Master Barang ----------
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('akses:barang.read');

    Route::get('/barang', [BarangController::class, 'index'])->middleware('akses:barang.read');
    Route::get('/barang/create', [BarangController::class, 'create'])->middleware('akses:barang.create');
    Route::post('/barang', [BarangController::class, 'store'])->middleware('akses:barang.create');
    Route::get('/barang/{id}', [BarangController::class, 'show'])->whereNumber('id')->middleware('akses:barang.read');
    Route::get('/barang/{id}/edit', [BarangController::class, 'edit'])->whereNumber('id')->middleware('akses:barang.update');
    Route::put('/barang/{id}', [BarangController::class, 'update'])->whereNumber('id')->middleware('akses:barang.update');
    Route::post('/barang/{id}/toggle', [BarangController::class, 'toggle'])->whereNumber('id')->middleware('akses:barang.update');
    Route::delete('/barang/{id}', [BarangController::class, 'destroy'])->whereNumber('id')->middleware('akses:barang.delete');

// ---------- Role & Margin (manager) ----------
    Route::resource('role', RoleController::class)->middleware('akses:role.manage');
    Route::get('/margin', [MarginPenjualanController::class, 'index'])->middleware('akses:margin.read');
    Route::get('/margin/create', [MarginPenjualanController::class, 'create'])->middleware('akses:margin.create');
    Route::post('/margin', [MarginPenjualanController::class, 'store'])->middleware('akses:margin.create');
    Route::get('/margin/{id}/edit', [MarginPenjualanController::class, 'edit'])->whereNumber('id')->middleware('akses:margin.update');
    Route::put('/margin/{id}', [MarginPenjualanController::class, 'update'])->whereNumber('id')->middleware('akses:margin.update');
    Route::delete('/margin/{id}', [MarginPenjualanController::class, 'destroy'])->whereNumber('id')->middleware('akses:margin.delete');
    Route::post('/margin/{id}/aktifkan', [MarginPenjualanController::class, 'aktifkan'])->whereNumber('id')->middleware('akses:margin.update');


// ---------- Transaksi ----------
Route::prefix('transaksi')->group(function () {

    // Penjualan (create/edit/laporan SEBELUM {id})
    Route::get('/penjualan', [PenjualanController::class, 'index'])->middleware('akses:penjualan.read');
    Route::get('/penjualan/create', [PenjualanController::class, 'create'])->middleware('akses:penjualan.create');
    Route::get('/penjualan/laporan', [PenjualanController::class, 'laporan'])->middleware('akses:laporan.read');
    Route::post('/penjualan', [PenjualanController::class, 'store'])->middleware('akses:penjualan.create');
    Route::get('/penjualan/{id}', [PenjualanController::class, 'show'])->whereNumber('id')->middleware('akses:penjualan.read');
    Route::get('/penjualan/{id}/edit', [PenjualanController::class, 'edit'])->whereNumber('id')->middleware('akses:penjualan.update');
    Route::put('/penjualan/{id}', [PenjualanController::class, 'update'])->whereNumber('id')->middleware('akses:penjualan.update');
    Route::post('/penjualan/{id}/bayar', [PenjualanController::class, 'bayar'])->whereNumber('id')->middleware('akses:penjualan.bayar');
    Route::delete('/penjualan/{id}', [PenjualanController::class, 'destroy'])->whereNumber('id')->middleware('akses:penjualan.delete');

    // Pengadaan
    Route::get('/pengadaan', [PengadaanController::class, 'index'])->middleware('akses:pengadaan.read');
    Route::get('/pengadaan/create', [PengadaanController::class, 'create'])->middleware('akses:pengadaan.create');
    Route::get('/pengadaan/laporan-vendor', [PengadaanController::class, 'laporanVendor'])->middleware('akses:laporan.read');
    Route::post('/pengadaan', [PengadaanController::class, 'store'])->middleware('akses:pengadaan.create');
    Route::get('/pengadaan/{id}', [PengadaanController::class, 'show'])->whereNumber('id')->middleware('akses:pengadaan.read');
// UPDATE
    Route::get('/pengadaan/{id}/edit',[PengadaanController::class, 'edit'])->whereNumber('id')->middleware('akses:pengadaan.update');
    Route::put('/pengadaan/{id}',[PengadaanController::class, 'update'])->whereNumber('id')->middleware('akses:pengadaan.update');
// APPROVAL
    Route::post('/pengadaan/{id}/approve',[PengadaanController::class, 'approve'])->whereNumber('id')->middleware('akses:pengadaan.approve');

    // Penerimaan
    Route::get('/penerimaan', [PenerimaanController::class, 'index'])->middleware('akses:penerimaan.read');
    Route::get('/penerimaan/create', [PenerimaanController::class, 'create'])->middleware('akses:penerimaan.create');
    Route::post('/penerimaan', [PenerimaanController::class, 'store'])->middleware('akses:penerimaan.create');
    Route::get('/penerimaan/{id}', [PenerimaanController::class, 'show'])->whereNumber('id')->middleware('akses:penerimaan.read');
// UPDATE PENERIMAAN
    Route::get('/penerimaan/{id}/edit',[PenerimaanController::class, 'edit'])->middleware('akses:penerimaan.update');
    Route::put('/penerimaan/{id}',[PenerimaanController::class, 'update'])->middleware('akses:penerimaan.update');

    // Retur
    Route::get('/retur', [ReturController::class, 'index'])->middleware('akses:retur.read');
    Route::get('/retur/create', [ReturController::class, 'create'])->middleware('akses:retur.create');
    Route::post('/retur', [ReturController::class, 'store'])->middleware('akses:retur.create');

    // Kartu stok: hanya baca
    Route::get('/kartu-stok', [KartuStokController::class, 'index'])->middleware('akses:kartu_stok.read');
});

// ---------- Halaman uji (manager) ----------
Route::get('/uji-sp-function', [UjiSpFunctionController::class, 'index'])->middleware('akses:uji.read');
Route::get('/uji-trigger', [UjiTriggerController::class, 'index'])->middleware('akses:uji.read');
Route::post('/uji-trigger', [UjiTriggerController::class, 'index'])->middleware('akses:uji.read');

