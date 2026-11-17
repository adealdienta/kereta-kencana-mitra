<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BarangApiController;
use App\Http\Controllers\Api\PesananApiController;
use App\Http\Controllers\Api\PengaturanApiController;

/*
|--------------------------------------------------------------------------
| API Routes - PR. KERETA KENCANA (Integrasi Aplikasi Mobile Mitra B2B)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // --- 1. ENDPOINT PUBLIK ---
    Route::get('/ping', function () {
        return response()->json([
            'success' => true,
            'message' => 'Layanan API PR. KERETA KENCANA aktif.',
            'data' => [
                'status' => 'online',
                'app' => 'PR. KERETA KENCANA B2B API',
                'version' => '1.0.0',
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    });

    // Konfigurasi Resmi Pabrik (WA Pabrik & NPPBKC)
    Route::get('/pengaturan', [PengaturanApiController::class, 'index']);

    // Katalog Produk Rokok
    Route::get('/barang', [BarangApiController::class, 'index']);
    Route::get('/barang/{id}', [BarangApiController::class, 'show']);

    // Autentikasi Publik dengan Rate Limiting
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/register', [AuthApiController::class, 'register']);
        Route::post('/login', [AuthApiController::class, 'login']);
        Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
        Route::post('/verify-reset-token', [AuthApiController::class, 'verifyToken']);
        Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);
    });

    // --- 2. ENDPOINT TERAUTENTIKASI (Bearer Token Sanctum - Khusus Role Pelanggan) ---
    Route::middleware(['auth:sanctum', 'role.pelanggan'])->group(function () {
        // Sesi Akun & Profil
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/profile', [AuthApiController::class, 'profile']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::post('/profile/foto', [AuthApiController::class, 'uploadFotoProfil']);

        // Pemesanan & Alur Order
        Route::get('/pesanan/status-kunci', [PesananApiController::class, 'statusKunci']);
        Route::get('/pesanan', [PesananApiController::class, 'index']);
        Route::get('/pesanan/{id}', [PesananApiController::class, 'show']);
        Route::post('/pesanan', [PesananApiController::class, 'store']);
        Route::post('/pesanan/{id}/batal', [PesananApiController::class, 'batalkan']);
        Route::post('/pesanan/{id}/konfirmasi-terima', [PesananApiController::class, 'konfirmasiTerima']);
        Route::post('/pesanan/{id}/upload-bukti-transfer', [PesananApiController::class, 'uploadBuktiTransfer']);
    });
});
