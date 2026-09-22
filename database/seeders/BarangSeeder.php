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

        // 5 Produk Resmi Pabrik (Semuanya Sigaret Kretek Tangan / SKT isi 12 batang)
        $barangs = [
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-DH12',
                'nama' => 'DWIPANTARA Hitam 12',
                'slug' => 'dwipantara-hitam-12',
                'deskripsi' => 'Sigaret Kretek Tangan Dwipantara varian Hitam dengan racikan tembakau pilihan berkarakter mantap, pembakaran lambat, dan aroma rempah khas nusantara.',
                'profil_rasa' => 'Kuat, mantap, gurih rempah pilihan',
                'tar_mg' => 31.0,
                'nikotin_mg' => 2.0,
                'batang_per_bungkus' => 12,
                'bungkus_per_slop' => 10,
                'slop_per_bal' => 20,
                'harga_per_bal' => 980000,
                'harga_per_slop' => 49000,
                'min_order_bal' => 1,
                'min_order_slop' => 1,
                'stok' => 200,
                'status_stok' => 'Ready Stock',
                'gambar' => 'assets/img/hero-pabrik.jpg',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-DK12',
                'nama' => 'DWIPANTARA Kuning 12',
                'slug' => 'dwipantara-kuning-12',
                'deskripsi' => 'Sigaret Kretek Tangan Dwipantara varian Kuning dengan paduan tembakau Jawa dan cengkeh harum, menghadirkan tarikan yang halus dan seimbang.',
                'profil_rasa' => 'Seimbang, wangi manis cengkeh, tarikan halus',
                'tar_mg' => 29.0,
                'nikotin_mg' => 1.9,
                'batang_per_bungkus' => 12,
                'bungkus_per_slop' => 10,
                'slop_per_bal' => 20,
                'harga_per_bal' => 1050000,
                'harga_per_slop' => 52500,
                'min_order_bal' => 1,
                'min_order_slop' => 1,
                'stok' => 180,
                'status_stok' => 'Ready Stock',
                'gambar' => 'assets/img/tembakau-cengkeh.jpg',
                'aktif' => true,
                'urutan' => 2,
            ],
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-DHJ12',
                'nama' => 'DWIPANTARA Hijau 12',
                'slug' => 'dwipantara-hijau-12',
                'deskripsi' => 'Sigaret Kretek Tangan Dwipantara varian Hijau dengan sentuhan saus racikan segar dan tembakau alami, cocok untuk penggemar kretek beraroma bersih.',
                'profil_rasa' => 'Segar, aroma herbal alami, tarikan enteng',
                'tar_mg' => 28.0,
                'nikotin_mg' => 1.8,
                'batang_per_bungkus' => 12,
                'bungkus_per_slop' => 10,
                'slop_per_bal' => 20,
                'harga_per_bal' => 1050000,
                'harga_per_slop' => 52500,
                'min_order_bal' => 1,
                'min_order_slop' => 1,
                'stok' => 150,
                'status_stok' => 'Ready Stock',
                'gambar' => 'assets/img/gudang-distribusi.jpg',
                'aktif' => true,
                'urutan' => 3,
            ],
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-SM12',
                'nama' => 'SEMBADA 12',
                'slug' => 'sembada-12',
                'deskripsi' => 'Sigaret Kretek Tangan Sembada 12 batang dengan racikan tembakau tradisi luhur, cita rasa klasik untuk pecinta kretek sejati.',
                'profil_rasa' => 'Klasik, berbobot, aroma tembakau matang',
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
                'gambar' => 'assets/img/produksi-skt.jpg',
                'aktif' => true,
                'urutan' => 4,
            ],
            [
                'kategori_id' => $skt->id,
                'kode_barang' => 'SKT-KK12',
                'nama' => 'KERETA KENCANA 12',
                'slug' => 'kereta-kencana-12',
                'deskripsi' => 'Sigaret Kretek Tangan Kereta Kencana 12, kretek unggulan mahakarya lintingan tangan terlatih dari pabrik Ponggok Blitar dengan tembakau dan cengkeh pilihan.',
                'profil_rasa' => 'Full flavour, kaya rempah, pembakaran nikmat',
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
                'gambar' => 'assets/img/hero-pabrik.jpg',
                'aktif' => true,
                'urutan' => 5,
            ],
        ];

        // Hapus produk lama yang tidak terpakai
        $activeSlugs = array_column($barangs, 'slug');
        Barang::whereNotIn('slug', $activeSlugs)->delete();

        // Simpan / update data 5 produk resmi
        foreach ($barangs as $b) {
            Barang::updateOrCreate(['slug' => $b['slug']], $b);
        }
    }
}
