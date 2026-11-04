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
            'bank_nama' => config('pabrik.bank_nama', 'BCA (Bank Central Asia)'),
            'bank_rekening' => config('pabrik.bank_rekening', '8220918822'),
            'bank_atas_nama' => config('pabrik.bank_atas_nama', 'PR KERETA KENCANA'),
        ], 'Konfigurasi pabrik berhasil dimuat.');
    }
}
