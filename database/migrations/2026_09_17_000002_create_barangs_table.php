<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->string('kode_barang', 50)->unique();
            $table->string('nama', 150);
            $table->string('slug', 150)->unique();
            $table->text('deskripsi')->nullable();
            $table->string('profil_rasa')->nullable();
            $table->decimal('tar_mg', 5, 1)->nullable();
            $table->decimal('nikotin_mg', 5, 1)->nullable();
            $table->integer('batang_per_bungkus')->default(12);
            $table->integer('bungkus_per_slop')->default(10);
            $table->integer('slop_per_bal')->default(20);
            $table->decimal('harga_per_bal', 14, 2)->default(0);
            $table->decimal('harga_per_slop', 14, 2)->nullable();
            $table->integer('min_order_bal')->default(1);
            $table->integer('min_order_slop')->default(1);
            $table->integer('stok')->default(100);
            $table->string('status_stok', 50)->default('Ready Stock');
            $table->string('gambar')->nullable();
            $table->boolean('aktif')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
