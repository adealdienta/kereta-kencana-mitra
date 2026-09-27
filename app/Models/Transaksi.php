<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'bukti_penerimaan',
        'diterima_pada',
        'catatan_penerima',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'jumlah' => 'integer',
            'diterima_pada' => 'datetime',
        ];
    }

    /**
     * Relasi ke rincian item produk (Multi-Item)
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransaksiDetail::class, 'transaksi_id');
    }

    public function items(): HasMany
    {
        return $this->details();
    }

    /**
     * Relasi ke barang (Backward compatibility jika ada transaksi tunggal)
     */
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

    /**
     * Menghasilkan teks ringkasan varian produk yang dipesan
     * Contoh: "1 Bal Sembada 12, 1 Slop Kereta Kencana 12"
     */
    public function getRingkasanItemAttribute(): string
    {
        if ($this->details && $this->details->isNotEmpty()) {
            return $this->details->map(function ($d) {
                $nama = $d->barang?->nama ?? 'Produk';
                return "{$d->jumlah} {$d->satuan} {$nama}";
            })->implode(', ');
        }

        if ($this->barang) {
            return "{$this->jumlah} {$this->satuan} {$this->barang->nama}";
        }

        return 'Pesanan Multi-Item';
    }

    /**
     * Total bungkus rokok keseluruhan dari seluruh item
     */
    public function getTotalBungkusAttribute(): int
    {
        if ($this->details && $this->details->isNotEmpty()) {
            return $this->details->sum(function ($d) {
                return $d->total_bungkus;
            });
        }

        $slopPerBal = $this->barang?->slop_per_bal ?: 20;
        $bungkusPerSlop = $this->barang?->bungkus_per_slop ?: 10;
        if ($this->satuan === 'Bal') {
            return $this->jumlah * $slopPerBal * $bungkusPerSlop;
        }
        return $this->jumlah * $bungkusPerSlop;
    }
}
