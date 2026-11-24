<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\ResetPasswordMail;

class UserController extends Controller
{
    public function index()
    {
        // Hanya menampilkan akun admin internal pabrik (Owner, Super Admin, Admin)
        $users = User::whereIn('role', ['owner', 'superadmin', 'admin'])->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:owner,superadmin,admin'],
        ]);

        if ($validated['role'] === 'owner' && !auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang berwenang menambahkan akun dengan role Owner.');
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLogger::log('Tambah Pengguna', "Menambahkan akun baru: {$user->name} ({$user->role})");

        return back()->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->isPelanggan()) {
            abort(403, 'Akun mitra toko tidak dapat diubah melalui manajemen staf internal.');
        }

        if ($user->isOwner() && !auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang berwenang mengubah akun Owner.');
        }

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:owner,superadmin,admin'],
        ]);

        if ($validated['role'] === 'owner' && !auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang berwenang menetapkan role Owner.');
        }

        if ($user->id === auth()->id() && $validated['role'] !== $user->role) {
            return back()->with('error', 'Anda tidak dapat menurunkan atau mengubah tingkatan role akun Anda sendiri.');
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
            $user->tokens()->delete();
        }

        $user->save();

        ActivityLogger::log('Ubah Pengguna', "Memperbarui akun: {$user->name}");

        return back()->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Proteksi integritas data: Akun mitra toko tidak boleh dihapus oleh siapa pun
        if ($user->isPelanggan()) {
            return back()->with('error', 'Akun mitra toko tidak dapat dihapus demi menjaga integritas riwayat transaksi dan audit perizinan cukai pabrik.');
        }

        if ($user->isOwner() && !auth()->user()->isOwner()) {
            abort(403, 'Hanya Owner yang berwenang menghapus akun Owner.');
        }

        $nama = $user->name;
        $user->delete();

        ActivityLogger::log('Hapus Pengguna', "Menghapus akun staf: {$nama}");

        return back()->with('success', 'Akun staf berhasil dihapus.');
    }

    /**
     * Menampilkan daftar mitra toko terdaftar (BKPM Acara 23 - Filter & Pencarian)
     * Dapat diakses oleh Admin Gudang (Read-Only), Super Admin, dan Owner.
     */
    public function mitraIndex(Request $request)
    {
        $query = User::where('role', 'pelanggan');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('nama_toko', 'like', "%{$s}%")
                  ->orWhere('telepon', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $mitras = $query->latest()->paginate(10)->withQueryString();

        return view('admin.mitra.index', compact('mitras'));
    }

    /**
     * Kirimkan tautan atur ulang kata sandi resmi ke email mitra toko
     * Mencegah staf mengarang/mengetahui kata sandi mitra (perlindungan privasi data pelanggan).
     */
    public function kirimResetLinkMitra(Request $request, $id)
    {
        $mitra = User::where('role', 'pelanggan')->findOrFail($id);

        // Generate token aman dan OTP 6 digit
        $token = Str::random(64);
        $otpCode = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $normalizedEmail = strtolower(trim($mitra->email));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $normalizedEmail],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Simpan juga OTP 6 digit di Cache agar mitra bisa memakai kode OTP di Aplikasi Mobile maupun Web
        Cache::put('mobile_reset_otp:' . $normalizedEmail, Hash::make($otpCode), now()->addMinutes(60));
        Cache::forget('mobile_reset_otp_attempts:' . $normalizedEmail);

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $normalizedEmail,
        ]);

        try {
            Mail::to($normalizedEmail)->send(new ResetPasswordMail($mitra, $token, $resetUrl, $otpCode));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email verifikasi ke mitra. Pastikan konfigurasi mail server aktif.');
        }

        $staffName = auth()->user()->name;
        ActivityLogger::log('Bantuan Reset Sandi', "Staf '{$staffName}' memicu pengiriman email reset sandi ke toko: {$mitra->nama_toko} ({$mitra->email})");

        return back()->with('success', "Tautan pengaturan ulang kata sandi telah berhasil dikirimkan ke email resmi mitra ({$mitra->email}). Pemilik toko dapat mengatur kata sandi barunya secara mandiri tanpa campur tangan staf.");
    }

    /**
     * Alias kompatibilitas
     */
    public function resetPasswordMitra(Request $request, $id)
    {
        return $this->kirimResetLinkMitra($request, $id);
    }
}
