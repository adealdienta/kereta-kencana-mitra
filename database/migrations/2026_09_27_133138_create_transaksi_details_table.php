<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel detail item transaksi (Master-Detail Multi-Item)
        Schema::create('transaksi_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete();
            $table->string('satuan', 20)->default('Slop'); // 'Slop' atau 'Bal'
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->timestamps();
        });

        // 2. Buat kolom barang_id pada tabel transaksis menjadi nullable (karena barang dipecah ke detail)
        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable()->change();
        });

        // 3. Migrasikan data transaksi lama ke tabel detail agar tidak ada data yang hilang
        $oldTransaksis = DB::table('transaksis')->get();
        foreach ($oldTransaksis as $trx) {
            if ($trx->barang_id) {
                $barang = DB::table('barangs')->where('id', $trx->barang_id)->first();
                $hargaSatuan = 0;
                if ($barang) {
                    $hargaSatuan = ($trx->satuan === 'Slop') ? $barang->harga_per_slop : $barang->harga_per_bal;
                }
                if ($hargaSatuan == 0 && $trx->jumlah > 0) {
                    $hargaSatuan = $trx->total_harga / $trx->jumlah;
                }

                DB::table('transaksi_details')->insert([
                    'transaksi_id' => $trx->id,
                    'barang_id' => $trx->barang_id,
                    'satuan' => $trx->satuan ?: 'Slop',
                    'jumlah' => $trx->jumlah ?: 1,
                    'harga_satuan' => $hargaSatuan,
                    'subtotal' => $trx->total_harga ?: 0,
                    'created_at' => $trx->created_at ?: now(),
                    'updated_at' => $trx->updated_at ?: now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_details');

        Schema::table('transaksis', function (Blueprint $table) {
            $table->foreignId('barang_id')->nullable(false)->change();
        });
    }
};
