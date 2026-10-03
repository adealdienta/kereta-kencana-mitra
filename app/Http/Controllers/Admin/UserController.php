<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\ActivityLogger;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Hanya menampilkan akun staf internal pabrik (Owner, Super Admin, Staff)
        $users = User::whereIn('role', ['owner', 'superadmin', 'staff'])->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:owner,superadmin,staff'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        ActivityLogger::log('Tambah Pengguna', "Menambahkan akun baru: {$user->name} ({$user->role})");

        return back()->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'in:owner,superadmin,staff'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
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
     * Reset kata sandi mitra toko atas permintaan bantuan
     * Hanya dapat dieksekusi oleh Owner & Super Admin demi keamanan.
     */
    public function resetPasswordMitra(Request $request, $id)
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Owner atau Super Admin yang berwenang mereset kata sandi mitra toko.');
        }

        $mitra = User::where('role', 'pelanggan')->findOrFail($id);

        $request->validate([
            'new_password' => ['required', 'string', 'min:6'],
        ], [
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $mitra->password = Hash::make($request->new_password);
        $mitra->save();

        ActivityLogger::log('Reset Sandi Mitra', "Mereset kata sandi mitra toko: {$mitra->nama_toko} ({$mitra->email})");

        return back()->with('success', "Kata sandi untuk toko '{$mitra->nama_toko}' ({$mitra->name}) berhasil diperbarui.");
    }
}
