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
                'name' => 'Bapak Komari Yaman (Owner)',
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

        // 3. Role Staff Operasional
        User::updateOrCreate(
            ['email' => 'staff@keretakencana.com'],
            [
                'name' => 'Staf Operasional Pabrik',
                'password' => $defaultPassword,
                'role' => 'staff',
            ]
        );

        // 4. Akun Admin Demo (Kompatibilitas Login Lama)
        User::updateOrCreate(
            ['email' => 'admin@keretakencana.com'],
            [
                'name' => 'Admin Gudang & Pemesanan',
                'password' => $defaultPassword,
                'role' => 'staff',
            ]
        );
    }
}
