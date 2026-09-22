@extends('layouts.admin')

@section('title', 'Dashboard Operasional Pabrik')
@section('header_title', 'Ringkasan Manajemen & Distribusi Pabrik')

@section('content')
<!-- Metric Cards Grid -->
<div class="metrics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 28px;">
    <div class="metric-card" style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: 8px; background: rgba(197, 160, 89, 0.15); color: var(--admin-gold); display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-boxes-stacked"></i>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Varian Produk</div>
            <div style="font-size: 24px; font-weight: 800; color: #1e293b;">{{ $totalProduk }} SKM/SKT</div>
        </div>
    </div>

    <div class="metric-card" style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: 8px; background: rgba(59, 130, 246, 0.12); color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Total Pesanan</div>
            <div style="font-size: 24px; font-weight: 800; color: #1e293b;">{{ $totalPesanan }} Transaksi</div>
        </div>
    </div>

    <div class="metric-card" style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-truck-fast"></i>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Volume Distribusi</div>
            <div style="font-size: 24px; font-weight: 800; color: #1e293b;">{{ number_format($totalBal) }} Bal</div>
        </div>
    </div>

    <div class="metric-card" style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 22px;">
            <i class="fa-solid fa-money-bill-wave"></i>
        </div>
        <div>
            <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 600;">Estimasi Omzet</div>
            <div style="font-size: 20px; font-weight: 800; color: #1e293b;">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Pesanan Terbaru -->
    <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">
                <i class="fa-solid fa-receipt"></i> Pesanan B2B Terbaru
            </h3>
            <a href="{{ route('admin.transaksis.index') }}" style="color: var(--admin-gold); font-size: 13px; font-weight: 600; text-decoration: none;">
                Lihat Semua &rarr;
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left; color: #64748b;">
                        <th style="padding: 10px;">Kode & Tanggal</th>
                        <th style="padding: 10px;">Mitra / Toko</th>
                        <th style="padding: 10px;">Varian Produk</th>
                        <th style="padding: 10px; text-align: center;">Jumlah</th>
                        <th style="padding: 10px; text-align: right;">Total</th>
                        <th style="padding: 10px; text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesananTerbaru as $trx)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 10px;">
                                <a href="{{ route('admin.transaksis.show', $trx->id) }}" style="font-weight: 700; color: #1e293b; text-decoration: none;">
                                    #{{ $trx->kode_transaksi }}
                                </a>
                                <div style="font-size: 11px; color: #94a3b8;">{{ $trx->created_at->format('d/m/Y') }}</div>
                            </td>
                            <td style="padding: 10px;">
                                <strong>{{ $trx->nama_mitra }}</strong>
                                <div style="font-size: 11px; color: #64748b;">{{ $trx->telepon }}</div>
                            </td>
                            <td style="padding: 10px;">{{ $trx->barang->nama }}</td>
                            <td style="padding: 10px; text-align: center; font-weight: 700;">{{ $trx->jumlah }} Bal</td>
                            <td style="padding: 10px; text-align: right; font-weight: 700; color: #0f1316;">
                                {{ $trx->formatted_total }}
                            </td>
                            <td style="padding: 10px; text-align: center;">
                                @php
                                    $badgeColor = match($trx->status) {
                                        'Baru Masuk' => 'background: #fef3c7; color: #92400e;',
                                        'Diproses' => 'background: #e0f2fe; color: #0369a1;',
                                        'Selesai' => 'background: #ecfdf5; color: #065f46;',
                                        'Dibatalkan' => 'background: #fef2f2; color: #991b1b;',
                                        default => 'background: #f1f5f9; color: #475569;'
                                    };
                                @endphp
                                <span style="{{ $badgeColor }} padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">
                                    {{ $trx->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">Belum ada pesanan masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Kolom Samping: Stok Kritis & Log -->
    <div>
        <!-- Peringatan Stok (BKPM Acara 18) -->
        <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 20px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">
                <i class="fa-solid fa-triangle-exclamation" style="color: #f59e0b;"></i> Monitoring Stok Kritis
            </h3>
            @if($stokMenipis->count() > 0)
                <ul style="list-style: none; padding: 0; font-size: 13px;">
                    @foreach($stokMenipis as $stk)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                            <span>{{ $stk->nama }}</span>
                            <span style="background: #fef2f2; color: #991b1b; padding: 2px 8px; border-radius: 4px; font-weight: 700;">
                                Sisa: {{ $stk->stok }} Bal
                            </span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p style="font-size: 13px; color: #059669;"><i class="fa-solid fa-circle-check"></i> Seluruh stok varian rokok dalam kondisi aman.</p>
            @endif
        </div>

        <!-- Audit Trail Ringkas -->
        <div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">
                <i class="fa-solid fa-clock-rotate-left"></i> Log Aktivitas Terakhir
            </h3>
            <ul style="list-style: none; padding: 0; font-size: 12px; color: #475569;">
                @foreach($aktivitasTerbaru as $act)
                    <li style="padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                        <strong>{{ $act->user_nama }}</strong>: {{ $act->action }}
                        <div style="color: #94a3b8; font-size: 11px;">{{ $act->created_at->diffForHumans() }}</div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection
