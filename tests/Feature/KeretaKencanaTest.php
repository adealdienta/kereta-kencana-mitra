<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class KeretaKencanaTest extends TestCase
{
    use DatabaseTransactions;
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
        if ($barang->stok < 20) {
            $barang->update(['stok' => 200]);
            $barang->refresh();
        }
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

    public function test_pemesanan_multi_item_dengan_satuan_independen_slop_dan_bal(): void
    {
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'nama_toko' => 'Toko Campuran Jaya',
            'telepon' => '085712349999',
            'alamat' => 'Jl. Merdeka No. 88, Ponggok, Blitar',
        ]);

        $barangs = Barang::take(2)->get();
        $this->assertCount(2, $barangs);

        $b1 = $barangs[0]; // misal pesan 1 Bal
        $b2 = $barangs[1]; // misal pesan 1 Slop

        if ($b1->stok < 10) { $b1->update(['stok' => 100]); $b1->refresh(); }
        if ($b2->stok < 10) { $b2->update(['stok' => 100]); $b2->refresh(); }

        $stokAwalB1 = $b1->stok;
        $stokAwalB2 = $b2->stok;

        $response = $this->actingAs($customer)->post('/pesan', [
            'nama_mitra' => $customer->nama_toko,
            'telepon' => $customer->telepon,
            'alamat' => $customer->alamat,
            'catatan' => 'Pesanan kombinasi 1 Bal B1 dan 1 Slop B2',
            'items' => [
                $b1->id => [
                    'satuan' => 'Bal',
                    'jumlah' => 1,
                ],
                $b2->id => [
                    'satuan' => 'Slop',
                    'jumlah' => 1,
                ],
            ],
        ]);

        $trx = Transaksi::where('nama_mitra', 'Toko Campuran Jaya')->latest()->first();
        $this->assertNotNull($trx);
        $response->assertRedirect(route('pesanan.invoice', $trx->kode_transaksi));

        // Verifikasi rincian transaksi (transaksi_details)
        $this->assertCount(2, $trx->details);

        $expectedTotal = (1 * (float)$b1->harga_per_bal) + (1 * (float)$b2->harga_per_slop);
        $this->assertEquals($expectedTotal, (float)$trx->total_harga);

        // Verifikasi stok berkurang akurat per varian
        $b1->refresh();
        $b2->refresh();
        $this->assertEquals($stokAwalB1 - 1, $b1->stok);
        $this->assertEquals($stokAwalB2 - 1, $b2->stok); // 1 Slop = 1 Bal dipotong (ceil)

        // Verifikasi pembatalan memulihkan kedua stok varian
        $this->actingAs($customer)->post("/pesanan/{$trx->id}/batal");
        $b1->refresh();
        $b2->refresh();
        $this->assertEquals($stokAwalB1, $b1->stok);
        $this->assertEquals($stokAwalB2, $b2->stok);
    }

    public function test_pelanggan_dapat_meminta_link_reset_sandi(): void
    {
        Mail::fake();

        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'email' => 'reset_test_' . time() . '@test.com',
        ]);

        $response = $this->post('/lupa-sandi', [
            'email' => $customer->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $customer->email,
        ]);

        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });
    }

    public function test_pelanggan_dapat_mereset_sandi_dengan_token(): void
    {
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'email' => 'reset_done_' . time() . '@test.com',
            'password' => Hash::make('passwordLama123'),
        ]);

        $plainToken = 'test-token-reset-1234567890';
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $customer->email],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now(),
            ]
        );

        // Akses halaman reset sandi
        $this->get("/reset-sandi/{$plainToken}?email=" . urlencode($customer->email))
            ->assertStatus(200);

        // Kirim perubahan sandi baru
        $response = $this->post('/reset-sandi', [
            'token' => $plainToken,
            'email' => $customer->email,
            'password' => 'passwordBaruSuper123',
            'password_confirmation' => 'passwordBaruSuper123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success');

        // Token telah dihapus
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $customer->email,
        ]);

        // Sandi baru terupdate dan bisa login
        $customer->refresh();
        $this->assertTrue(Hash::check('passwordBaruSuper123', $customer->password));

        $loginResponse = $this->post('/login', [
            'email' => $customer->email,
            'password' => 'passwordBaruSuper123',
        ]);
        $loginResponse->assertRedirect(route('pesanan.saya'));
    }

    public function test_staff_dapat_mengirim_link_reset_sandi_ke_email_mitra_dari_panel_admin(): void
    {
        Mail::fake();

        $admin = User::where('role', 'staff')->first() ?? User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'staff']);
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $response = $this->actingAs($admin)->post("/admin/mitra/{$customer->id}/kirim-reset-link");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $customer->email,
        ]);

        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($customer) {
            return $mail->hasTo($customer->email);
        });
    }

    public function test_api_v1_lupa_sandi_dan_reset_sandi_mobile(): void
    {
        Mail::fake();

        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'email' => 'mobile_api_' . time() . '@test.com',
            'password' => Hash::make('rahasiaLama123'),
        ]);

        // 1. Endpoint Forgot Password Mobile
        $forgotResponse = $this->postJson('/api/v1/forgot-password', [
            'email' => $customer->email,
        ]);

        $forgotResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        $token = $forgotResponse->json('data.token');
        $this->assertNotEmpty($token);

        // 2. Endpoint Verifikasi Token Mobile
        $verifyResponse = $this->postJson('/api/v1/verify-reset-token', [
            'email' => $customer->email,
            'token' => $token,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'valid' => true,
            ]);

        // 3. Endpoint Reset Password Mobile
        $resetResponse = $this->postJson('/api/v1/reset-password', [
            'email' => $customer->email,
            'token' => $token,
            'password' => 'passwordMobileBaru123',
            'password_confirmation' => 'passwordMobileBaru123',
        ]);

        $resetResponse->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ]);

        // Verifikasi kata sandi baru telah tersimpan
        $customer->refresh();
        $this->assertTrue(Hash::check('passwordMobileBaru123', $customer->password));
    }
}
