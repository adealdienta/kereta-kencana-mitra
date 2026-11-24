<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('barangs')->orderBy('id')->get()->each(function ($barang) {
            $slopPerBal = max(1, (int) ($barang->slop_per_bal ?: 20));
            DB::table('barangs')
                ->where('id', $barang->id)
                ->update(['stok' => max(0, (int) $barang->stok) * $slopPerBal]);
        });
    }

    public function down(): void
    {
        DB::table('barangs')->orderBy('id')->get()->each(function ($barang) {
            $slopPerBal = max(1, (int) ($barang->slop_per_bal ?: 20));
            DB::table('barangs')
                ->where('id', $barang->id)
                ->update(['stok' => intdiv(max(0, (int) $barang->stok), $slopPerBal)]);
        });
    }
};
