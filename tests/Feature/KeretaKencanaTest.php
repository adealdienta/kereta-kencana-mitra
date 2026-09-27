<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class KeretaKencanaTest extends TestCase
{
    public function test_halaman_publik_dapat_diakses(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/profil')->assertStatus(200);
        $this->get('/katalog')->assertStatus(200);
        $this->get('/kontak')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);

        // Halaman pesan wajib redirect ke login jika belum punya akun
        $this->get('/pesan')->assertRedirect(route('login'));
    }

    public function test_halaman_detail_produk(): void
    {
        $barang = Barang::first();
        $this->get('/produk/' . $barang->slug)->assertStatus(200);
    }

    public function test_registrasi_mitra_toko_baru(): void
    {
        $email = 'mitra_' . time() . '@test.com';

        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'nama_toko' => 'Toko Berkah Uji',
            'email' => $email,
            'telepon' => '081234567899',
            'alamat' => 'Jl. Mawar No. 4, Ponggok, Blitar',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('pelanggan', $user->role);
        $this->assertEquals('Toko Berkah Uji', $user->nama_toko);
        $response->assertRedirect(route('pesanan.form'));
    }

    public function test_alur_pemesanan_b2b_mengurangi_stok(): void
    {
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'nama_toko' => 'UD. Mitra Test Laravel',
            'telepon' => '081234567890',
            'alamat' => 'Jl. Pengujian No. 10, Blitar',
        ]);

        $barang = Barang::where('slug', 'kereta-kencana-12')->first() ?? Barang::first();
        $stokAwal = $barang->stok;

        $response = $this->actingAs($customer)->post('/pesan', [
            'barang_id' => $barang->id,
            'nama_mitra' => $customer->nama_toko,
            'telepon' => $customer->telepon,
            'alamat' => $customer->alamat,
            'jumlah' => 10,
            'satuan' => 'Bal',
            'catatan' => 'Test order otomatis',
        ]);

        $barang->refresh();
        $this->assertEquals($stokAwal - 10, $barang->stok);

        $transaksi = Transaksi::where('nama_mitra', 'UD. Mitra Test Laravel')->latest()->first();
        $this->assertNotNull($transaksi);
        $this->assertEquals($customer->id, $transaksi->user_id);
        $response->assertRedirect(route('pesanan.invoice', $transaksi->kode_transaksi));
    }

    public function test_pemesanan_per_slop_minimal_satu_slop(): void
    {
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'nama_toko' => 'Warung Kopi Mbak Sri',
            'telepon' => '081398765432',
            'alamat' => 'Jl. Pasar Pon No. 5, Blitar',
        ]);

        $barang = Barang::where('slug', 'kereta-kencana-12')->first() ?? Barang::first();

        $response = $this->actingAs($customer)->post('/pesan', [
            'barang_id' => $barang->id,
            'nama_mitra' => $customer->nama_toko,
            'telepon' => $customer->telepon,
            'alamat' => $customer->alamat,
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

    public function test_pelanggan_dapat_melihat_halaman_pelacakan(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $this->actingAs($customer)->get('/pesanan-saya')->assertStatus(200);
    }

    public function test_konfirmasi_terima_barang_dan_unggah_foto(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create(['role' => 'pelanggan']);
        $barang = Barang::first();

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-TEST-' . time(),
            'user_id' => $customer->id,
            'barang_id' => $barang->id,
            'nama_mitra' => 'Toko Konfirmasi Test',
            'telepon' => '081122334455',
            'alamat' => 'Jl. Blitar Raya',
            'jumlah' => 2,
            'satuan' => 'Slop',
            'total_harga' => $barang->harga_per_slop * 2,
            'status' => 'Dikirim',
            'sumber' => 'Web B2B Form',
        ]);

        $fakePhoto = UploadedFile::fake()->image('bukti_rokok.jpg', 600, 400);

        $response = $this->actingAs($customer)->post("/pesanan/{$transaksi->id}/konfirmasi-terima", [
            'foto_bukti' => $fakePhoto,
            'catatan_penerima' => 'Barang diterima lengkap dan segel utuh',
        ]);

        $transaksi->refresh();
        $this->assertEquals('Selesai', $transaksi->status);
        $this->assertNotNull($transaksi->bukti_penerimaan);
        $this->assertNotNull($transaksi->diterima_pada);
        Storage::disk('public')->assertExists($transaksi->bukti_penerimaan);

        $response->assertRedirect(route('pesanan.saya'));
    }

    public function test_pembatalan_mandiri_oleh_pelanggan_jika_status_baru_masuk(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $barang = Barang::first();
        $stokAwal = $barang->stok;

        $transaksi = Transaksi::create([
            'kode_transaksi' => 'TRX-BATAL-' . time(),
            'user_id' => $customer->id,
            'barang_id' => $barang->id,
            'nama_mitra' => 'Toko Mau Batal',
            'telepon' => '081122334455',
            'alamat' => 'Jl. Blitar Raya',
            'jumlah' => 1,
            'satuan' => 'Bal',
            'total_harga' => $barang->harga_per_bal,
            'status' => 'Baru Masuk',
            'sumber' => 'Web B2B Form',
        ]);

        $response = $this->actingAs($customer)->post("/pesanan/{$transaksi->id}/batal");

        $transaksi->refresh();
        $this->assertEquals('Dibatalkan', $transaksi->status);
        $barang->refresh();
        $this->assertEquals($stokAwal + 1, $barang->stok);

        $response->assertRedirect();
    }

    public function test_role_owner_dapat_membuka_dashboard(): void
    {
        $owner = User::where('role', 'owner')->first();
        $this->actingAs($owner)->get('/admin')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/barangs')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/kategoris')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/transaksis')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/transaksis/offline')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/users')->assertStatus(200);
    }

    public function test_role_staff_dibatasi_dari_menu_users(): void
    {
        $staff = User::where('role', 'staff')->first();
        $this->actingAs($staff)->get('/admin/transaksis')->assertStatus(200);
        $this->actingAs($staff)->get('/admin/users')->assertStatus(403);
    }
}
