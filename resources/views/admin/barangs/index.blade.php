@extends('layouts.admin')

@section('title', 'Kelola Produk Rokok SKM & SKT')
@section('header_title', 'Katalog & Manajemen Produk Rokok')

@section('content')
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <form action="{{ route('admin.barangs.index') }}" method="GET" style="display: flex; gap: 8px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..." 
                   style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
            <select name="kategori_id" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                <option value="">-- Semua Kategori --</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>
                        {{ $k->nama_kategori }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-filter"></i> Filter</button>
        </form>

        <a href="{{ route('admin.barangs.create') }}" class="btn btn-gold btn-sm" style="background: var(--admin-gold); color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-weight: 600;">
            <i class="fa-solid fa-plus"></i> Tambah Produk Baru
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                    <th style="padding: 12px;">Foto</th>
                    <th style="padding: 12px;">Kode & Nama Produk</th>
                    <th style="padding: 12px;">Kategori</th>
                    <th style="padding: 12px;">Kemasan</th>
                    <th style="padding: 12px; text-align: right;">Harga Slop & Bal</th>
                    <th style="padding: 12px; text-align: center;">Stok</th>
                    <th style="padding: 12px; text-align: center;">Status</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($barangs as $b)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 10px;">
                            <img src="{{ $b->image_url }}" alt="{{ $b->nama }}" style="width: 50px; height: 40px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td style="padding: 10px;">
                            <strong style="color: #1e293b;">{{ $b->nama }}</strong>
                            <div style="font-size: 11px; font-family: monospace; color: #64748b;">{{ $b->kode_barang }}</div>
                        </td>
                        <td style="padding: 10px;">{{ $b->kategori->nama_kategori }}</td>
                        <td style="padding: 10px;">
                            {{ $b->batang_per_bungkus }} Btg | {{ $b->bungkus_per_slop }} Bks/Slop | {{ $b->slop_per_bal }} Slop/Bal
                        </td>
                        <td style="padding: 10px; text-align: right;">
                            <div style="font-weight: 700; color: #b45309;">{{ $b->formatted_harga_slop }} <span style="font-weight: normal; font-size: 11px; color: #64748b;">/ Slop</span></div>
                            <div style="font-size: 11px; color: #475569;">{{ $b->formatted_harga }} / Bal</div>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="font-weight: 700; color: {{ $b->stok <= 20 ? '#dc2626' : '#16a34a' }};">
                                {{ $b->stok }} Bal
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <span style="background: {{ $b->aktif ? '#ecfdf5' : '#fef2f2' }}; color: {{ $b->aktif ? '#059669' : '#dc2626' }}; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 11px;">
                                {{ $b->aktif ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </td>
                        <td style="padding: 10px; text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('admin.barangs.edit', $b->id) }}" style="color: #0284c7; padding: 4px 8px; font-size: 13px;" title="Edit Produk">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.barangs.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $b->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px 8px;" title="Hapus Produk">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 24px; color: #94a3b8;">Belum ada data produk rokok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $barangs->links() }}
    </div>
</div>
@endsection
