<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barangs';

    protected $fillable = [
        'kategori_id',
        'kode_barang',
        'nama',
        'slug',
        'deskripsi',
        'profil_rasa',
        'tar_mg',
        'nikotin_mg',
        'batang_per_bungkus',
        'bungkus_per_slop',
        'slop_per_bal',
        'harga_per_bal',
        'harga_per_slop',
        'min_order_bal',
        'min_order_slop',
        'stok',
        'status_stok',
        'gambar',
        'aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'harga_per_bal' => 'decimal:2',
            'harga_per_slop' => 'decimal:2',
            'tar_mg' => 'decimal:1',
            'nikotin_mg' => 'decimal:1',
            'batang_per_bungkus' => 'integer',
            'bungkus_per_slop' => 'integer',
            'slop_per_bal' => 'integer',
            'min_order_bal' => 'integer',
            'min_order_slop' => 'integer',
            'stok' => 'integer',
            'urutan' => 'integer',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'barang_id');
    }

    public function usersFavorit(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'barang_user', 'barang_id', 'user_id')->withTimestamps();
    }

    public function scopeAktif($query)
    {
        return $query->where('aktif', true);
    }

    public static function hargaSlopByQty(int $totalSlop): float
    {
        if ($totalSlop > 10) {
            return 60000.0;
        }
        if ($totalSlop >= 6) {
            return 65000.0;
        }
        return 70000.0;
    }

    public function getHargaPerSlopAttribute(): float
    {
        $val = (float)($this->attributes['harga_per_slop'] ?? 0);
        if ($val > 0) {
            return $val;
        }
        $slopPerBal = $this->slop_per_bal ?: 20;
        return round((float)$this->harga_per_bal / $slopPerBal);
    }

    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->harga_per_bal, 0, ',', '.');
    }

    public function getFormattedHargaSlopAttribute(): string
    {
        return 'Rp ' . number_format((float)$this->harga_per_slop, 0, ',', '.');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->gambar) {
            if (file_exists(public_path($this->gambar))) {
                return asset($this->gambar);
            }
            if (file_exists(public_path('storage/' . $this->gambar))) {
                return asset('storage/' . $this->gambar);
            }
            return asset($this->gambar);
        }

        // Pemetaan otomatis berdasarkan slug produk resmi PR. Kereta Kencana
        $slugMap = [
            'sembada-12' => 'assets/img/produk/sembada-12.jpg',
            'kereta-kencana-12' => 'assets/img/produk/kereta-kencana-12.jpg',
            'sembada-cethe-12' => 'assets/img/produk/sembada-cethe-12.jpg',
        ];

        if (!empty($this->slug) && isset($slugMap[$this->slug])) {
            return asset($slugMap[$this->slug]);
        }

        return asset('assets/img/logo-resmi.png');
    }

    /**
     * Foto Produk Resmi (1 Foto Mewakili 1 Produk)
     */
    public function getGalleryImagesAttribute(): array
    {
        return [
            [
                'url' => $this->image_url,
                'label' => $this->nama,
                'badge' => 'Produk Resmi',
            ]
        ];
    }
}
