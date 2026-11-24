<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. Role Owner
        User::updateOrCreate(
            ['email' => 'owner@keretakencana.com'],
            [
                'name' => 'Owner PR. Kereta Kencana',
                'password' => $defaultPassword,
                'role' => 'owner',
            ]
        );

        // 2. Role Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@keretakencana.com'],
            [
                'name' => 'Super Admin (Web Dev)',
                'password' => $defaultPassword,
                'role' => 'superadmin',
            ]
        );

        // 3. Role Admin (Admin Gudang & Pemesanan)
        User::updateOrCreate(
            ['email' => 'admin@keretakencana.com'],
            [
                'name' => 'Admin Gudang & Pemesanan',
                'password' => $defaultPassword,
                'role' => 'admin',
            ]
        );

        // 4. Akun Demo Mitra / Pelanggan (untuk pengujian Web & Mobile)
        User::updateOrCreate(
            ['email' => 'mitra@keretakencana.com'],
            [
                'name' => 'Mitra Demo Toko',
                'nama_toko' => 'Toko Mitra Jaya',
                'telepon' => '+6285200001111',
                'alamat' => 'Jl. Raya Kebonduren No. 10, Ponggok, Kab. Blitar',
                'password' => $defaultPassword,
                'role' => 'pelanggan',
            ]
        );
    }
}
