@extends('layouts.admin')

@section('title', 'Edit Kategori ' . $kategori->nama_kategori)
@section('header_title', 'Perbarui Kategori Sigaret')

@section('content')
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px; max-width: 600px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.kategoris.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px;">
            &larr; Kembali ke Daftar Kategori
        </a>
    </div>

    <form action="{{ route('admin.kategoris.update', $kategori->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Nama Kategori <span style="color: #dc2626;">*</span></label>
            <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" required
                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Deskripsi Kategori</label>
            <textarea name="deskripsi" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
        </div>

        <button type="submit" style="background: var(--admin-gold); color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 700; cursor: pointer;">
            <i class="fa-solid fa-save"></i> Perbarui Kategori
        </button>
    </form>
</div>
@endsection
