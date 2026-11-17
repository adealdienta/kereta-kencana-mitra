<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('barangs')->update([
            'harga_per_slop' => 70000,
            'harga_per_bal' => 1200000,
        ]);
    }

    public function down(): void
    {
        // Biarkan data harga saat ini tetap utuh saat rollback
    }
};
