<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaksiDetail extends Model
{
    use HasFactory;

    protected $table = 'transaksi_details';

    protected $fillable = [
        'transaksi_id',
        'barang_id',
        'satuan',
        'jumlah',
        'harga_satuan',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_satuan' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function transaksi(): BelongsTo
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function getFormattedHargaSatuanAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->harga_satuan, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->subtotal, 0, ',', '.');
    }

    /**
     * Hitung perkiraan total bungkus untuk item ini
     */
    public function getTotalBungkusAttribute(): int
    {
        $bungkusPerSlop = $this->barang?->bungkus_per_slop ?: 10;
        $slopPerBal = $this->barang?->slop_per_bal ?: 20;

        if ($this->satuan === 'Bal') {
            return $this->jumlah * $slopPerBal * $bungkusPerSlop;
        }

        return $this->jumlah * $bungkusPerSlop;
    }

    /**
     * Hitung total slop untuk item ini
     */
    public function getTotalSlopAttribute(): int
    {
        $slopPerBal = $this->barang?->slop_per_bal ?: 20;

        if ($this->satuan === 'Bal') {
            return $this->jumlah * $slopPerBal;
        }

        return $this->jumlah;
    }
}
