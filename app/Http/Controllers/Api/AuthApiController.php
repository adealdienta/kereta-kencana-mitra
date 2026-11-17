<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RegisterApiRequest;
use App\Http\Requests\Api\LoginApiRequest;
use App\Http\Requests\Api\UpdateProfileApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use App\Helpers\ActivityLogger;
use Carbon\Carbon;

class AuthApiController extends ApiController
{
    /**
     * Registrasi Akun Mitra Baru (Role Pelanggan)
     * POST /api/v1/register
     */
    public function register(RegisterApiRequest $request)
    {
        $validated = $request->validated();
        $normalizedPhone = static::normalizePhoneNumber($validated['telepon']);

        $user = User::create([
            'name' => $validated['name'],
            'nama_toko' => $validated['nama_toko'],
            'email' => strtolower(trim($validated['email'])),
            'telepon' => $normalizedPhone,
            'alamat' => $validated['alamat'],
            'password' => Hash::make($validated['password']),
            'role' => 'pelanggan',
        ]);

        $token = $user->createToken('mobile-mitra')->plainTextToken;

        ActivityLogger::log('API Register Mitra', "Mitra toko '{$user->nama_toko}' mendaftar via Mobile API");

        return $this->successResponse([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nama_toko' => $user->nama_toko,
                'email' => $user->email,
                'telepon' => $user->telepon,
                'alamat' => $user->alamat,
                'foto_profil_url' => $user->foto_profil_url,
                'role' => $user->role,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ], 'Pendaftaran kemitraan berhasil! Selamat datang di PR. Kereta Kencana.', 201);
    }

    /**
     * Masuk / Login Mitra via Email atau Nomor WhatsApp
     * POST /api/v1/login
     */
    public function login(LoginApiRequest $request)
    {
        $identifier = $request->input('identifier') ?? $request->input('login') ?? $request->input('email') ?? $request->input('telepon');
        $identifier = trim($identifier);
        $password = $request->input('password');

        $query = User::query();

        if (str_contains($identifier, '@')) {
            $query->where('email', strtolower($identifier));
        } else {
            $normalized = static::normalizePhoneNumber($identifier);
            $query->where(function ($q) use ($identifier, $normalized) {
                $q->where('telepon', $identifier)
                  ->orWhere('telepon', $normalized)
                  ->orWhere('email', strtolower($identifier));
            });
        }

        $user = $query->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return $this->errorResponse('Kombinasi email/nomor WhatsApp dan kata sandi tidak cocok.', 401);
        }

        if ($user->role !== 'pelanggan') {
            return $this->errorResponse('Akses aplikasi mobile khusus untuk akun mitra (pelanggan). Akun internal silakan gunakan portal web.', 403);
        }

        $token = $user->createToken('mobile-mitra')->plainTextToken;

        ActivityLogger::log('API Login Mitra', "Mitra '{$user->nama_toko}' berhasil masuk via Mobile API");

        return $this->successResponse([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nama_toko' => $user->nama_toko,
                'email' => $user->email,
                'telepon' => $user->telepon,
                'alamat' => $user->alamat,
                'foto_profil_url' => $user->foto_profil_url,
                'role' => $user->role,
                'created_at' => $user->created_at?->toIso8601String(),
            ],
        ], 'Berhasil masuk ke akun mitra.');
    }

    /**
     * Keluar / Logout (Hapus Token Aktif)
     * POST /api/v1/logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        ActivityLogger::log('API Logout Mitra', "Mitra '{$user?->nama_toko}' keluar dari sesi mobile");

        return $this->successResponse(null, 'Berhasil keluar dari sesi akun.');
    }

    /**
     * Lihat Profil Pengguna yang Sedang Login
     * GET /api/v1/profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'nama_toko' => $user->nama_toko,
            'email' => $user->email,
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'foto_profil_url' => $user->foto_profil_url,
            'role' => $user->role,
            'created_at' => $user->created_at?->toIso8601String(),
        ], 'Data profil mitra berhasil dimuat.');
    }

    /**
     * Perbarui Profil & Alamat Toko Mitra
     * PUT /api/v1/profile
     */
    public function updateProfile(UpdateProfileApiRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->nama_toko = $validated['nama_toko'];
        $user->email = strtolower(trim($validated['email']));
        $user->telepon = static::normalizePhoneNumber($validated['telepon']);
        $user->alamat = $validated['alamat'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLogger::log('API Update Profil', "Mitra '{$user->nama_toko}' memperbarui data profil via mobile");

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'nama_toko' => $user->nama_toko,
            'email' => $user->email,
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'foto_profil_url' => $user->foto_profil_url,
            'role' => $user->role,
            'updated_at' => $user->updated_at?->toIso8601String(),
        ], 'Profil mitra dan alamat toko berhasil diperbarui.');
    }

    /**
     * Unggah Foto Profil / Avatar Toko Mitra
     * POST /api/v1/profile/foto
     */
    public function uploadFotoProfil(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ], [
            'foto.required' => 'Berkas foto profil wajib diunggah.',
            'foto.image' => 'Berkas harus berupa gambar.',
            'foto.mimes' => 'Format gambar yang diperbolehkan: JPEG, PNG, JPG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        $user = $request->user();

        // Hapus foto lama jika ada di storage publik
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $path = $request->file('foto')->store('profil', 'public');
        $user->foto_profil = $path;
        $user->save();

        ActivityLogger::log('API Upload Foto Profil', "Mitra '{$user->nama_toko}' memperbarui foto profil via mobile");

        return $this->successResponse([
            'id' => $user->id,
            'name' => $user->name,
            'nama_toko' => $user->nama_toko,
            'email' => $user->email,
            'telepon' => $user->telepon,
            'alamat' => $user->alamat,
            'foto_profil_url' => $user->foto_profil_url,
            'role' => $user->role,
            'updated_at' => $user->updated_at?->toIso8601String(),
        ], 'Foto profil mitra berhasil diperbarui.');
    }

    /**
     * Endpoint API Permintaan Reset Sandi dari Aplikasi Mobile
     * POST /api/v1/forgot-password
     */
    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', strtolower(trim($validated['email'])))->first();

        if (!$user) {
            return $this->errorResponse('Alamat email tidak terdaftar dalam sistem mitra kami.', 404);
        }

        // Generate token dan OTP 6 digit
        $token = Str::random(64);
        $otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        Cache::put(
            $this->resetOtpCacheKey($user->email),
            Hash::make($otpCode),
            now()->addMinutes(60)
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $token, $resetUrl, $otpCode));
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengirim email reset sandi. Pastikan konfigurasi server email aktif.', 500, [
                'error_detail' => config('app.debug') ? $e->getMessage() : null,
            ]);
        }

        ActivityLogger::log('API Reset Sandi Mobile', "Permintaan reset sandi via Mobile API untuk {$user->email}");

        return $this->successResponse([
            'email' => $user->email,
            'token' => $token,
        ], 'Kode verifikasi 6-digit dan tautan reset sandi telah berhasil dikirimkan ke email Anda.');
    }

    /**
     * Endpoint API Verifikasi Token / OTP dari Aplikasi Mobile
     * POST /api/v1/verify-reset-token
     */
    public function verifyToken(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'token.required' => 'Token verifikasi wajib disertakan.',
        ]);

        $email = strtolower(trim($request->email));
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !$this->matchesResetCredential($email, $request->token, $record->token)) {
            return response()->json([
                'success' => false,
                'status' => 'error',
                'valid' => false,
                'message' => 'Kode verifikasi atau token tidak valid atau tidak ditemukan.',
                'errors' => ['valid' => false],
            ], 422);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            $this->clearResetCredentials($email);
            return response()->json([
                'success' => false,
                'status' => 'error',
                'valid' => false,
                'message' => 'Kode verifikasi atau token telah kedaluwarsa.',
                'errors' => ['valid' => false],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'status' => 'success',
            'valid' => true,
            'message' => 'Kode verifikasi sah dan dapat digunakan untuk membuat kata sandi baru.',
            'data' => [
                'valid' => true,
                'email' => $email,
            ],
        ], 200);
    }

    /**
     * Endpoint API Reset Sandi Baru dari Aplikasi Mobile
     * POST /api/v1/reset-password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'token.required' => 'Token verifikasi wajib disertakan.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $email = strtolower(trim($request->email));
        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record || !$this->matchesResetCredential($email, $request->token, $record->token)) {
            return $this->errorResponse('Kode verifikasi atau token tidak valid atau telah usang.', 422);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            $this->clearResetCredentials($email);
            return $this->errorResponse('Kode verifikasi atau token telah kedaluwarsa.', 422);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return $this->errorResponse('Akun pengguna tidak ditemukan.', 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $this->clearResetCredentials($email);

        ActivityLogger::log('API Sandi Diperbarui', "Kata sandi akun {$user->email} berhasil diubah melalui Mobile API");

        return $this->successResponse(null, 'Kata sandi akun Anda berhasil diperbarui. Silakan login ke dalam aplikasi mobile.');
    }

    protected function resetOtpCacheKey(string $email): string
    {
        return 'mobile_reset_otp:' . strtolower(trim($email));
    }

    protected function matchesResetCredential(string $email, string $input, string $storedTokenHash): bool
    {
        if (Hash::check($input, $storedTokenHash)) {
            return true;
        }

        $otpHash = Cache::get($this->resetOtpCacheKey($email));
        return is_string($otpHash) && Hash::check($input, $otpHash);
    }

    protected function clearResetCredentials(string $email): void
    {
        $normalized = strtolower(trim($email));
        DB::table('password_reset_tokens')->where('email', $normalized)->delete();
        Cache::forget($this->resetOtpCacheKey($normalized));
    }
}
