<?php

namespace App\Http\Controllers\Api;

use App\Models\Barang;

class BarangApiController extends ApiController
{
    /**
     * Daftar Seluruh Katalog Produk Rokok Aktif
     * GET /api/v1/barang
     */
    public function index()
    {
        $barangs = Barang::with('kategori')
            ->aktif()
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

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
            'tar_mg' => (float)$b->tar_mg,
            'nikotin_mg' => (float)$b->nikotin_mg,
            'batang_per_bungkus' => (int)$b->batang_per_bungkus,
            'bungkus_per_slop' => (int)$b->bungkus_per_slop,
            'slop_per_bal' => (int)$slopPerBal,
            'harga_per_slop' => (float)$b->harga_per_slop,
            'harga_per_bal' => (float)$b->harga_per_bal,
            'min_order_slop' => (int)($b->min_order_slop ?: 1),
            'min_order_bal' => (int)($b->min_order_bal ?: 1),
            'stok' => (int)$b->stok, // Stok aktual gudang dalam satuan Bal
            'stok_slop_estimasi' => (int)($b->stok * $slopPerBal), // Perkiraan slop untuk panduan mitra
            'status_stok' => $b->stok > 0 ? ($b->status_stok ?: 'Ready Stock') : 'Habis',
            'image_url' => $b->image_url,
            'gallery_images' => $b->gallery_images,
        ];
    }
}
