<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;

class TransaksiSeeder extends Seeder
{
    /**
     * Seeder transaksi sengaja dikosongkan agar sistem bersih
     * dan siap digunakan untuk transaksi riil dari mitra toko.
     */
    public function run(): void
    {
        // Hapus aman sesuai urutan foreign key tanpa truncate() yang gagal di MySQL
        TransaksiDetail::query()->delete();
        Transaksi::query()->delete();
    }
}
