<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MobileApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function createMitra(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Pak Joko Widodo',
            'nama_toko' => 'Toko Subur Makmur',
            'email' => 'mitra_' . Str::random(8) . '@test.com',
            'telepon' => '+628' . random_int(100000000, 999999999),
            'alamat' => 'Jl. Diponegoro No. 12, Ponggok, Blitar',
            'password' => Hash::make('password123'),
            'role' => 'pelanggan',
        ], $attributes));
    }

    public function test_ping_endpoint(): void
    {
        $response = $this->getJson('/api/v1/ping');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'online');
    }

    public function test_pengaturan_endpoint(): void
    {
        // Verifikasi nilai bawaan setelah penghapusan data dummy bank dan update WA admin
        $defaultRes = $this->getJson('/api/v1/pengaturan');
        $defaultRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.wa_pabrik', '6285790627104')
            ->assertJsonPath('data.bank_rekening', null);

        config(['pabrik.wa_pabrik' => '628123456789', 'pabrik.nppbkc' => '0821.1.2.99999']);

        $response = $this->getJson('/api/v1/pengaturan');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.wa_pabrik', '628123456789')
            ->assertJsonPath('data.nppbkc', '0821.1.2.99999');
    }

    public function test_katalog_barang_dapat_diakses_publik(): void
    {
        $response = $this->getJson('/api/v1/barang');
        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'kode_barang', 'nama', 'harga_per_slop', 'harga_per_bal',
                        'slop_per_bal', 'stok', 'status_stok', 'image_url', 'tar_mg', 'nikotin_mg'
                    ]
                ]
            ]);
    }

    public function test_register_mitra_via_mobile_api(): void
    {
        $email = 'mitra_' . Str::random(8) . '@toko.com';

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Bambang Sudarsono',
            'nama_toko' => 'Toko Rejeki Nomplok',
            'email' => $email,
            'telepon' => '081298765432',
            'alamat' => 'Jl. Pasar Legi No. 5, Blitar',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'nama_toko', 'email', 'telepon', 'role']
                ]
            ]);

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('pelanggan', $user->role);
        $this->assertEquals('+6281298765432', $user->telepon); // Terverifikasi normalisasi
    }

    public function test_login_mitra_dengan_email(): void
    {
        $mitra = $this->createMitra();

        $response = $this->postJson('/api/v1/login', [
            'email' => $mitra->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.id', $mitra->id);
        $this->assertNotEmpty($response->json('data.token'));
    }

    public function test_login_mitra_dengan_nomor_whatsapp(): void
    {
        $uniqueDigits = (string)random_int(100000000, 999999999);
        $mitra = $this->createMitra(['telepon' => '+628' . $uniqueDigits]);

        $response = $this->postJson('/api/v1/login', [
            'identifier' => '08' . $uniqueDigits, // Input format 08...
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.id', $mitra->id);
    }

    public function test_login_gagal_jika_role_bukan_pelanggan(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin_' . Str::random(5) . '@keretakencana.com',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_logout_menghapus_token(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertCount(0, $mitra->fresh()->tokens);
    }

    public function test_get_and_update_profile(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;

        // GET Profile
        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/profile')
            ->assertStatus(200)
            ->assertJsonPath('data.id', $mitra->id);

        // UPDATE Profile
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/profile', [
                'name' => 'Joko Widodo Update',
                'nama_toko' => 'Toko Subur Makmur Baru',
                'email' => $mitra->email,
                'telepon' => '08999888777',
                'alamat' => 'Alamat Baru No. 99',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.name', 'Joko Widodo Update')
            ->assertJsonPath('data.telepon', '+628999888777');
    }

    public function test_pembuatan_pesanan_sukses_dan_mengurangi_stok_agregasi(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();

        $stokAwal = $barang->stok; // dalam Bal

        // Pesan 5 Slop dan 6 Slop (Total 11 Slop -> tier Rp 60.000/slop = Rp 660.000)
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/pesanan', [
                'metode_pembayaran' => 'Transfer Bank',
                'catatan' => 'Titip di toko tetangga jika tutup',
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 5],
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 6],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'Baru Masuk')
            ->assertJsonPath('data.metode_pembayaran', 'Transfer Bank')
            ->assertJsonPath('data.total_harga', 660000);

        // Pengurangan stok: ceil(11/20) = 1 Bal
        $this->assertEquals($stokAwal - 1, $barang->fresh()->stok);
    }

    public function test_pembuatan_pesanan_gagal_jika_stok_tidak_cukup(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();
        $barang->update(['stok' => 0]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/pesanan', [
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 1],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_pembuatan_pesanan_ditolak_jika_melebihi_19_slop_atau_satuan_bal(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();

        // Tolak > 19 Slop
        $overRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/pesanan', [
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 20],
                ],
            ]);
        $overRes->assertStatus(422)
            ->assertJsonPath('success', false);

        // Tolak satuan Bal (khusus via WA Admin)
        $balRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/pesanan', [
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Bal', 'jumlah' => 1],
                ],
            ]);
        $balRes->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_pembuatan_pesanan_ditolak_jika_order_lock_aktif(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();

        // Buat transaksi yang statusnya 'Dikirim' tanpa bukti terima
        Transaksi::create([
            'kode_transaksi' => 'TRX-LOCK-' . Str::random(8),
            'user_id' => $mitra->id,
            'barang_id' => $barang->id,
            'nama_mitra' => $mitra->nama_toko,
            'telepon' => $mitra->telepon,
            'alamat' => $mitra->alamat,
            'jumlah' => 1,
            'satuan' => 'Bal',
            'total_harga' => 1000000,
            'status' => 'Dikirim',
            'bukti_penerimaan' => null,
        ]);

        // Cek status kunci endpoint
        $lockCheck = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/pesanan/status-kunci');
        $lockCheck->assertStatus(200)
            ->assertJsonPath('data.terkunci', true);

        // Coba pesan lagi -> Harus 409 Conflict
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/pesanan', [
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 1],
                ],
            ]);

        $response->assertStatus(409)
            ->assertJsonPath('errors.order_lock', true);
    }

    public function test_idempotency_key_mencegah_pesanan_ganda(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();
        $idempotencyKey = 'key-' . Str::uuid();

        // Request pertama
        $res1 = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Idempotency-Key', $idempotencyKey)
            ->postJson('/api/v1/pesanan', [
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 1],
                ],
            ]);
        $res1->assertStatus(201);
        $orderId = $res1->json('data.id');

        // Request kedua dengan key yang sama -> return pesanan yang sama tanpa potong stok lagi
        $res2 = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->withHeader('Idempotency-Key', $idempotencyKey)
            ->postJson('/api/v1/pesanan', [
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 1],
                ],
            ]);
        $res2->assertStatus(200);
        $this->assertEquals($orderId, $res2->json('data.id'));
    }

    public function test_batal_pesanan_mengembalikan_stok(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();

        $stokAwal = $barang->stok;

        // Buat pesanan 5 Slop (memotong 1 Bal)
        $resOrder = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/pesanan', [
                'items' => [
                    ['barang_id' => $barang->id, 'satuan' => 'Slop', 'jumlah' => 5],
                ],
            ]);
        $orderId = $resOrder->json('data.id');
        $this->assertEquals($stokAwal - 1, $barang->fresh()->stok);

        // Batalkan pesanan
        $resBatal = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/pesanan/{$orderId}/batal");

        $resBatal->assertStatus(200)
            ->assertJsonPath('data.status', 'Dibatalkan');

        // Stok kembali utuh
        $this->assertEquals($stokAwal, $barang->fresh()->stok);
    }

    public function test_batal_pesanan_gagal_jika_status_bukan_baru_masuk(): void
    {
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-PROSES-' . Str::random(8),
            'user_id' => $mitra->id,
            'barang_id' => $barang->id,
            'nama_mitra' => $mitra->nama_toko,
            'telepon' => $mitra->telepon,
            'alamat' => $mitra->alamat,
            'jumlah' => 1,
            'satuan' => 'Bal',
            'total_harga' => 1000000,
            'status' => 'Diproses', // Sudah diproses
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/pesanan/{$trx->id}/batal");

        $response->assertStatus(422);
    }

    public function test_konfirmasi_terima_berhasil_dengan_upload_foto(): void
    {
        Storage::fake('public');
        $mitra = $this->createMitra();
        $token = $mitra->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();

        $trx = Transaksi::create([
            'kode_transaksi' => 'TRX-DELIV-' . Str::random(8),
            'user_id' => $mitra->id,
            'barang_id' => $barang->id,
            'nama_mitra' => $mitra->nama_toko,
            'telepon' => $mitra->telepon,
            'alamat' => $mitra->alamat,
            'jumlah' => 1,
            'satuan' => 'Bal',
            'total_harga' => 1000000,
            'status' => 'Dikirim',
        ]);

        $foto = UploadedFile::fake()->image('bukti_terima.jpg', 600, 600);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/pesanan/{$trx->id}/konfirmasi-terima", [
                'foto_bukti' => $foto,
                'catatan_penerima' => 'Barang diterima lengkap dan rapi.',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'Selesai');

        $this->assertEquals('Selesai', $trx->fresh()->status);
        $this->assertNotNull($trx->fresh()->bukti_penerimaan);
        $this->assertNotNull($trx->fresh()->diterima_pada);
    }

    public function test_otorisasi_antar_user_tidak_bisa_mengakses_pesanan_user_lain(): void
    {
        $userA = $this->createMitra(['name' => 'User A']);
        $userB = $this->createMitra(['name' => 'User B']);

        $tokenB = $userB->createToken('test')->plainTextToken;
        $barang = Barang::aktif()->first();

        // Buat pesanan milik User A
        $trxA = Transaksi::create([
            'kode_transaksi' => 'TRX-A-' . Str::random(8),
            'user_id' => $userA->id,
            'barang_id' => $barang->id,
            'nama_mitra' => $userA->nama_toko,
            'telepon' => $userA->telepon,
            'alamat' => $userA->alamat,
            'jumlah' => 1,
            'satuan' => 'Bal',
            'total_harga' => 1000000,
            'status' => 'Baru Masuk',
        ]);

        // User B mencoba melihat detail pesanan User A -> 404
        $this->withHeader('Authorization', 'Bearer ' . $tokenB)
            ->getJson("/api/v1/pesanan/{$trxA->id}")
            ->assertStatus(404);

        // User B mencoba membatalkan pesanan User A -> 404
        $this->withHeader('Authorization', 'Bearer ' . $tokenB)
            ->postJson("/api/v1/pesanan/{$trxA->id}/batal")
            ->assertStatus(404);
    }
}
