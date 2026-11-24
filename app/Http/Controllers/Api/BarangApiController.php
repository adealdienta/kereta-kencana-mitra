<?php

namespace App\Http\Controllers\Api;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangApiController extends ApiController
{
    /**
     * Daftar Seluruh Katalog Produk Rokok Aktif
     * GET /api/v1/barang
     */
    public function index(Request $request)
    {
        $query = Barang::with('kategori')
            ->aktif()
            ->orderBy('urutan')
            ->orderBy('id');

        if ($request->filled('kategori')) {
            $kat = trim((string) $request->query('kategori'));
            $query->whereHas('kategori', function ($q) use ($kat) {
                $q->where('slug', $kat)->orWhere('nama_kategori', $kat);
            });
        }

        $barangs = $query->get();

        $data = $barangs->map(function ($b) {
            return $this->formatBarang($b);
        });

        return $this->successResponse($data, 'Katalog produk rokok berhasil dimuat.');
    }

    /**
     * Rincian Varian Produk Berdasarkan ID
     * GET /api/v1/barang/{id}
     */
    public function show($id)
    {
        $barang = Barang::with('kategori')->aktif()->find($id);

        if (!$barang) {
            return $this->errorResponse('Produk rokok tidak ditemukan atau sudah tidak aktif.', 404);
        }

        return $this->successResponse($this->formatBarang($barang), 'Detail produk berhasil dimuat.');
    }

    /**
     * Format data barang untuk konsumsi seragam aplikasi mobile
     */
    protected function formatBarang(Barang $b): array
    {
        $slopPerBal = $b->slop_per_bal ?: 20;

        return [
            'id' => $b->id,
            'kategori_id' => $b->kategori_id,
            'kategori_nama' => $b->kategori?->nama_kategori ?? 'Sigaret Kretek',
            'kode_barang' => $b->kode_barang,
            'nama' => $b->nama,
            'slug' => $b->slug,
            'deskripsi' => $b->deskripsi,
            'profil_rasa' => $b->profil_rasa,
            'tar_mg' => $b->tar_mg !== null ? (float)$b->tar_mg : null,
            'nikotin_mg' => $b->nikotin_mg !== null ? (float)$b->nikotin_mg : null,
            'batang_per_bungkus' => (int)$b->batang_per_bungkus,
            'bungkus_per_slop' => (int)$b->bungkus_per_slop,
            'slop_per_bal' => (int)$slopPerBal,
            'harga_per_slop' => (float)$b->harga_per_slop,
            'harga_per_bal' => (float)$b->harga_per_bal,
            'min_order_slop' => (int)($b->min_order_slop ?: 1),
            'min_order_bal' => (int)($b->min_order_bal ?: 1),
            'stok' => (int)$b->stok, // Stok presisi gudang dalam satuan Slop
            'stok_bal' => (int)$b->stok_bal_utuh, // Jumlah Bal utuh setara
            'stok_slop_estimasi' => (int)$b->stok, // Kompatibilitas jumlah Slop tersedia
            'status_stok' => ($b->stok > 0 && strcasecmp((string) $b->status_stok, 'Habis') !== 0)
                ? ($b->status_stok ?: 'Ready Stock')
                : 'Habis',
            'aktif' => (bool)$b->aktif,
            'best_seller' => (bool)$b->best_seller,
            'image_url' => $b->image_url,
            'gallery_images' => $b->gallery_images,
        ];
    }
}
