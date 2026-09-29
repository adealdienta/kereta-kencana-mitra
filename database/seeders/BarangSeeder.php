<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barang;
use App\Models\Kategori;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $skt = Kategori::where('slug', 'skt')->first();

        // 3 Produk Resmi Pabrik PR. Kereta Kencana (Sigaret Kretek Tangan / SKT 12 Batang)
        $barangs = [
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-SM12',
                'nama' => 'SEMBADA 12',
                'slug' => 'sembada-12',
                'deskripsi' => 'Sigaret Kretek Tangan Sembada 12 batang dengan racikan tembakau tradisi luhur Blitar, berkarakter mantap, dan aroma gurih klasik untuk pecinta kretek sejati.',
                'profil_rasa' => 'Klasik, berbobot, aroma tembakau matang dan rempah pilihan',
                'tar_mg' => 32.0,
                'nikotin_mg' => 2.1,
                'batang_per_bungkus' => 12,
                'bungkus_per_slop' => 10,
                'slop_per_bal' => 20,
                'harga_per_bal' => 950000,
                'harga_per_slop' => 47500,
                'min_order_bal' => 1,
                'min_order_slop' => 1,
                'stok' => 220,
                'status_stok' => 'Ready Stock',
                'gambar' => 'assets/img/produk/sembada-12.jpg',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-KK12',
                'nama' => 'KERETA KENCANA 12',
                'slug' => 'kereta-kencana-12',
                'deskripsi' => 'Sigaret Kretek Tangan Kereta Kencana 12, kretek unggulan mahakarya lintingan tangan terlatih dari pabrik Ponggok Blitar dalam balutan kemasan merah emas yang elegan.',
                'profil_rasa' => 'Full flavour, kaya rempah, pembakaran halus dan nikmat',
                'tar_mg' => 30.0,
                'nikotin_mg' => 2.0,
                'batang_per_bungkus' => 12,
                'bungkus_per_slop' => 10,
                'slop_per_bal' => 20,
                'harga_per_bal' => 1150000,
                'harga_per_slop' => 57500,
                'min_order_bal' => 1,
                'min_order_slop' => 1,
                'stok' => 250,
                'status_stok' => 'Ready Stock',
                'gambar' => 'assets/img/produk/kereta-kencana-12.jpg',
                'aktif' => true,
                'urutan' => 2,
            ],
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-SC12',
                'nama' => 'SEMBADA Cethe 12',
                'slug' => 'sembada-cethe-12',
                'deskripsi' => 'Sigaret Kretek Tangan Sembada Cethe 12 batang dengan racikan istimewa khas tradisi nyethe / cangkrukan Jawa Timur, beraroma harum dan tarikan mantap.',
                'profil_rasa' => 'Khas aroma cethe kopi, manis gurih seimbang, tarikan mantap',
                'tar_mg' => 31.0,
                'nikotin_mg' => 2.0,
                'batang_per_bungkus' => 12,
                'bungkus_per_slop' => 10,
                'slop_per_bal' => 20,
                'harga_per_bal' => 1050000,
                'harga_per_slop' => 52500,
                'min_order_bal' => 1,
                'min_order_slop' => 1,
                'stok' => 200,
                'status_stok' => 'Ready Stock',
                'gambar' => 'assets/img/produk/sembada-cethe-12.jpg',
                'aktif' => true,
                'urutan' => 3,
            ],
        ];

        // Hapus produk lama yang tidak terpakai
        $activeSlugs = array_column($barangs, 'slug');
        Barang::whereNotIn('slug', $activeSlugs)->delete();

        // Simpan / update data 3 produk resmi
        foreach ($barangs as $b) {
            Barang::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
