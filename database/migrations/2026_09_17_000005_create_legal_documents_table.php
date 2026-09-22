<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dokumen', 200);
            $table->string('nomor', 100)->nullable();
            $table->string('penerbit', 150)->nullable();
            $table->date('berlaku_sampai')->nullable();
            $table->string('status', 50)->default('Aktif');
            $table->text('catatan')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
