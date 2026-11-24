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
use App\Http\Controllers\Admin\PengaturanController;

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

// --- 2. RUTE AUTENTIKASI (LOGIN & REGISTER MITRA) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:20,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Mandiri Lupa & Atur Ulang Kata Sandi Mitra
Route::get('/lupa-sandi', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/lupa-sandi', [AuthController::class, 'sendResetLinkEmail'])->middleware('throttle:15,1')->name('password.email');
Route::get('/reset-sandi/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-sandi', [AuthController::class, 'resetPassword'])->middleware('throttle:15,1')->name('password.update');

// --- 3. RUTE PEMESANAN & PELACAKAN (KHUSUS MITRA TOKO / PELANGGAN) ---
Route::middleware(['auth', 'role:pelanggan'])->group(function () {
    Route::get('/pesan', [PemesananController::class, 'form'])->name('pesanan.form');
    Route::post('/pesan', [PemesananController::class, 'store'])->name('pesanan.store');
    Route::get('/pesanan-saya', [PemesananController::class, 'pelacakan'])->name('pesanan.saya');
    Route::put('/profil-mitra', [AuthController::class, 'updateProfileMitra'])->name('mitra.profile.update');
    Route::post('/pesanan/{id}/konfirmasi-terima', [PemesananController::class, 'konfirmasiTerima'])->name('pesanan.konfirmasi');
    Route::post('/pesanan/{id}/upload-bukti-transfer', [PemesananController::class, 'uploadBuktiTransfer'])->name('pesanan.upload_transfer');
    Route::post('/pesanan/{id}/batal', [PemesananController::class, 'batalkanPesanan'])->name('pesanan.batal');
});

// Halaman Invoice Digital (Hanya pemilik pesanan atau staf internal)
Route::middleware(['auth'])->get('/invoice/{kode_transaksi}', [PemesananController::class, 'invoice'])->name('pesanan.invoice');

// --- 4. RUTE DASHBOARD OPERASIONAL INTERNAL (ROLE ADMIN, OWNER, SUPERADMIN) ---
Route::middleware(['auth', 'role:admin,owner,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Daftar Mitra Toko (Dapat diakses oleh Staf/Admin Gudang, Super Admin, Owner)
    Route::get('/mitra', [UserController::class, 'mitraIndex'])->name('mitra.index');
    Route::post('/mitra/{id}/kirim-reset-link', [UserController::class, 'kirimResetLinkMitra'])->name('mitra.kirim_reset_link');

    // Transaksi Offline / Kasir Langsung di Pabrik
    Route::get('/transaksis/offline', [TransaksiController::class, 'createOffline'])->name('transaksis.offline');
    Route::post('/transaksis/offline', [TransaksiController::class, 'storeOffline'])->name('transaksis.store_offline');

    // Transaksi & Pesanan Distributor (Acara 17, 18, 23)
    Route::get('/transaksis', [TransaksiController::class, 'index'])->name('transaksis.index');
    Route::get('/transaksis/{id}', [TransaksiController::class, 'show'])->name('transaksis.show');
    Route::put('/transaksis/{id}', [TransaksiController::class, 'update'])->name('transaksis.update');
    Route::post('/transaksis/{id}/verifikasi-pembayaran', [TransaksiController::class, 'verifikasiPembayaran'])->name('transaksis.verifikasi_pembayaran');
    Route::get('/transaksis/{id}/faktur', [TransaksiController::class, 'cetakFaktur'])->name('transaksis.faktur');

    // CRUD Produk / Barang Rokok (Acara 15, 16)
    Route::resource('barangs', BarangController::class)->except(['show']);

    // CRUD Kategori Produk (Acara 13, 14)
    Route::resource('kategoris', KategoriController::class)->except(['show']);

    // Dokumen Perizinan & Legalitas Cukai
    Route::get('/legalitas', [LegalitasController::class, 'index'])->name('legalitas.index');
    Route::post('/legalitas', [LegalitasController::class, 'store'])->name('legalitas.store');
    Route::put('/legalitas/{id}', [LegalitasController::class, 'update'])->name('legalitas.update');
    Route::delete('/legalitas/{id}', [LegalitasController::class, 'destroy'])->name('legalitas.destroy');

    // Pengaturan Website, Jam Operasional & Media Publik (Admin, Owner, Super Admin)
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/umum', [PengaturanController::class, 'updateUmum'])->name('pengaturan.umum');
    Route::post('/pengaturan/hero', [PengaturanController::class, 'storeHero'])->name('pengaturan.hero.store');
    Route::post('/pengaturan/media/{key}', [PengaturanController::class, 'updateMedia'])->name('pengaturan.media.update');
    Route::delete('/pengaturan/media/{key}', [PengaturanController::class, 'resetMedia'])->name('pengaturan.media.reset');

    // Fitur Khusus Role Owner & Super Admin (Acara 9 - 10)
    Route::middleware(['role:owner,superadmin'])->group(function () {
        // Verifikasi Manual Penerimaan Khusus Owner & Super Admin dengan Surat Jalan Fisik
        Route::post('/transaksis/{id}/verifikasi-manual', [TransaksiController::class, 'verifikasiManual'])->name('transaksis.verifikasi_manual');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
    });
});
