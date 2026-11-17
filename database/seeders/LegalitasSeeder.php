<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalDocument;

class LegalitasSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data dummy lama yang nomornya belum terverifikasi
        LegalDocument::whereIn('nomor', [
            '0821.1.2.XXXXX',
            '9120001234567',
            '503/IUI/TMBK/2021',
            'IDM000987654',
        ])->delete();

        $docs = [
            [
                'nama_dokumen' => 'NPPBKC (Nomor Pokok Pengusaha Barang Kena Cukai)',
                'nomor' => '750******-070700-221221*******',
                'penerbit' => 'KPPBC Tipe Madya Pabean C Blitar (Ditjen Bea dan Cukai)',
                'berlaku_sampai' => null,
                'status' => 'Aktif',
                'catatan' => 'Pengusaha Pabrik Hasil Tembakau (Ditetapkan 16 Agustus 2022). Sebagian digit disensor untuk mencegah penyalahgunaan/pemalsuan.',
                'urutan' => 1,
            ],
        ];

        foreach ($docs as $d) {
            LegalDocument::updateOrCreate(['nomor' => $d['nomor']], $d);
        }
    }
}
