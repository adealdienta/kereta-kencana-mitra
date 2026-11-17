<?php

namespace App\Http\Controllers\Api;

class PengaturanApiController extends ApiController
{
    /**
     * Dapatkan Konfigurasi Resmi Pabrik (WA Pabrik & NPPBKC)
     * GET /api/v1/pengaturan
     */
    public function index()
    {
        return $this->successResponse([
            'wa_pabrik' => config('pabrik.wa_pabrik'),
            'nppbkc' => config('pabrik.nppbkc'),
            'bank_nama' => config('pabrik.bank_nama'),
            'bank_rekening' => config('pabrik.bank_rekening'),
            'bank_atas_nama' => config('pabrik.bank_atas_nama'),
        ], 'Konfigurasi pabrik berhasil dimuat.');
    }
}
