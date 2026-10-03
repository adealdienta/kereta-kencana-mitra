<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;

class TransaksiSeeder extends Seeder
{
    /**
     * Seeder transaksi sengaja dikosongkan agar sistem bersih
     * dan siap digunakan untuk transaksi riil dari mitra toko.
     */
    public function run(): void
    {
        // Kosongkan transaksi jika dijalankan
        Transaksi::truncate();
    }
}
