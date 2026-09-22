@extends('layouts.admin')

@section('title', 'Profil Akun Saya')
@section('header_title', 'Pengaturan Profil Pengguna')

@section('content')
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 28px; max-width: 600px;">
    <h3 style="font-size: 16px; color: #1e293b; margin-bottom: 20px;">
        <i class="fa-solid fa-user-gear"></i> Informasi Akun Anda
    </h3>

    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Nama Lengkap <span style="color: #dc2626;">*</span></label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Alamat Email <span style="color: #dc2626;">*</span></label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Tingkatan Role / Hak Akses</label>
            <input type="text" value="{{ strtoupper($user->role) }}" disabled
                   style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; background: #f8fafc; color: #64748b; border-radius: 6px;">
            <small style="color: #94a3b8; font-size: 11px;">Role hanya dapat diubah oleh Owner / Super Admin.</small>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Ganti Kata Sandi (Opsional)</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti sandi"
                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
        </div>

        <button type="submit" style="background: var(--admin-gold); color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; cursor: pointer;">
            <i class="fa-solid fa-save"></i> Perbarui Profil Saya
        </button>
    </form>
</div>
@endsection
