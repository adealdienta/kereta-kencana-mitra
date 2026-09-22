@extends('layouts.admin')

@section('title', 'Edit Produk ' . $barang->nama)
@section('header_title', 'Perbarui Spesifikasi Produk Rokok')

@section('content')
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 28px; max-width: 900px;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('admin.barangs.index') }}" style="color: #64748b; text-decoration: none; font-size: 13px;">
            &larr; Kembali ke Daftar Produk
        </a>
    </div>

    <form action="{{ route('admin.barangs.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Nama Produk <span style="color: #dc2626;">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $barang->nama) }}" required
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Kode Barang <span style="color: #dc2626;">*</span></label>
                <input type="text" name="kode_barang" value="{{ old('kode_barang', $barang->kode_barang) }}" required
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Kategori Sigaret <span style="color: #dc2626;">*</span></label>
                <select name="kategori_id" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id', $barang->kategori_id) == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Status Stok</label>
                <select name="status_stok" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
                    <option value="Ready Stock" {{ old('status_stok', $barang->status_stok) == 'Ready Stock' ? 'selected' : '' }}>Ready Stock</option>
                    <option value="Pre-Order" {{ old('status_stok', $barang->status_stok) == 'Pre-Order' ? 'selected' : '' }}>Pre-Order</option>
                    <option value="Habis" {{ old('status_stok', $barang->status_stok) == 'Habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Profil Rasa & Karakteristik</label>
            <input type="text" name="profil_rasa" value="{{ old('profil_rasa', $barang->profil_rasa) }}"
                   style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Deskripsi Narasi Produk</label>
            <textarea name="deskripsi" rows="3" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
        </div>

        <!-- Spesifikasi Kemasan & Harga -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px; margin-bottom: 20px;">
            <h4 style="font-size: 14px; margin-bottom: 12px; color: #1e293b;">Spesifikasi Kemasan & Harga Bal</h4>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Batang / Bungkus</label>
                    <input type="number" name="batang_per_bungkus" value="{{ old('batang_per_bungkus', $barang->batang_per_bungkus) }}" required min="1"
                           style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Bungkus / Slop</label>
                    <input type="number" name="bungkus_per_slop" value="{{ old('bungkus_per_slop', $barang->bungkus_per_slop) }}" required min="1"
                           style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Slop / Bal</label>
                    <input type="number" name="slop_per_bal" value="{{ old('slop_per_bal', $barang->slop_per_bal) }}" required min="1"
                           style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Harga Bal (Rp)</label>
                    <input type="number" name="harga_per_bal" value="{{ old('harga_per_bal', (int)$barang->harga_per_bal) }}" required min="0"
                           style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Harga Slop (Rp)</label>
                    <input type="number" name="harga_per_slop" value="{{ old('harga_per_slop', (int)$barang->harga_per_slop) }}" min="0" placeholder="Otomatis"
                           style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    <small style="font-size: 10px; color: #64748b;">(Otomatis = Bal / Slop)</small>
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Min. Order Slop</label>
                    <input type="number" name="min_order_slop" value="{{ old('min_order_slop', $barang->min_order_slop ?? 1) }}" required min="1"
                           style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div>
                    <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Min. Order Bal</label>
                    <input type="number" name="min_order_bal" value="{{ old('min_order_bal', $barang->min_order_bal ?? 1) }}" required min="1"
                           style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
            </div>

            <div>
                <label style="font-size: 12px; font-weight: 600; display: block; margin-bottom: 4px;">Stok Tersedia (Bal)</label>
                <input type="number" name="stok" value="{{ old('stok', $barang->stok) }}" required min="0"
                       style="width: 250px; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px;">
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Kadar Tar (mg)</label>
                <input type="number" step="0.1" name="tar_mg" value="{{ old('tar_mg', $barang->tar_mg) }}"
                       style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
            <div>
                <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Kadar Nikotin (mg)</label>
                <input type="number" step="0.1" name="nikotin_mg" value="{{ old('nikotin_mg', $barang->nikotin_mg) }}"
                       style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px;">
            </div>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px;">Ganti Foto Produk</label>
            @if($barang->gambar)
                <div style="margin-bottom: 8px;">
                    <img src="{{ $barang->image_url }}" alt="Preview" style="height: 60px; border-radius: 4px; border: 1px solid #cbd5e1;">
                </div>
            @endif
            <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff;">
            <small style="color: #64748b; font-size: 11px;">Biarkan kosong jika tidak ingin mengganti gambar.</small>
        </div>

        <div style="margin-bottom: 24px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13px;">
                <input type="checkbox" name="aktif" value="1" {{ old('aktif', $barang->aktif) ? 'checked' : '' }}>
                <span>Publikasikan dan Tampilkan di Katalog Website</span>
            </label>
        </div>

        <button type="submit" class="btn btn-gold" style="background: var(--admin-gold); color: #fff; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; cursor: pointer;">
            <i class="fa-solid fa-save"></i> Perbarui Produk
        </button>
    </form>
</div>
@endsection
