<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->boolean('best_seller')->default(false)->after('aktif');
        });

        DB::table('barangs')
            ->where('slug', 'sembada-12')
            ->orWhere('kode_barang', 'SKT-SM12')
            ->update(['best_seller' => true]);
    }

    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropColumn('best_seller');
        });
    }
};
