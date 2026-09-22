@extends('layouts.admin')

@section('title', 'Kelola Pesanan Distributor B2B')
@section('header_title', 'Manajemen Pesanan & Distribusi Armada B2B')

@section('content')
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
    <!-- Filter & Pencarian (BKPM Acara 23) -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <form action="{{ route('admin.transaksis.index') }}" method="GET" style="display: flex; gap: 8px; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, mitra, no. DO, resi..." 
                   style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; min-width: 240px;">
            
            <select name="status" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                <option value="">-- Semua Status --</option>
                <option value="Baru Masuk" {{ request('status') === 'Baru Masuk' ? 'selected' : '' }}>Baru Masuk</option>
                <option value="Diproses" {{ request('status') === 'Diproses' ? 'selected' : '' }}>Diproses Pabrik</option>
                <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai / Terkirim</option>
                <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-filter"></i> Saring Data</button>
        </form>

        <a href="{{ route('pesanan.form') }}" target="_blank" style="background: #121619; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600;">
            <i class="fa-solid fa-plus"></i> Input Order Baru (Kasir B2B)
        </a>
    </div>

    <!-- Tabel Pesanan -->
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                    <th style="padding: 12px;">No. Transaksi</th>
                    <th style="padding: 12px;">Tanggal</th>
                    <th style="padding: 12px;">Nama Mitra / Toko</th>
                    <th style="padding: 12px;">Varian Produk</th>
                    <th style="padding: 12px; text-align: center;">Jumlah Bal</th>
                    <th style="padding: 12px; text-align: right;">Total Nilai</th>
                    <th style="padding: 12px;">No. DO / Resi</th>
                    <th style="padding: 12px; text-align: center;">Status</th>
                    <th style="padding: 12px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksis as $t)
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px; font-weight: 700; font-family: monospace;">
                            <a href="{{ route('admin.transaksis.show', $t->id) }}" style="color: #b45309; text-decoration: none;">
                                #{{ $t->kode_transaksi }}
                            </a>
                            <div style="font-size: 10px; color: #94a3b8;">{{ $t->sumber }}</div>
                        </td>
                        <td style="padding: 12px; color: #64748b;">{{ $t->created_at->format('d/m/y H:i') }}</td>
                        <td style="padding: 12px;">
                            <strong>{{ $t->nama_mitra }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $t->telepon }}</div>
                        </td>
                        <td style="padding: 12px;">
                            {{ $t->barang->nama }}
                            <div style="font-size: 11px; color: #94a3b8;">{{ $t->barang->kategori->nama_kategori }}</div>
                        </td>
                        <td style="padding: 12px; text-align: center; font-weight: 700;">
                            {{ $t->jumlah }} <span style="font-size: 11px; font-weight: normal; color: #64748b;">{{ $t->satuan ?? 'Bal' }}</span>
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 700; color: #0f1316;">
                            {{ $t->formatted_total }}
                        </td>
                        <td style="padding: 12px; font-size: 11px; font-family: monospace;">
                            @if($t->nomor_do)
                                <div>DO: {{ $t->nomor_do }}</div>
                            @endif
                            @if($t->nomor_resi)
                                <div style="color: #0284c7;">Resi: {{ $t->nomor_resi }}</div>
                            @endif
                            @if(!$t->nomor_do && !$t->nomor_resi)
                                <span style="color: #cbd5e1;">- Belum Terbit -</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            @php
                                $color = match($t->status) {
                                    'Baru Masuk' => 'background: #fef3c7; color: #92400e;',
                                    'Diproses' => 'background: #e0f2fe; color: #0369a1;',
                                    'Selesai' => 'background: #ecfdf5; color: #065f46;',
                                    'Dibatalkan' => 'background: #fef2f2; color: #991b1b;',
                                    default => 'background: #f1f5f9; color: #475569;'
                                };
                            @endphp
                            <span style="{{ $color }} padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 11px;">
                                {{ $t->status }}
                            </span>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <div style="display: inline-flex; gap: 6px;">
                                <a href="{{ route('admin.transaksis.show', $t->id) }}" style="color: #0284c7; padding: 4px 6px;" title="Detail & Proses">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.transaksis.faktur', $t->id) }}" target="_blank" style="color: #059669; padding: 4px 6px;" title="Cetak Faktur / DO">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <form action="{{ route('admin.transaksis.destroy', $t->id) }}" method="POST" onsubmit="return confirm('Hapus transaksi {{ $t->kode_transaksi }}? Stok akan dipulihkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: none; border: none; color: #dc2626; cursor: pointer; padding: 4px 6px;" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 24px; color: #94a3b8;">Tidak ada data pesanan yang sesuai filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $transaksis->links() }}
    </div>
</div>
@endsection
