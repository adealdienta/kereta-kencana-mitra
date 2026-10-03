<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\ActivityLogger;

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
}
