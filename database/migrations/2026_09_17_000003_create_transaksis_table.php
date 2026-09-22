<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->string('nama_mitra', 150);
            $table->string('telepon', 30);
            $table->text('alamat');
            $table->integer('jumlah')->default(1);
            $table->string('satuan', 20)->default('Bal');
            $table->decimal('total_harga', 14, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['Baru Masuk', 'Diproses', 'Selesai', 'Dibatalkan'])->default('Baru Masuk');
            $table->string('nomor_do', 100)->nullable();
            $table->string('nomor_resi', 100)->nullable();
            $table->string('sumber', 50)->default('Web B2B');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
