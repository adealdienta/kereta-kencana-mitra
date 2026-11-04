<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Helpers\ActivityLogger;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isPelanggan()) {
                return redirect()->route('pesanan.saya');
            }
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            if (Auth::user()->isPelanggan()) {
                return redirect()->route('pesanan.form');
            }
            return redirect()->route('admin.dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nama_toko' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'unique:users,email'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:500'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nama_toko.required' => 'Nama toko / warung / mitra usaha wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.',
            'telepon.required' => 'Nomor WhatsApp / telepon wajib diisi untuk koordinasi pengiriman.',
            'alamat.required' => 'Alamat lengkap pengiriman barang wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'nama_toko' => $validated['nama_toko'],
            'email' => $validated['email'],
            'telepon' => $validated['telepon'],
            'alamat' => $validated['alamat'],
            'password' => Hash::make($validated['password']),
            'role' => 'pelanggan',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::log('Registrasi Mitra', "Mitra toko '{$user->nama_toko}' berhasil mendaftar akun baru");

        return redirect()->route('pesanan.form')
            ->with('success', 'Pendaftaran berhasil! Selamat datang, ' . $user->name . ' (' . $user->nama_toko . '). Silakan lanjutkan formulir pemesanan rokok Anda.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            ActivityLogger::log('Login Sistem', 'Pengguna berhasil masuk: ' . Auth::user()->name);

            if (Auth::user()->isPelanggan()) {
                return redirect()->intended(route('pesanan.saya'))
                    ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . ' (' . (Auth::user()->nama_toko ?? 'Mitra Usaha') . ')!');
            }

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan kata sandi tidak cocok.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        ActivityLogger::log('Logout Sistem', 'Pengguna keluar dari sistem');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('success', 'Anda telah berhasil keluar dari sesi akun.');
    }

    public function profile()
    {
        return view('admin.profil', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nama_toko' => ['nullable', 'string', 'max:150'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'min:6'],
        ]);

        $user->name = $validated['name'];
        if (isset($validated['nama_toko'])) $user->nama_toko = $validated['nama_toko'];
        if (isset($validated['telepon'])) $user->telepon = $validated['telepon'];
        if (isset($validated['alamat'])) $user->alamat = $validated['alamat'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLogger::log('Update Profil', 'Pengguna memperbarui profil pribadi');

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Memperbarui profil toko & kata sandi mandiri oleh mitra yang sedang login
     * (BKPM Acara 15 - 16 Update Data Pengguna)
     */
    public function updateProfileMitra(Request $request)
    {
        $user = Auth::user();

        if (!$user->isPelanggan()) {
            abort(403, 'Aksi hanya diperuntukkan bagi akun mitra toko.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'nama_toko' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string', 'max:500'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'min:6'],
        ], [
            'name.required' => 'Nama pemilik wajib diisi.',
            'nama_toko.required' => 'Nama toko wajib diisi.',
            'telepon.required' => 'Nomor WhatsApp wajib diisi.',
            'alamat.required' => 'Alamat pengiriman toko wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $user->name = $validated['name'];
        $user->nama_toko = $validated['nama_toko'];
        $user->telepon = $validated['telepon'];
        $user->alamat = $validated['alamat'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLogger::log('Update Profil Mitra', "Mitra {$user->nama_toko} ({$user->name}) memperbarui profil akun / kata sandi");

        return back()->with('success', 'Profil toko dan data akun Anda berhasil diperbarui.');
    }

    /**
     * Tampilkan formulir permintaan atur ulang kata sandi mandiri
     */
    public function showForgotPassword()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isPelanggan() ? 'pesanan.saya' : 'admin.dashboard');
        }
        return view('auth.forgot-password');
    }

    /**
     * Kirim email tautan atur ulang kata sandi ke email terdaftar mitra
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Alamat email tidak terdaftar dalam sistem mitra kami. Silakan periksa kembali atau hubungi pengelola.');
        }

        // Generate token aman dan OTP 6 digit
        $token = Str::random(64);
        $otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        try {
            Mail::to($user->email)->send(new ResetPasswordMail($user, $token, $resetUrl, $otpCode));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email reset: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email verifikasi. Pastikan konfigurasi mail server aktif.');
        }

        ActivityLogger::log('Permintaan Reset Sandi', "Mitra '{$user->name}' ({$user->email}) meminta tautan reset kata sandi");

        return back()->with('success', 'Tautan pengaturan ulang kata sandi telah berhasil dikirimkan ke email Anda. Silakan periksa kotak masuk (inbox) atau folder spam.');
    }

    /**
     * Tampilkan formulir atur ulang kata sandi baru
     */
    public function showResetPassword($token, Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Proses pembaruan kata sandi baru dengan verifikasi token
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'token.required' => 'Token reset tidak valid atau tidak ditemukan.',
            'email.required' => 'Alamat email wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return back()->withErrors(['email' => 'Permintaan atur ulang kata sandi tidak ditemukan atau sudah kedaluwarsa.']);
        }

        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Token atur ulang kata sandi tidak valid atau telah usang.']);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Tautan atur ulang kata sandi telah kedaluwarsa (lebih dari 60 menit). Silakan ajukan permohonan baru.']);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        ActivityLogger::log('Sandi Diperbarui', "Pengguna {$user->name} ({$user->email}) berhasil mereset kata sandi secara mandiri");

        return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }
}
