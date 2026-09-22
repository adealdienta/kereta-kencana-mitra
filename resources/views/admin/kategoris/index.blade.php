@extends('layouts.admin')

@section('title', 'Kelola Kategori Produk')
@section('header_title', 'Kategori Sigaret Kretek (SKM / SKT)')

@section('content')
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="font-size: 16px; color: #1e293b;">Daftar Kategori Produk</h3>
            <p style="color: #64748b; font-size: 13px;">Klasifikasi jenis rokok kretek mesin & kretek tangan</p>
        </div>
        <a href="{{ route('admin.kategoris.create') }}" style="background: var(--admin-gold); color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 13px;">
            <i class="fa-solid fa-plus"></i> Tambah Kategori
        </a>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                <th style="padding: 12px;">Nama Kategori</th>
                <th style="padding: 12px;">Slug</th>
                <th style="padding: 12px;">Deskripsi Karakteristik</th>
                <th style="padding: 12px; text-align: center;">Jumlah Varian</th>
                <th style="padding: 12px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kategoris as $k)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px; font-weight: 700; color: #1e293b;">{{ $k->nama_kategori }}</td>
                    <td style="padding: 12px; font-family: monospace; color: #0284c7;">{{ $k->slug }}</td>
                    <td style="padding: 12px; color: #64748b;">{{ $k->deskripsi ?? '-' }}</td>
                    <td style="padding: 12px; text-align: center; font-weight: 700;">
                        <span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 4px;">
                            {{ $k->barangs_count }} Produk
                        </span>
                    </td>
                    <td style="padding: 12px; text-align: center;">
                        <div style="display: inline-flex; gap: 8px;">
                            <a href="{{ route('admin.kategoris.edit', $k->id) }}" style="color: #0284c7;" title="Edit Kategori">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.kategoris.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $k->nama_kategori }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer;" title="Hapus Kategori">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
