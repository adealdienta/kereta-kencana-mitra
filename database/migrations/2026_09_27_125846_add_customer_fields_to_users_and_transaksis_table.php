<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Kolom profil mitra untuk tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('nama_toko', 150)->nullable()->after('name');
            $table->string('telepon', 30)->nullable()->after('email');
            $table->text('alamat')->nullable()->after('telepon');
        });

        // 2. Kolom bukti penerimaan & penyelarasan status tabel transaksis
        Schema::table('transaksis', function (Blueprint $table) {
            // Ubah tipe kolom status menjadi string agar fleksibel mendukung status baru
            $table->string('status', 50)->default('Baru Masuk')->change();
            $table->string('bukti_penerimaan')->nullable()->after('status');
            $table->timestamp('diterima_pada')->nullable()->after('bukti_penerimaan');
            $table->text('catatan_penerima')->nullable()->after('diterima_pada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['bukti_penerimaan', 'diterima_pada', 'catatan_penerima']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nama_toko', 'telepon', 'alamat']);
        });
    }
};
