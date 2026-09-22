<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LegalDocument;

class LegalitasSeeder extends Seeder
{
    public function run(): void
    {
        $docs = [
            [
                'nama_dokumen' => 'NPPBKC (Nomor Pokok Pengusaha Barang Kena Cukai)',
                'nomor' => '0821.1.2.XXXXX',
                'penerbit' => 'KPPBC Tipe Madya Pabean C Blitar',
                'berlaku_sampai' => '2028-12-31',
                'status' => 'Aktif',
                'catatan' => 'Izin pita cukai resmi dari Direktorat Jenderal Bea dan Cukai untuk produksi tembakau.',
                'urutan' => 1,
            ],
            [
                'nama_dokumen' => 'NIB (Nomor Induk Berusaha)',
                'nomor' => '9120001234567',
                'penerbit' => 'Kementerian Investasi / BKPM RI',
                'berlaku_sampai' => '2030-01-01',
                'status' => 'Aktif',
                'catatan' => 'Identitas berusaha terintegrasi OSS RBA sektor industri pengolahan tembakau.',
                'urutan' => 2,
            ],
            [
                'nama_dokumen' => 'Izin Usaha Industri (IUI)',
                'nomor' => '503/IUI/TMBK/2021',
                'penerbit' => 'DPMPTSP Kabupaten Blitar',
                'berlaku_sampai' => '2029-06-30',
                'status' => 'Aktif',
                'catatan' => 'Izin operasional pabrik sigaret kretek di kawasan industri Ponggok, Kabupaten Blitar.',
                'urutan' => 3,
            ],
            [
                'nama_dokumen' => 'Sertifikat Merek Dagang Kereta Kencana',
                'nomor' => 'IDM000987654',
                'penerbit' => 'DJKI Kementerian Hukum dan HAM RI',
                'berlaku_sampai' => '2032-10-15',
                'status' => 'Aktif',
                'catatan' => 'Perlindungan hak kekayaan intelektual merek dagang kelas barang 34.',
                'urutan' => 4,
            ],
        ];

        foreach ($docs as $d) {
            LegalDocument::updateOrCreate(['nomor' => $d['nomor']], $d);
        }
    }
}
