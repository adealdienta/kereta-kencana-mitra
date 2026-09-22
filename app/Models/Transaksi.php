<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'barang_id',
        'nama_mitra',
        'telepon',
        'alamat',
        'jumlah',
        'satuan',
        'total_harga',
        'catatan',
        'status',
        'nomor_do',
        'nomor_resi',
        'sumber',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'jumlah' => 'integer',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function produk(): BelongsTo
    {
        return $this->barang();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->total_harga, 0, ',', '.');
    }
}
