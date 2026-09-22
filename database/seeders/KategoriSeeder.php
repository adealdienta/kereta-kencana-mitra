<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        Kategori::updateOrCreate(
            ['slug' => 'skm'],
            [
                'nama_kategori' => 'Sigaret Kretek Mesin (SKM)',
                'deskripsi' => 'Rokok kretek yang diproduksi menggunakan mesin modern berstandar tinggi dengan tarikan halus dan filter presisi.',
            ]
        );

        Kategori::updateOrCreate(
            ['slug' => 'skt'],
            [
                'nama_kategori' => 'Sigaret Kretek Tangan (SKT)',
                'deskripsi' => 'Kretek linting tangan tradisional dengan racikan tembakau asli dan cengkeh pilihan warisan nusantara.',
            ]
        );
    }
}
