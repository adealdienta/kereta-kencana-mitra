<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Barang;
use App\Models\User;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $dh = Barang::where('slug', 'dwipantara-hitam-12')->first();
        $dk = Barang::where('slug', 'dwipantara-kuning-12')->first();
        $dhj = Barang::where('slug', 'dwipantara-hijau-12')->first();
        $sm = Barang::where('slug', 'sembada-12')->first();
        $kk = Barang::where('slug', 'kereta-kencana-12')->first();
        $staff = User::where('role', 'staff')->first();

        // Hapus transaksi lama agar bersih dan sinkron dengan produk baru
        Transaksi::truncate();

        $transaksiList = [
            [
                'kode_transaksi' => 'TRX-202609-001',
                'user_id' => $staff?->id,
                'barang_id' => $dh->id,
                'nama_mitra' => 'Toko Berkah Grosir',
                'telepon' => '081234567891',
                'alamat' => 'Jl. Basuki Rahmat No. 12, Malang, Jawa Timur',
                'jumlah' => 15,
                'satuan' => 'Bal',
                'total_harga' => 15 * $dh->harga_per_bal,
                'catatan' => 'Kirim via kargo langganan Blitar-Malang armada cold diesel',
                'status' => 'Diproses',
                'nomor_do' => 'DO-KK-2026-0901',
                'nomor_resi' => 'CARGO-MLG-8823',
                'sumber' => 'Web B2B WhatsApp',
            ],
            [
                'kode_transaksi' => 'TRX-202609-002',
                'user_id' => $staff?->id,
                'barang_id' => $kk->id,
                'nama_mitra' => 'UD. Tembakau Sentosa',
                'telepon' => '082198765432',
                'alamat' => 'Pasar Legi Blok A-4, Kota Blitar',
                'jumlah' => 20,
                'satuan' => 'Bal',
                'total_harga' => 20 * $kk->harga_per_bal,
                'catatan' => 'Ambil langsung mandiri di pos gerbang gudang pabrik Ponggok',
                'status' => 'Selesai',
                'nomor_do' => 'DO-KK-2026-0894',
                'nomor_resi' => 'PICKUP-PABRIK-042',
                'sumber' => 'Web B2B Form',
            ],
            [
                'kode_transaksi' => 'TRX-202609-003',
                'user_id' => $staff?->id,
                'barang_id' => $dk->id,
                'nama_mitra' => 'Agen Rokok Jaya Abadi',
                'telepon' => '085712349999',
                'alamat' => 'Kec. Wlingi, Kabupaten Blitar',
                'jumlah' => 10,
                'satuan' => 'Bal',
                'total_harga' => 10 * $dk->harga_per_bal,
                'catatan' => 'Mohon sertakan faktur fisik bercap basah perusahaan',
                'status' => 'Baru Masuk',
                'nomor_do' => null,
                'nomor_resi' => null,
                'sumber' => 'Web B2B WhatsApp',
            ],
            [
                'kode_transaksi' => 'TRX-202609-004',
                'user_id' => $staff?->id,
                'barang_id' => $sm->id,
                'nama_mitra' => 'Grosir Berkah Mandiri',
                'telepon' => '081333445566',
                'alamat' => 'Jl. Dhoho No. 45, Kota Kediri',
                'jumlah' => 8,
                'satuan' => 'Bal',
                'total_harga' => 8 * $sm->harga_per_bal,
                'catatan' => 'Pesanan rutin distributor Kediri Raya',
                'status' => 'Diproses',
                'nomor_do' => 'DO-KK-2026-0905',
                'nomor_resi' => 'EXP-KDR-1092',
                'sumber' => 'Web B2B Form',
            ],
            [
                'kode_transaksi' => 'TRX-202609-005',
                'user_id' => $staff?->id,
                'barang_id' => $dhj->id,
                'nama_mitra' => 'Mitra Tembakau Sejahtera',
                'telepon' => '085299887711',
                'alamat' => 'Pasar Wage Blok B-12, Tulungagung',
                'jumlah' => 12,
                'satuan' => 'Bal',
                'total_harga' => 12 * $dhj->harga_per_bal,
                'catatan' => 'Pengiriman armada pabrik langsung ke gudang Tulungagung',
                'status' => 'Selesai',
                'nomor_do' => 'DO-KK-2026-0889',
                'nomor_resi' => 'TRUCK-KK-08',
                'sumber' => 'Web B2B WhatsApp',
            ],
            [
                'kode_transaksi' => 'TRX-202609-006',
                'user_id' => $staff?->id,
                'barang_id' => $kk->id,
                'nama_mitra' => 'Warung Kopi Mbak Sri',
                'telepon' => '081398765432',
                'alamat' => 'Jl. Pasar Pon No. 5, Blitar',
                'jumlah' => 5,
                'satuan' => 'Slop',
                'total_harga' => 5 * $kk->harga_per_slop,
                'catatan' => 'Pembelian eceran slop untuk warung',
                'status' => 'Selesai',
                'nomor_do' => 'DO-KK-2026-0910',
                'nomor_resi' => 'LOKAL-BLT-055',
                'sumber' => 'Web B2B Form',
            ],
            [
                'kode_transaksi' => 'TRX-202609-007',
                'user_id' => $staff?->id,
                'barang_id' => $dh->id,
                'nama_mitra' => 'Toko Sumber Rejeki',
                'telepon' => '087811223344',
                'alamat' => 'Kec. Srengat, Kabupaten Blitar',
                'jumlah' => 3,
                'satuan' => 'Slop',
                'total_harga' => 3 * $dh->harga_per_slop,
                'catatan' => 'Uji pasar varian Dwipantara Hitam di toko',
                'status' => 'Diproses',
                'nomor_do' => 'DO-KK-2026-0912',
                'nomor_resi' => 'LOKAL-BLT-059',
                'sumber' => 'Web B2B WhatsApp',
            ],
            [
                'kode_transaksi' => 'TRX-202609-008',
                'user_id' => $staff?->id,
                'barang_id' => $sm->id,
                'nama_mitra' => 'Koperasi Unit Desa Ponggok',
                'telepon' => '081277665544',
                'alamat' => 'Dusun Kebonduren, Ponggok, Blitar',
                'jumlah' => 2,
                'satuan' => 'Bal',
                'total_harga' => 2 * $sm->harga_per_bal,
                'catatan' => 'Pasokan etalase koperasi desa',
                'status' => 'Baru Masuk',
                'nomor_do' => null,
                'nomor_resi' => null,
                'sumber' => 'Web B2B Form',
            ],
        ];

        foreach ($transaksiList as $t) {
            Transaksi::create($t);
        }
    }
}
