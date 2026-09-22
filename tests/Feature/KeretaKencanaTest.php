<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;

class KeretaKencanaTest extends TestCase
{
    public function test_halaman_publik_dapat_diakses(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/profil')->assertStatus(200);
        $this->get('/katalog')->assertStatus(200);
        $this->get('/pesan')->assertStatus(200);
        $this->get('/kontak')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
    }

    public function test_halaman_detail_produk(): void
    {
        $barang = Barang::first();
        $this->get('/produk/' . $barang->slug)->assertStatus(200);
    }

    public function test_alur_pemesanan_b2b_mengurangi_stok(): void
    {
        $barang = Barang::where('slug', 'kereta-kencana-12')->first() ?? Barang::first();
        $stokAwal = $barang->stok;

        $response = $this->post('/pesan', [
            'barang_id' => $barang->id,
            'nama_mitra' => 'UD. Mitra Test Laravel',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Pengujian No. 10, Blitar',
            'jumlah' => 10,
            'catatan' => 'Test order otomatis',
        ]);

        $barang->refresh();
        $this->assertEquals($stokAwal - 10, $barang->stok);

        $transaksi = Transaksi::where('nama_mitra', 'UD. Mitra Test Laravel')->latest()->first();
        $this->assertNotNull($transaksi);
        $response->assertRedirect(route('pesanan.invoice', $transaksi->kode_transaksi));
    }

    public function test_pemesanan_per_slop_minimal_satu_slop(): void
    {
        $barang = Barang::where('slug', 'kereta-kencana-12')->first() ?? Barang::first();

        $response = $this->post('/pesan', [
            'barang_id' => $barang->id,
            'nama_mitra' => 'Warung Kopi Mbak Sri',
            'telepon' => '081398765432',
            'alamat' => 'Jl. Pasar Pon No. 5, Blitar',
            'satuan' => 'Slop',
            'jumlah' => 1,
            'catatan' => 'Pesan minimal 1 slop untuk warung',
        ]);

        $transaksi = Transaksi::where('nama_mitra', 'Warung Kopi Mbak Sri')->latest()->first();
        $this->assertNotNull($transaksi);
        $this->assertEquals('Slop', $transaksi->satuan);
        $this->assertEquals(1, $transaksi->jumlah);
        $this->assertEquals($barang->harga_per_slop, $transaksi->total_harga);
        $response->assertRedirect(route('pesanan.invoice', $transaksi->kode_transaksi));
    }

    public function test_role_owner_dapat_membuka_dashboard(): void
    {
        $owner = User::where('role', 'owner')->first();
        $this->actingAs($owner)->get('/admin')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/barangs')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/kategoris')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/transaksis')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/users')->assertStatus(200);
    }

    public function test_role_staff_dibatasi_dari_menu_users(): void
    {
        $staff = User::where('role', 'staff')->first();
        $this->actingAs($staff)->get('/admin/transaksis')->assertStatus(200);
        $this->actingAs($staff)->get('/admin/users')->assertStatus(403);
    }
}
