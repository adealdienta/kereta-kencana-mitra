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
            'usia_21' => '1',
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
            'metode_pembayaran' => 'Bayar di Tempat (COD)',
            'catatan' => 'Test order otomatis',
        ]);

        $barang->refresh();
        $this->assertEquals($stokAwal - 10, $barang->stok);

        $transaksi = Transaksi::where('nama_mitra', 'UD. Mitra Test Laravel')->latest()->first();
        $this->assertNotNull($transaksi);
        $this->assertEquals($customer->id, $transaksi->user_id);
        $this->assertEquals('Bayar di Tempat (COD)', $transaksi->metode_pembayaran);
        $this->assertEquals(650000.0, (float)$transaksi->total_harga);
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
        $this->assertEquals(70000.0, (float)$transaksi->total_harga);
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
            'jumlah' => 2,
            'satuan' => 'Slop',
            'total_harga' => 140000,
            'status' => 'Baru Masuk',
            'sumber' => 'Web B2B Form',
        ]);

        $response = $this->actingAs($customer)->post("/pesanan/{$transaksi->id}/batal");

        $transaksi->refresh();
        $this->assertEquals('Dibatalkan', $transaksi->status);
        $barang->refresh();
        $this->assertEquals($stokAwal + 2, $barang->stok);

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

    public function test_role_admin_dibatasi_dari_menu_users(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)->get('/admin/transaksis')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/pengaturan')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(403);
    }

    public function test_pemesanan_multi_item_slop_campuran_dan_pemulihan_stok_batal(): void
    {
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'nama_toko' => 'Toko Campuran Jaya',
            'telepon' => '085712349999',
            'alamat' => 'Jl. Merdeka No. 88, Ponggok, Blitar',
        ]);

        $barangs = Barang::aktif()->take(2)->get();
        $this->assertCount(2, $barangs);

        $b1 = $barangs[0];
        $b2 = $barangs[1];

        if ($b1->stok < 10) { $b1->update(['stok' => 100]); $b1->refresh(); }
        if ($b2->stok < 10) { $b2->update(['stok' => 100]); $b2->refresh(); }

        $stokAwalB1 = $b1->stok;
        $stokAwalB2 = $b2->stok;

        $response = $this->actingAs($customer)->post('/pesan', [
            'nama_mitra' => $customer->nama_toko,
            'telepon' => $customer->telepon,
            'alamat' => $customer->alamat,
            'metode_pembayaran' => 'Bayar di Tempat (COD)',
            'catatan' => 'Pesanan kombinasi 2 Slop B1 dan 1 Slop B2',
            'items' => [
                $b1->id => [
                    'satuan' => 'Slop',
                    'jumlah' => 2,
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

        $this->assertCount(2, $trx->details);
        $this->assertEquals(3 * 70000.0, (float)$trx->total_harga);

        $b1->refresh();
        $b2->refresh();
        $this->assertEquals($stokAwalB1 - 2, $b1->stok);
        $this->assertEquals($stokAwalB2 - 1, $b2->stok);

        // Verifikasi pembatalan memulihkan kedua stok varian secara presisi (Slop)
        $this->actingAs($customer)->post("/pesanan/{$trx->id}/batal");
        $b1->refresh();
        $b2->refresh();
        $this->assertEquals($stokAwalB1, $b1->stok);
        $this->assertEquals($stokAwalB2, $b2->stok);
    }

    public function test_pelanggan_ditolak_dari_panel_admin(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)->get('/admin')->assertStatus(403);
        $this->actingAs($customer)->get('/admin/transaksis')->assertStatus(403);
        $this->actingAs($customer)->get('/admin/barangs')->assertStatus(403);
        $this->actingAs($customer)->get('/admin/mitra')->assertStatus(403);
        $this->actingAs($customer)->get('/admin/legalitas')->assertStatus(403);
    }

    public function test_batas_total_19_slop_gabungan_seluruh_varian(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $barangs = Barang::aktif()->take(2)->get();
        $this->assertCount(2, $barangs);

        foreach ($barangs as $b) {
            if ($b->stok < 50) {
                $b->update(['stok' => 100]);
            }
        }

        // Total 10 + 10 = 20 Slop -> harus ditolak
        $failResp = $this->actingAs($customer)->post('/pesan', [
            'nama_mitra' => 'Toko Melebihi Batas',
            'telepon' => '081200009999',
            'alamat' => 'Ponggok, Blitar',
            'items' => [
                $barangs[0]->id => ['satuan' => 'Slop', 'jumlah' => 10],
                $barangs[1]->id => ['satuan' => 'Slop', 'jumlah' => 10],
            ],
        ]);
        $failResp->assertSessionHasErrors('items');

        // Total 10 + 9 = 19 Slop -> harus diterima (tier 60.000 * 19 = 1.140.000)
        $okResp = $this->actingAs($customer)->post('/pesan', [
            'nama_mitra' => 'Toko Pas 19 Slop',
            'telepon' => '081200009998',
            'alamat' => 'Ponggok, Blitar',
            'items' => [
                $barangs[0]->id => ['satuan' => 'Slop', 'jumlah' => 10],
                $barangs[1]->id => ['satuan' => 'Slop', 'jumlah' => 9],
            ],
        ]);
        $okResp->assertRedirect();

        $trx = Transaksi::where('nama_mitra', 'Toko Pas 19 Slop')->latest()->first();
        $this->assertNotNull($trx);
        $this->assertEquals(19, $trx->jumlah);
        $this->assertEquals(1140000.0, (float)$trx->total_harga);
    }

    public function test_transfer_bank_dan_upload_bukti_transfer_web_serta_proteksi_invoice(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create(['role' => 'pelanggan']);
        $otherCustomer = User::factory()->create(['role' => 'pelanggan']);
        $barang = Barang::aktif()->first();
        if ($barang->stok < 20) {
            $barang->update(['stok' => 100]);
        }

        $this->actingAs($customer)->post('/pesan', [
            'nama_mitra' => 'Toko Transfer Bank',
            'telepon' => '081255556666',
            'alamat' => 'Ponggok, Blitar',
            'metode_pembayaran' => 'Transfer Bank',
            'items' => [
                $barang->id => ['satuan' => 'Slop', 'jumlah' => 2],
            ],
        ])->assertRedirect();

        $trx = Transaksi::where('nama_mitra', 'Toko Transfer Bank')->latest()->first();
        $this->assertNotNull($trx);
        $this->assertEquals('Transfer Bank', $trx->metode_pembayaran);
        $this->assertEquals('Belum Dibayar', $trx->status_pembayaran);

        // Invoice dapat dibuka pemilik, namun ditolak bagi mitra lain
        $this->actingAs($customer)->get(route('pesanan.invoice', $trx->kode_transaksi))->assertStatus(200);
        $this->actingAs($otherCustomer)->get(route('pesanan.invoice', $trx->kode_transaksi))->assertStatus(403);

        // Upload bukti transfer via web
        $fakeReceipt = UploadedFile::fake()->image('struk_transfer.jpg', 500, 700);
        $this->actingAs($customer)
            ->post(route('pesanan.upload_transfer', $trx->id), [
                'bukti_transfer' => $fakeReceipt,
            ])
            ->assertRedirect(route('pesanan.saya'));

        $trx->refresh();
        $this->assertEquals('Menunggu Verifikasi', $trx->status_pembayaran);
        $this->assertNotNull($trx->bukti_transfer);
        Storage::disk('public')->assertExists($trx->bukti_transfer);
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

    public function test_admin_dapat_mengirim_link_reset_sandi_ke_email_mitra_dari_panel_admin(): void
    {
        Mail::fake();

        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
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
            ])
            ->assertJsonMissingPath('data.token');

        $token = '';
        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($customer, &$token) {
            if ($mail->hasTo($customer->email)) {
                $token = $mail->token;
                return $token !== '';
            }
            return false;
        });
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

    public function test_api_v1_lupa_sandi_dengan_otp_6_digit_mobile(): void
    {
        Mail::fake();

        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'email' => 'mobile_otp_' . time() . '@test.com',
            'password' => Hash::make('rahasiaLama123'),
        ]);

        $this->postJson('/api/v1/forgot-password', [
            'email' => $customer->email,
        ])->assertStatus(200);

        $capturedOtp = '';
        Mail::assertSent(\App\Mail\ResetPasswordMail::class, function ($mail) use ($customer, &$capturedOtp) {
            if ($mail->hasTo($customer->email)) {
                $capturedOtp = $mail->otpCode;
                return strlen($capturedOtp) === 6;
            }
            return false;
        });

        $this->assertNotEmpty($capturedOtp);

        $this->postJson('/api/v1/verify-reset-token', [
            'email' => $customer->email,
            'token' => $capturedOtp,
        ])->assertStatus(200)->assertJson(['valid' => true]);

        $this->postJson('/api/v1/reset-password', [
            'email' => $customer->email,
            'token' => $capturedOtp,
            'password' => 'passwordOtpBaru123',
            'password_confirmation' => 'passwordOtpBaru123',
        ])->assertStatus(200);

        $this->assertTrue(Hash::check('passwordOtpBaru123', $customer->fresh()->password));
    }

    public function test_upload_dan_tampil_dokumen_legalitas(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin_legal_' . time() . '@test.com',
        ]);

        // 1. Dokumen tanpa lampiran tampil sebagai "Arsip fisik pabrik"
        \App\Models\LegalDocument::create([
            'nama_dokumen' => 'Izin Uji Arsip Fisik',
            'nomor' => '000-FISIK-TEST',
            'penerbit' => 'KPPBC Blitar',
            'status' => 'Aktif',
            'urutan' => 98,
        ]);

        $this->get('/profil')
            ->assertStatus(200)
            ->assertSee('000-FISIK-TEST')
            ->assertSee('Arsip fisik pabrik');

        // 2. Admin upload file PDF dokumen legalitas -> tampil link "Lihat dokumen"
        $file = UploadedFile::fake()->create('nppbkc_resmi.pdf', 120, 'application/pdf');
        $response = $this->actingAs($admin)->post('/admin/legalitas', [
            'nama_dokumen' => 'Izin Uji Upload PDF',
            'nomor' => '000-PDF-TEST',
            'penerbit' => 'KPPBC Blitar',
            'status' => 'Aktif',
            'urutan' => 99,
            'file_dokumen' => $file,
        ]);

        $response->assertSessionHas('success');

        $uploadedDoc = \App\Models\LegalDocument::where('nomor', '000-PDF-TEST')->first();
        $this->assertNotNull($uploadedDoc);
        $this->assertNotNull($uploadedDoc->file_dokumen);
        Storage::disk('public')->assertExists($uploadedDoc->file_dokumen);

        $this->get('/profil')
            ->assertStatus(200)
            ->assertSee('000-PDF-TEST')
            ->assertSee('Lihat dokumen');
    }

    public function test_harga_bertingkat_slop_seragam(): void
    {
        $barangs = Barang::aktif()->orderBy('urutan')->get();
        $this->assertGreaterThanOrEqual(2, $barangs->count());

        $b1 = $barangs[0];
        $b2 = $barangs[1];

        foreach ([$b1, $b2] as $b) {
            if ($b->stok < 50) {
                $b->update(['stok' => 100]);
            }
        }

        // 1. Pembelian 1-5 Slop (3 Slop -> Rp 70.000/slop = Rp 210.000)
        $u1 = User::factory()->create(['role' => 'pelanggan']);
        $this->actingAs($u1)->post('/pesan', [
            'nama_mitra' => 'Toko Tier 1',
            'telepon' => '081200000001',
            'alamat' => 'Ponggok, Blitar',
            'items' => [
                $b1->id => ['satuan' => 'Slop', 'jumlah' => 3],
            ],
        ])->assertRedirect();

        $trx1 = Transaksi::where('nama_mitra', 'Toko Tier 1')->latest()->first();
        $this->assertEquals(210000.0, (float) $trx1->total_harga);

        // 2. Pembelian 6-10 Slop (4 + 3 = 7 Slop -> Rp 65.000/slop = Rp 455.000)
        $u2 = User::factory()->create(['role' => 'pelanggan']);
        $this->actingAs($u2)->post('/pesan', [
            'nama_mitra' => 'Toko Tier 2',
            'telepon' => '081200000002',
            'alamat' => 'Ponggok, Blitar',
            'items' => [
                $b1->id => ['satuan' => 'Slop', 'jumlah' => 4],
                $b2->id => ['satuan' => 'Slop', 'jumlah' => 3],
            ],
        ])->assertRedirect();

        $trx2 = Transaksi::where('nama_mitra', 'Toko Tier 2')->latest()->first();
        $this->assertEquals(455000.0, (float) $trx2->total_harga);

        // 3. Pembelian di atas 10 Slop (12 Slop -> Rp 60.000/slop = Rp 720.000)
        $u3 = User::factory()->create(['role' => 'pelanggan']);
        $this->actingAs($u3)->post('/pesan', [
            'nama_mitra' => 'Toko Tier 3',
            'telepon' => '081200000003',
            'alamat' => 'Ponggok, Blitar',
            'items' => [
                $b2->id => ['satuan' => 'Slop', 'jumlah' => 12],
            ],
        ])->assertRedirect();

        $trx3 = Transaksi::where('nama_mitra', 'Toko Tier 3')->latest()->first();
        $this->assertEquals(720000.0, (float) $trx3->total_harga);
    }

    public function test_pelanggan_diblokir_dari_admin_dan_transaksi_admin_aman(): void
    {
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);
        $this->actingAs($pelanggan)->get('/admin')->assertStatus(403);

        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $barang = Barang::aktif()->first();
        $slopPerBal = $barang->slop_per_bal ?: 20;
        $barang->update(['stok' => 10 * $slopPerBal]);

        // 1. Transaksi offline langsung tercatat Tunai (Kasir Pabrik) & Lunas (2 Bal = 40 Slop)
        $this->actingAs($superadmin)->post('/admin/transaksis/offline', [
            'barang_id' => $barang->id,
            'satuan' => 'Bal',
            'jumlah' => 2,
            'nama_mitra' => 'Mitra Kasir Tunai',
        ])->assertRedirect();

        $offlineTrx = Transaksi::where('nama_mitra', 'Mitra Kasir Tunai')->latest()->first();
        $this->assertNotNull($offlineTrx);
        $this->assertEquals('Tunai (Kasir Pabrik)', $offlineTrx->metode_pembayaran);
        $this->assertEquals('Lunas', $offlineTrx->status_pembayaran);
        $this->assertEquals(8 * $slopPerBal, $barang->fresh()->stok);

        // 2. Ubah status offline ke Dibatalkan -> stok kembali 10 Bal, lalu jika stok diturunkan jadi 1 Bal dan coba aktifkan lagi -> ditolak
        $this->actingAs($superadmin)->put("/admin/transaksis/{$offlineTrx->id}", [
            'status' => 'Dibatalkan',
        ])->assertSessionHas('success');
        $this->assertEquals(10 * $slopPerBal, $barang->fresh()->stok);

        $barang->update(['stok' => 1 * $slopPerBal]);
        $this->actingAs($superadmin)->put("/admin/transaksis/{$offlineTrx->id}", [
            'status' => 'Diproses',
        ])->assertSessionHas('error');
        $this->assertEquals('Dibatalkan', $offlineTrx->fresh()->status);
        $this->assertEquals(1 * $slopPerBal, $barang->fresh()->stok);

        // 3. Jika stok cukup lalu status diubah ke Selesai -> status_pembayaran ikut Lunas
        $barang->update(['stok' => 5 * $slopPerBal]);
        $this->actingAs($superadmin)->put("/admin/transaksis/{$offlineTrx->id}", [
            'status' => 'Selesai',
        ])->assertSessionHas('success');
        $this->assertEquals('Selesai', $offlineTrx->fresh()->status);
        $this->assertEquals('Lunas', $offlineTrx->fresh()->status_pembayaran);
        $this->assertEquals(3 * $slopPerBal, $barang->fresh()->stok);
    }

    public function test_admin_dapat_memperbarui_user_dan_dokumen_legalitas(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Awal']);

        $this->actingAs($owner)->put("/admin/users/{$admin->id}", [
            'name' => 'Admin Terbarui',
            'email' => $admin->email,
            'role' => 'superadmin',
        ])->assertSessionHas('success');

        $this->assertEquals('Admin Terbarui', $admin->fresh()->name);
        $this->assertEquals('superadmin', $admin->fresh()->role);

        $doc = \App\Models\LegalDocument::create([
            'nama_dokumen' => 'Dokumen Awal',
            'nomor' => 'DOC-001',
            'penerbit' => 'KPPBC Blitar',
            'status' => 'Aktif',
            'urutan' => 50,
        ]);

        $this->actingAs($owner)->put("/admin/legalitas/{$doc->id}", [
            'nama_dokumen' => 'Dokumen Diperbarui',
            'nomor' => 'DOC-002',
            'penerbit' => 'KPPBC Blitar',
            'status' => 'Aktif',
            'urutan' => 51,
        ])->assertSessionHas('success');

        $this->assertEquals('Dokumen Diperbarui', $doc->fresh()->nama_dokumen);
        $this->assertEquals('DOC-002', $doc->fresh()->nomor);
    }

    public function test_owner_dan_admin_tidak_memiliki_fitur_hapus_transaksi(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner']);
        $barang = Barang::aktif()->first();

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-NODEL-' . time(),
            'barang_id' => $barang->id,
            'nama_mitra' => 'Toko Audit Aman',
            'telepon' => '+6285700001111',
            'alamat' => 'Ponggok, Blitar',
            'jumlah' => 2,
            'satuan' => 'Slop',
            'total_harga' => 140000,
            'status' => 'Baru Masuk',
        ]);

        // Halaman daftar transaksi tidak memuat form DELETE
        $this->actingAs($admin)
            ->get('/admin/transaksis')
            ->assertStatus(200)
            ->assertDontSee('name="_method" value="DELETE"', false);

        // Endpoint DELETE ditolak (405 Method Not Allowed) untuk semua role internal
        $this->actingAs($admin)->delete("/admin/transaksis/{$trx->id}")->assertStatus(405);
        $this->actingAs($owner)->delete("/admin/transaksis/{$trx->id}")->assertStatus(405);

        // Data transaksi tetap aman di database
        $this->assertNotNull($trx->fresh());
    }

    public function test_transfer_bank_wajib_upload_dan_verifikasi_lunas_sebelum_diproses_atau_dikirim(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $barang = Barang::aktif()->first();

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-GUARD-' . time(),
            'user_id' => $customer->id,
            'barang_id' => $barang->id,
            'nama_mitra' => 'Toko Uji Transfer',
            'telepon' => '+6285700002222',
            'alamat' => 'Ponggok, Blitar',
            'jumlah' => 2,
            'satuan' => 'Slop',
            'total_harga' => 140000,
            'metode_pembayaran' => 'Transfer Bank',
            'status_pembayaran' => 'Belum Dibayar',
            'status' => 'Baru Masuk',
        ]);

        // 1. Belum ada bukti transfer: Verifikasi Lunas dan ubah status Diproses/Dikirim wajib ditolak
        $this->actingAs($admin)
            ->post(route('admin.transaksis.verifikasi_pembayaran', $trx->id), ['action' => 'terima'])
            ->assertSessionHas('error');
        $this->assertEquals('Belum Dibayar', $trx->fresh()->status_pembayaran);

        foreach (['Diproses', 'Dikirim'] as $targetStatus) {
            $this->actingAs($admin)
                ->put("/admin/transaksis/{$trx->id}", ['status' => $targetStatus])
                ->assertSessionHas('error');
            $this->assertEquals('Baru Masuk', $trx->fresh()->status);
        }

        // 2. Setelah pembeli unggah bukti transfer tetapi admin belum Verifikasi Lunas -> tetap ditolak
        $this->actingAs($customer)
            ->post(route('pesanan.upload_transfer', $trx->id), [
                'bukti_transfer' => UploadedFile::fake()->image('struk.jpg', 500, 600),
            ])
            ->assertRedirect(route('pesanan.saya'));
        $this->assertEquals('Menunggu Verifikasi', $trx->fresh()->status_pembayaran);

        $this->actingAs($admin)
            ->put("/admin/transaksis/{$trx->id}", ['status' => 'Diproses'])
            ->assertSessionHas('error');
        $this->assertEquals('Baru Masuk', $trx->fresh()->status);

        // 3. Setelah admin Verifikasi Lunas -> barulah Diproses dan Dikirim diperbolehkan
        $this->actingAs($admin)
            ->post(route('admin.transaksis.verifikasi_pembayaran', $trx->id), ['action' => 'terima'])
            ->assertSessionHas('success');
        $this->assertEquals('Lunas', $trx->fresh()->status_pembayaran);

        $this->actingAs($admin)
            ->put("/admin/transaksis/{$trx->id}", ['status' => 'Diproses'])
            ->assertSessionHas('success');
        $this->assertEquals('Diproses', $trx->fresh()->status);

        $this->actingAs($admin)
            ->put("/admin/transaksis/{$trx->id}", ['status' => 'Dikirim'])
            ->assertSessionHas('success');
        $this->assertEquals('Dikirim', $trx->fresh()->status);

        // 4. Pembayaran COD tetap bisa langsung Diproses & Dikirim tanpa bukti transfer
        $trxCod = Transaksi::create([
            'kode_transaksi' => 'TRX-COD-' . (time() + 1),
            'user_id' => $customer->id,
            'barang_id' => $barang->id,
            'nama_mitra' => 'Toko Uji COD',
            'telepon' => '+6285700003333',
            'alamat' => 'Blitar',
            'jumlah' => 2,
            'satuan' => 'Slop',
            'total_harga' => 140000,
            'metode_pembayaran' => 'Bayar di Tempat (COD)',
            'status_pembayaran' => 'Belum Dibayar',
            'status' => 'Baru Masuk',
        ]);

        $this->actingAs($admin)
            ->put("/admin/transaksis/{$trxCod->id}", ['status' => 'Dikirim'])
            ->assertSessionHas('success');
        $this->assertEquals('Dikirim', $trxCod->fresh()->status);
    }

    public function test_revisi_tanggal_fasilitas_best_seller_dan_stok_order(): void
    {
        // 1. Tanggal penggantian gambar fasilitas produksi memakai <time datetime="..."> format Indonesia
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Diperbarui: <time datetime="2026-11-20">20 November 2026</time>', false)
            ->assertSee('Paling Diminati');

        $this->get('/profil')
            ->assertStatus(200)
            ->assertSee('Diperbarui: <time datetime="2026-11-20">20 November 2026</time>', false);

        // 2. Flag best_seller aktif pada SEMBADA 12 (Sembada Putih) dan muncul di katalog & detail
        $sembada = Barang::where('slug', 'sembada-12')->firstOrFail();
        $this->assertTrue((bool) $sembada->best_seller);

        $this->get('/katalog')->assertStatus(200)->assertSee('Paling Diminati');
        $sembadaMainHtml = strstr($this->get('/produk/' . $sembada->slug)->assertStatus(200)->getContent(), 'Varian Sejenis Lainnya', true);
        $this->assertStringContainsString('<span class="bestseller-tag">Paling Diminati</span>', $sembadaMainHtml);

        $kencana = Barang::where('slug', 'kereta-kencana-12')->firstOrFail();
        $this->assertFalse((bool) $kencana->best_seller);
        $kencanaMainHtml = strstr($this->get('/produk/' . $kencana->slug)->assertStatus(200)->getContent(), 'Varian Sejenis Lainnya', true);
        $this->assertStringNotContainsString('Paling Diminati', $kencanaMainHtml);

        // 3. Halaman /pesan menampilkan stok produk, "Stok habis" & input disabled jika stok 0
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $kencana->update(['stok' => 0]);

        $this->actingAs($customer)
            ->get('/pesan?produk=' . $kencana->slug)
            ->assertStatus(200)
            ->assertSee('Stok tersedia:')
            ->assertSee('Stok habis')
            ->assertSee('id="input_qty_' . $kencana->id . '"', false)
            ->assertSee('max="0" value="0"', false);

        // Order ke server untuk produk stok 0 tetap ditolak
        $this->actingAs($customer)
            ->post('/pesan', [
                'nama_mitra' => 'Toko Uji Habis',
                'telepon' => '081234567890',
                'alamat' => 'Blitar',
                'items' => [
                    $kencana->id => ['satuan' => 'Slop', 'jumlah' => 1],
                ],
            ])
            ->assertSessionHasErrors('items');
    }

    public function test_rbac_ketat_superadmin_owner_pelanggan_dan_admin_dilarang_order_web(): void
    {
        $owner = User::where('role', 'owner')->first() ?? User::factory()->create(['role' => 'owner']);
        $superadmin = User::where('role', 'superadmin')->first() ?? User::factory()->create(['role' => 'superadmin']);
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);

        // Superadmin tidak boleh membuat akun owner, mengubah/menghapus owner, atau mengubah role pelanggan
        $this->actingAs($superadmin)->post('/admin/users', [
            'name' => 'Calon Owner Ilegal',
            'email' => 'ilegal_owner_' . time() . '@test.com',
            'password' => 'rahasia123',
            'role' => 'owner',
        ])->assertStatus(403);

        $this->actingAs($superadmin)->put("/admin/users/{$owner->id}", [
            'name' => 'Owner Diubah',
            'email' => $owner->email,
            'role' => 'admin',
        ])->assertStatus(403);

        $this->actingAs($superadmin)->delete("/admin/users/{$owner->id}")->assertStatus(403);

        $this->actingAs($superadmin)->put("/admin/users/{$pelanggan->id}", [
            'name' => $pelanggan->name,
            'email' => $pelanggan->email,
            'role' => 'admin',
        ])->assertStatus(403);

        // Admin/Owner/Superadmin tidak bisa memesan sebagai pembeli di rute pelanggan web
        $this->actingAs($admin)->get('/pesan')->assertStatus(403);
        $this->actingAs($owner)->get('/pesan')->assertStatus(403);
    }

    public function test_pesanan_lunas_tidak_bisa_dibatalkan_mandiri_di_web_dan_api(): void
    {
        Storage::fake('public');
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $barang = Barang::aktif()->where('stok', '>=', 5)->firstOrFail();

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-LUNAS-' . time(),
            'user_id' => $customer->id,
            'barang_id' => $barang->id,
            'nama_mitra' => 'Toko Lunas Uji',
            'telepon' => '+6285700004444',
            'alamat' => 'Blitar',
            'jumlah' => 2,
            'satuan' => 'Slop',
            'total_harga' => 140000,
            'metode_pembayaran' => 'Transfer Bank',
            'status_pembayaran' => 'Lunas',
            'status' => 'Baru Masuk',
        ]);

        // Web self-cancel wajib ditolak
        $this->actingAs($customer)
            ->post(route('pesanan.batal', $trx->id))
            ->assertSessionHas('error');
        $this->assertEquals('Baru Masuk', $trx->fresh()->status);

        // API detail melaporkan boleh_batal = false & API batal mengembalikan 422
        $token = $customer->createToken('test-lunas')->plainTextToken;
        $this->withToken($token)
            ->getJson("/api/v1/pesanan/{$trx->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.boleh_batal', false);

        $this->withToken($token)
            ->postJson("/api/v1/pesanan/{$trx->id}/batal")
            ->assertStatus(422);
        $this->assertEquals('Baru Masuk', $trx->fresh()->status);

        // Upload bukti transfer untuk order COD ditolak oleh API
        $trxCod = Transaksi::create([
            'kode_transaksi' => 'TRX-COD2-' . time(),
            'user_id' => $customer->id,
            'barang_id' => $barang->id,
            'nama_mitra' => 'Toko COD Uji',
            'telepon' => '+6285700005555',
            'alamat' => 'Blitar',
            'jumlah' => 1,
            'satuan' => 'Slop',
            'total_harga' => 70000,
            'metode_pembayaran' => 'Bayar di Tempat (COD)',
            'status_pembayaran' => 'Belum Dibayar',
            'status' => 'Baru Masuk',
        ]);

        $this->withToken($token)
            ->postJson("/api/v1/pesanan/{$trxCod->id}/upload-bukti-transfer", [
                'bukti_transfer' => UploadedFile::fake()->image('bukti.jpg', 300, 300),
            ])
            ->assertStatus(422);
    }

    public function test_sinkronisasi_otp_web_ke_api_dan_best_seller_api(): void
    {
        Mail::fake();
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'email' => 'otp_sync_' . time() . '@test.com',
        ]);

        // Memicu lupa sandi dari web
        $this->post('/lupa-sandi', ['email' => $customer->email])
            ->assertSessionHas('success');

        $capturedOtp = '';
        Mail::assertSent(ResetPasswordMail::class, function ($mail) use ($customer, &$capturedOtp) {
            if ($mail->hasTo($customer->email)) {
                $capturedOtp = $mail->otpCode;
                return strlen($capturedOtp) === 6;
            }
            return false;
        });

        // OTP yang dikirim lewat Web bisa langsung diverifikasi lewat endpoint Mobile API
        $this->postJson('/api/v1/verify-reset-token', [
            'email' => $customer->email,
            'token' => $capturedOtp,
        ])->assertStatus(200)->assertJson(['valid' => true]);

        // API Barang menyertakan best_seller dan Pengaturan menyertakan nama_pabrik
        $sembada = Barang::where('slug', 'sembada-12')->firstOrFail();
        $this->getJson("/api/v1/barang/{$sembada->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.best_seller', true);

        $this->getJson('/api/v1/pengaturan')
            ->assertStatus(200)
            ->assertJsonPath('data.nama_pabrik', 'PR. KERETA KENCANA');
    }

    public function test_stok_presisi_slop_kurang_dari_satu_bal_tetap_bisa_dipesan_dan_tidak_membulatkan_bal(): void
    {
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'nama_toko' => 'Toko Presisi Slop',
            'telepon' => '085712340017',
            'alamat' => 'Ponggok, Blitar',
        ]);

        $barang = Barang::aktif()->firstOrFail();
        $barang->update([
            'stok' => 17,
            'status_stok' => 'Ready Stock',
        ]);

        $this->actingAs($customer)
            ->get('/pesan')
            ->assertStatus(200)
            ->assertSee('Stok tersedia:')
            ->assertSee('17 Slop');

        $response = $this->actingAs($customer)->post('/pesan', [
            'nama_mitra' => $customer->nama_toko,
            'telepon' => $customer->telepon,
            'alamat' => $customer->alamat,
            'metode_pembayaran' => 'Bayar di Tempat (COD)',
            'items' => [
                $barang->id => ['satuan' => 'Slop', 'jumlah' => 1],
            ],
        ]);

        $trx = Transaksi::where('nama_mitra', 'Toko Presisi Slop')->latest()->first();
        $this->assertNotNull($trx);
        $response->assertRedirect(route('pesanan.invoice', $trx->kode_transaksi));
        $this->assertEquals(16, $barang->fresh()->stok);

        // Pesan melebihi sisa 16 Slop wajib ditolak
        $this->actingAs($customer)
            ->post('/pesan', [
                'nama_mitra' => $customer->nama_toko,
                'telepon' => $customer->telepon,
                'alamat' => $customer->alamat,
                'items' => [
                    $barang->id => ['satuan' => 'Slop', 'jumlah' => 18],
                ],
            ])
            ->assertSessionHasErrors('items');
    }

    public function test_pengaturan_pabrik_dan_upload_media_tersinkron_ke_web_dan_api(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $owner = User::where('role', 'owner')->first() ?? User::factory()->create(['role' => 'owner']);
        $pelanggan = User::factory()->create(['role' => 'pelanggan']);

        // Pelanggan tidak boleh mengakses menu pengaturan internal pabrik
        $this->actingAs($pelanggan)->get('/admin/pengaturan')->assertStatus(403);

        // Admin dan Owner dapat membuka menu Pengaturan & Media dan memperbarui pengaturan pabrik
        $this->actingAs($admin)->get('/admin/pengaturan')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/pengaturan')->assertStatus(200);

        // Default jam operasional resmi langsung muncul di halaman publik & API
        $this->get('/')->assertSee('Buka: 09.00 - 16.00 WIB (Senin - Sabtu)');
        $this->get('/profil')->assertSee('Buka: 09.00 - 16.00 WIB (Senin - Sabtu)');
        $this->getJson('/api/v1/pengaturan')
            ->assertStatus(200)
            ->assertJsonPath('data.jam_operasional', 'Buka: 09.00 - 16.00 WIB (Senin - Sabtu)');

        $this->actingAs($owner)->put('/admin/pengaturan/umum', [
            'wa_pabrik' => '081234567899',
            'nppbkc' => '999000-070700-TEST',
            'jam_operasional' => 'Buka: 08.00 - 17.00 WIB (Senin - Sabtu)',
            'bank_nama' => 'Bank BCA',
            'bank_rekening' => '1234567890',
            'bank_atas_nama' => 'PR. KERETA KENCANA',
        ])->assertSessionHas('success');

        $this->getJson('/api/v1/pengaturan')
            ->assertStatus(200)
            ->assertJsonPath('data.wa_pabrik', '6281234567899')
            ->assertJsonPath('data.nppbkc', '999000-070700-TEST')
            ->assertJsonPath('data.jam_operasional', 'Buka: 08.00 - 17.00 WIB (Senin - Sabtu)')
            ->assertJsonPath('data.bank_nama', 'Bank BCA')
            ->assertJsonPath('data.bank_rekening', '1234567890');

        // Halaman publik langsung memakai nomor WA & jam operasional terbaru tanpa hardcode lama
        $this->get('/')
            ->assertSee('6281234567899')
            ->assertSee('Buka: 08.00 - 17.00 WIB (Senin - Sabtu)');
        $this->get('/profil')->assertSee('Buka: 08.00 - 17.00 WIB (Senin - Sabtu)');

        // Unggah media kustom pada slot hero & fasilitas lalu reset ke default
        $foto = UploadedFile::fake()->image('pabrik-baru.jpg', 1200, 800);
        $this->actingAs($owner)->post('/admin/pengaturan/media/pabrik-beranda', [
            'foto' => $foto,
            'caption' => 'Gedung Pabrik Baru Ponggok 2026',
            'updated_at' => '2026-11-23',
        ])->assertSessionHas('success');

        $storedPath = \App\Models\CompanySetting::getVal('media_file_pabrik-beranda');
        $this->assertNotEmpty($storedPath);
        Storage::disk('public')->assertExists($storedPath);

        $this->get('/')
            ->assertSee('Gedung Pabrik Baru Ponggok 2026')
            ->assertSee('datetime="2026-11-23"', false);

        $this->actingAs($owner)->delete('/admin/pengaturan/media/pabrik-beranda')
            ->assertSessionHas('success');
        Storage::disk('public')->assertMissing($storedPath);

        // Tambah lebih dari 2 gambar pada banner slider beranda (+2 slide baru), edit, dan hapus salah satu
        $this->actingAs($pelanggan)->post('/admin/pengaturan/hero', [
            'foto' => UploadedFile::fake()->image('pelanggan-hero.jpg', 1200, 675),
        ])->assertStatus(403);

        $beforeCount = count(\App\Services\PabrikConfigService::getHeroSlides());
        $beforeExtra = \App\Services\PabrikConfigService::getExtraHeroKeys();

        $this->actingAs($admin)->post('/admin/pengaturan/hero', [
            'foto' => UploadedFile::fake()->image('hero_uji_a.jpg', 1600, 900),
            'caption' => 'Armada Pengiriman Resmi Blitar',
        ])->assertSessionHas('success');

        $afterA = \App\Services\PabrikConfigService::getExtraHeroKeys();
        $newA = array_values(array_diff($afterA, $beforeExtra));
        $this->assertCount(1, $newA);
        $keyA = $newA[0];

        $this->actingAs($admin)->post('/admin/pengaturan/hero', [
            'foto' => UploadedFile::fake()->image('hero_uji_b.jpg', 1600, 900),
            'caption' => 'Kualitas Pita Cukai Resmi',
        ])->assertSessionHas('success');

        $afterB = \App\Services\PabrikConfigService::getExtraHeroKeys();
        $newB = array_values(array_diff($afterB, $afterA));
        $this->assertCount(1, $newB);
        $keyB = $newB[0];

        $heroAPath = \App\Models\CompanySetting::getVal("media_file_{$keyA}");
        $heroBPath = \App\Models\CompanySetting::getVal("media_file_{$keyB}");
        $this->assertNotEmpty($heroAPath);
        $this->assertNotEmpty($heroBPath);
        Storage::disk('public')->assertExists($heroAPath);
        Storage::disk('public')->assertExists($heroBPath);

        // Halaman beranda langsung merender slide tambahan berikut titik indikatornya
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('aria-label="Slide ' . ($beforeCount + 1) . '"', false)
            ->assertSee('aria-label="Slide ' . ($beforeCount + 2) . '"', false)
            ->assertSee($heroAPath)
            ->assertSee($heroBPath);

        // Edit slide tambahan pertama lalu hapus -> file ikut terhapus, jumlah slide berkurang 1
        $this->actingAs($admin)->post("/admin/pengaturan/media/{$keyA}", [
            'caption' => 'Armada Blitar Diperbarui',
        ])->assertSessionHas('success');
        $this->assertEquals('Armada Blitar Diperbarui', \App\Models\CompanySetting::getVal("media_caption_{$keyA}"));

        $this->actingAs($admin)->delete("/admin/pengaturan/media/{$keyA}")
            ->assertSessionHas('success');
        Storage::disk('public')->assertMissing($heroAPath);

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('aria-label="Slide ' . ($beforeCount + 1) . '"', false)
            ->assertDontSee('aria-label="Slide ' . ($beforeCount + 2) . '"', false)
            ->assertSee($heroBPath);

        // Gambar panel kanan halaman login (/login) dapat diubah & direset via Pengaturan & Media
        \Illuminate\Support\Facades\Auth::logout();
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('login-split-section')
            ->assertSee('pro-produksi-hero.jpg');

        $loginFoto = UploadedFile::fake()->image('login-banner-baru.jpg', 1200, 900);
        $this->actingAs($admin)->post('/admin/pengaturan/media/login-banner', [
            'foto' => $loginFoto,
            'caption' => 'Visual Baru Halaman Login 2026',
        ])->assertSessionHas('success');

        $loginPath = \App\Models\CompanySetting::getVal('media_file_login-banner');
        $this->assertNotEmpty($loginPath);
        Storage::disk('public')->assertExists($loginPath);

        \Illuminate\Support\Facades\Auth::logout();
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee($loginPath)
            ->assertSee('Visual Baru Halaman Login 2026');

        $this->actingAs($admin)->delete('/admin/pengaturan/media/login-banner')
            ->assertSessionHas('success');
        Storage::disk('public')->assertMissing($loginPath);

        \Illuminate\Support\Facades\Auth::logout();
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('pro-produksi-hero.jpg');
    }

    public function test_halaman_galat_kustom_dan_filter_admin_pembayaran_serta_log(): void
    {
        $owner = User::where('role', 'owner')->first() ?? User::factory()->create(['role' => 'owner']);
        $admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);

        // 1. Halaman 404 kustom berbahasa Indonesia (bukan bawaan Inggris Laravel)
        $this->get('/halaman-tidak-ditemukan-uji')
            ->assertStatus(404)
            ->assertSee('Halaman atau Produk Tidak Ditemukan')
            ->assertSee('Kembali ke Beranda');

        // 2. Halaman 403 kustom berbahasa Indonesia ketika admin membuka rute khusus owner/superadmin
        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertStatus(403)
            ->assertSee('Halaman Ini Tidak Tersedia untuk Peran Akun Anda');

        // 3. Filter status_pembayaran di daftar transaksi & filter search di audit log berjalan 200
        $this->actingAs($owner)->get('/admin/transaksis?status_pembayaran=Lunas')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/logs?search=Login')->assertStatus(200);
    }
}
