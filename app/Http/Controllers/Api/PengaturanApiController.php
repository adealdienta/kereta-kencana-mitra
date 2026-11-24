<?php

namespace App\Http\Controllers\Api;

use App\Services\PabrikConfigService;

class PengaturanApiController extends ApiController
{
    /**
     * Dapatkan Konfigurasi Resmi Pabrik (WA Pabrik, NPPBKC, & Bank)
     * GET /api/v1/pengaturan
     */
    public function index()
    {
        $bank = PabrikConfigService::bankInfo();

        return $this->successResponse([
            'nama_pabrik' => 'PR. KERETA KENCANA',
            'alamat_pabrik' => 'Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kecamatan Ponggok, Kabupaten Blitar, Jawa Timur 66153',
            'wa_pabrik' => PabrikConfigService::waPabrik(),
            'nppbkc' => PabrikConfigService::nppbkc(),
            'jam_operasional' => PabrikConfigService::jamOperasional(),
            'bank_nama' => $bank['nama'],
            'bank_rekening' => $bank['rekening'],
            'bank_atas_nama' => $bank['atas_nama'],
        ], 'Konfigurasi pabrik berhasil dimuat.');
    }
}
