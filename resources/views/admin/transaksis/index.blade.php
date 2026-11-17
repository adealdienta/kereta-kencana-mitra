@extends('layouts.admin')

@section('title', 'Kelola Pesanan Distributor B2B')
@section('header_title', 'Manajemen Pesanan & Distribusi Armada B2B')

@section('content')
<div class="admin-card" style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
    <!-- Filter & Tombol Aksi -->
    <div class="admin-filter-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 16px;">
        <form class="admin-filter-form" action="{{ route('admin.transaksis.index') }}" method="GET" style="display: flex; gap: 8px; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode, mitra, no. DO, resi..." 
                   style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; min-width: 220px;">
            
            <select name="status" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                <option value="">-- Semua Status --</option>
                <option value="Baru Masuk" {{ request('status') === 'Baru Masuk' ? 'selected' : '' }}>Baru Masuk</option>
                <option value="Diproses" {{ request('status') === 'Diproses' ? 'selected' : '' }}>Diproses Gudang</option>
                <option value="Dikirim" {{ request('status') === 'Dikirim' ? 'selected' : '' }}>Sedang Dikirim</option>
                <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai / Diterima</option>
                <option value="Dibatalkan" {{ request('status') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>

            <select name="sumber" style="padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                <option value="">-- Semua Saluran --</option>
                <option value="Web" {{ request('sumber') === 'Web' ? 'selected' : '' }}>Pesanan Online Web</option>
                <option value="Mobile App" {{ request('sumber') === 'Mobile App' ? 'selected' : '' }}>Aplikasi Mobile</option>
                <option value="Offline" {{ request('sumber') === 'Offline' ? 'selected' : '' }}>Langsung Pabrik (Offline)</option>
            </select>

            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 8px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="fa-solid fa-filter"></i> Saring Data
            </button>
        </form>

        <div class="admin-action-buttons" style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('admin.transaksis.offline') }}" style="background: #10b981; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-cash-register"></i> + Transaksi Langsung di Pabrik (Offline)
            </a>
            <a href="{{ route('pesanan.form') }}" target="_blank" style="background: #121619; color: #fff; padding: 8px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-cart-plus"></i> Form Order Web
            </a>
        </div>
    </div>

    <!-- Tabel Pesanan -->
    <div class="table-wrap" style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                    <th style="padding: 12px;">No. Transaksi</th>
                    <th style="padding: 12px;">Tanggal</th>
                    <th style="padding: 12px;">Nama Mitra / Toko</th>
                    <th style="padding: 12px;">Varian Produk</th>
                    <th style="padding: 12px; text-align: center;">Jumlah</th>
                    <th style="padding: 12px; text-align: right;">Total Nilai</th>
                    <th style="padding: 12px;">Bukti Terima</th>
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
                            <div style="font-size: 10.5px; margin-top: 2px;">
                                @if(str_contains($t->sumber, 'Offline'))
                                    <span style="background: #ecfdf5; color: #047857; padding: 2px 6px; border-radius: 4px; font-weight: 600;">
                                        <i class="fa-solid fa-store"></i> Kasir Pabrik
                                    </span>
                                @else
                                    <span style="color: #64748b;">
                                        <i class="fa-solid fa-globe"></i> {{ $t->sumber }}
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 10.5px; margin-top: 3px;">
                                @if($t->metode_pembayaran && stripos($t->metode_pembayaran, 'transfer') !== false)
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 1px 5px; border-radius: 3px; font-weight: 600;">
                                        Transfer: {{ $t->status_pembayaran ?? 'Belum Dibayar' }}
                                    </span>
                                @elseif(str_contains($t->sumber, 'Offline') || ($t->metode_pembayaran && stripos($t->metode_pembayaran, 'tunai') !== false))
                                    <span style="background: #dcfce7; color: #166534; padding: 1px 5px; border-radius: 3px; font-weight: 600;">
                                        Tunai: {{ $t->status_pembayaran ?? 'Lunas' }}
                                    </span>
                                @else
                                    <span style="background: #fef3c7; color: #92400e; padding: 1px 5px; border-radius: 3px; font-weight: 600;">
                                        COD &bull; {{ $t->status_pembayaran ?? ($t->status === 'Selesai' ? 'Lunas' : 'Belum Dibayar') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td style="padding: 12px; color: #64748b;">{{ $t->created_at->format('d/m/y H:i') }}</td>
                        <td style="padding: 12px;">
                            <strong>{{ $t->nama_mitra }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $t->telepon }}</div>
                        </td>
                        <td style="padding: 12px;">
                            {{ $t->ringkasan_item }}
                            @if($t->barang && (!$t->details || $t->details->count() <= 1))
                                <div style="font-size: 11px; color: #94a3b8;">{{ $t->barang->kategori?->nama_kategori }}</div>
                            @elseif($t->details && $t->details->count() > 1)
                                <div style="font-size: 11px; color: #94a3b8;">{{ $t->details->count() }} Varian Campuran</div>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center; font-weight: 700;">
                            {{ $t->jumlah }} <span style="font-size: 11px; font-weight: normal; color: #64748b;">{{ $t->satuan ?? 'Slop' }}</span>
                        </td>
                        <td style="padding: 12px; text-align: right; font-weight: 700; color: #0f1316;">
                            {{ $t->formatted_total }}
                        </td>
                        <td style="padding: 12px;">
                            @if($t->bukti_penerimaan)
                                <a href="{{ asset('storage/' . $t->bukti_penerimaan) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 4px; color: #059669; font-weight: 600; text-decoration: none; font-size: 11.5px; background: #ecfdf5; padding: 3px 8px; border-radius: 4px;">
                                    <i class="fa-solid fa-image"></i> Lihat Foto
                                </a>
                            @elseif($t->status === 'Dikirim')
                                @if(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
                                    <a href="{{ route('admin.transaksis.show', $t->id) }}" style="display: inline-flex; align-items: center; gap: 4px; background: #f0fdf4; border: 1px solid #86efac; color: #166534; padding: 3px 6px; border-radius: 4px; font-size: 11px; text-decoration: none; font-weight: 600;" title="Buka detail transaksi untuk mengunggah foto surat jalan fisik">
                                        <i class="fa-solid fa-stamp"></i> Verif Surat Jalan
                                    </a>
                                @else
                                    <span style="color: #94a3b8; font-size: 11px;" title="Menunggu konfirmasi penerimaan dari mitra melalui website atau aplikasi mobile">
                                        <i class="fa-solid fa-clock"></i> Tunggu Mitra
                                    </span>
                                @endif
                            @else
                                <span style="color: #cbd5e1; font-size: 11px;">-</span>
                            @endif
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            @php
                                $color = match($t->status) {
                                    'Baru Masuk' => 'background: #fef3c7; color: #92400e;',
                                    'Diproses' => 'background: #e0f2fe; color: #0369a1;',
                                    'Dikirim' => 'background: #f3e8ff; color: #7e22ce;',
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

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $transaksis->links() }}
    </div>
</div>
@endsection
