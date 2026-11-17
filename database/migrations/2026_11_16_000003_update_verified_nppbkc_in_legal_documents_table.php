<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('legal_documents')->whereIn('nomor', [
            '0821.1.2.XXXXX',
            '9120001234567',
            '503/IUI/TMBK/2021',
            'IDM000987654',
        ])->delete();

        $exists = DB::table('legal_documents')
            ->where('nomor', '750******-070700-221221*******')
            ->exists();

        if (!$exists) {
            DB::table('legal_documents')->insert([
                'nama_dokumen' => 'NPPBKC (Nomor Pokok Pengusaha Barang Kena Cukai)',
                'nomor' => '750******-070700-221221*******',
                'penerbit' => 'KPPBC Tipe Madya Pabean C Blitar (Ditjen Bea dan Cukai)',
                'berlaku_sampai' => null,
                'status' => 'Aktif',
                'catatan' => 'Pengusaha Pabrik Hasil Tembakau (Ditetapkan 16 Agustus 2022). Sebagian digit disensor untuk mencegah penyalahgunaan/pemalsuan.',
                'urutan' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('legal_documents')
            ->where('nomor', '750******-070700-221221*******')
            ->delete();
    }
};
