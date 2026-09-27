<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\TransaksiController;
use App\Http\Controllers\Admin\LegalitasController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ActivityLogController;

/*
|--------------------------------------------------------------------------
| Web Routes - PR. KERETA KENCANA (Sesuai Kurikulum BKPM Web 2)
|--------------------------------------------------------------------------
*/

// --- 1. RUTE PUBLIK ---
Route::get('/', [FrontController::class, 'beranda'])->name('beranda');
Route::get('/profil', [FrontController::class, 'profil'])->name('profil');
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/produk/{slug}', [KatalogController::class, 'detail'])->name('produk.detail');
Route::get('/kontak', [FrontController::class, 'kontak'])->name('kontak');
Route::post('/kontak', [FrontController::class, 'kirimPesan'])->name('kontak.kirim');

// API Endpoint AJAX pencatatan WhatsApp Modal (BKPM Acara 21)
Route::post('/api/log-order-wa', [PemesananController::class, 'apiLogWa'])->name('api.log.wa');

// --- 2. RUTE AUTENTIKASI (LOGIN & REGISTER MITRA) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- 3. RUTE PEMESANAN & PELACAKAN (WAJIB AUTHENTIKASI MITRA) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/pesan', [PemesananController::class, 'form'])->name('pesanan.form');
    Route::post('/pesan', [PemesananController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan-saya', [PemesananController::class, 'pelacakan'])->name('pesanan.saya');
    Route::post('/pesanan/{id}/konfirmasi-terima', [PemesananController::class, 'konfirmasiTerima'])->name('pesanan.konfirmasi');
    Route::post('/pesanan/{id}/batal', [PemesananController::class, 'batalkanPesanan'])->name('pesanan.batal');
});

// Halaman Invoice Digital (Bisa dibuka pelanggan/staf via link kode transaksi)
Route::get('/invoice/{kode_transaksi}', [PemesananController::class, 'invoice'])->name('pesanan.invoice');

// --- 4. RUTE DASHBOARD OPERASIONAL INTERNAL (ROLE STAFF, ADMIN, OWNER) ---
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Transaksi Offline / Kasir Langsung di Pabrik
    Route::get('/transaksis/offline', [TransaksiController::class, 'createOffline'])->name('transaksis.offline');
    Route::post('/transaksis/offline', [TransaksiController::class, 'storeOffline'])->name('transaksis.store_offline');
    Route::post('/transaksis/{id}/verifikasi-manual', [TransaksiController::class, 'verifikasiManual'])->name('transaksis.verifikasi_manual');

    // Transaksi & Pesanan Distributor (Acara 17, 18, 23)
    Route::get('/transaksis', [TransaksiController::class, 'index'])->name('transaksis.index');
    Route::get('/transaksis/{id}', [TransaksiController::class, 'show'])->name('transaksis.show');
    Route::put('/transaksis/{id}', [TransaksiController::class, 'update'])->name('transaksis.update');
    Route::delete('/transaksis/{id}', [TransaksiController::class, 'destroy'])->name('transaksis.destroy');
    Route::get('/transaksis/{id}/faktur', [TransaksiController::class, 'cetakFaktur'])->name('transaksis.faktur');

    // CRUD Produk / Barang Rokok (Acara 15, 16)
    Route::resource('barangs', BarangController::class);

    // CRUD Kategori Produk (Acara 13, 14)
    Route::resource('kategoris', KategoriController::class);

    // Dokumen Perizinan & Legalitas Cukai
    Route::get('/legalitas', [LegalitasController::class, 'index'])->name('legalitas.index');
    Route::post('/legalitas', [LegalitasController::class, 'store'])->name('legalitas.store');
    Route::put('/legalitas/{id}', [LegalitasController::class, 'update'])->name('legalitas.update');
    Route::delete('/legalitas/{id}', [LegalitasController::class, 'destroy'])->name('legalitas.destroy');

    // Fitur Khusus Role Owner & Super Admin (Acara 9 - 10)
    Route::middleware(['role:owner,superadmin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });
});
