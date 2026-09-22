@extends('layouts.admin')

@section('title', 'Kelola Pengguna & Staf')
@section('header_title', 'Manajemen Pengguna & Hak Akses Berjenjang (RBAC)')

@section('content')
<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px;">
    <!-- Tabel Pengguna -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
        <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 16px;">
            <i class="fa-solid fa-users-gear"></i> Daftar Akun Pengguna Terdaftar
        </h3>

        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                    <th style="padding: 10px;">Nama</th>
                    <th style="padding: 10px;">Email</th>
                    <th style="padding: 10px; text-align: center;">Tingkatan Role</th>
                    <th style="padding: 10px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px;">
                            <strong>{{ $u->name }}</strong>
                            @if($u->id === auth()->id())
                                <span style="font-size: 10px; background: #e2e8f0; padding: 2px 4px; border-radius: 3px;">(Anda)</span>
                            @endif
                        </td>
                        <td style="padding: 10px; color: #64748b;">{{ $u->email }}</td>
                        <td style="padding: 10px; text-align: center;">
                            <span class="role-badge role-{{ $u->role }}">
                                {{ strtoupper($u->role) }}
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            @if($u->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @else
                                <span style="color: #cbd5e1;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Form Tambah User -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
        <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 16px;">
            <i class="fa-solid fa-user-plus"></i> Tambah Akun Baru
        </h3>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Nama Lengkap <span style="color: #dc2626;">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Ahmad Fauzi"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Alamat Email <span style="color: #dc2626;">*</span></label>
                <input type="email" name="email" required placeholder="staf@keretakencana.com"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Kata Sandi Awal <span style="color: #dc2626;">*</span></label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                       style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 4px;">Tingkatan Akses / Role <span style="color: #dc2626;">*</span></label>
                <select name="role" required style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    <option value="staff">Staff (Operasional Pesanan & Pengiriman)</option>
                    <option value="superadmin">Super Admin (Teknis & Web Developer)</option>
                    <option value="owner">Owner (Hak Akses Penuh Sistem & Bisnis)</option>
                </select>
            </div>

            <button type="submit" style="background: var(--admin-gold); color: #fff; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 700; width: 100%; cursor: pointer;">
                <i class="fa-solid fa-save"></i> Daftarkan Pengguna
            </button>
        </form>
    </div>
</div>
@endsection
