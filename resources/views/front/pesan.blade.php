@extends('layouts.app')

@section('title', 'Formulir Pemesanan Distributor B2B')

@section('content')
<!-- Header Banner -->
<section class="page-header">
    <div class="container">
        <span class="header-badge">Layanan Pemesanan Resmi Pabrik</span>
        <h1 class="page-title">Formulir Pemesanan Rokok</h1>
        <p class="page-subtitle" style="margin: 0;">Pemesanan Online Khusus Satuan Slop (Total Maks. 19 Slop Gabungan Seluruh Varian). Skala Bal Hubungi Admin.</p>
    </div>
</section>

<section class="section-py">
    <div class="container" style="max-width: 900px;">
        <div class="order-shell scroll-reveal reveal-zoom">
            
            @if($isLocked)
                <!-- Banner Kunci Pemesanan (Order Lock) -->
                <div style="background: rgba(185, 28, 28, 0.06); border: 1px solid var(--danger); border-radius: 2px; padding: 24px; margin-bottom: 24px;">
                    <h3 style="color: var(--danger); font-family: var(--font-serif); font-size: 20px; margin-bottom: 8px; font-weight: 600;">Pemesanan Baru Terkunci Sementara</h3>
                    <p style="color: var(--text-dark); font-size: 14.5px; margin: 0 0 16px; line-height: 1.6;">
                        Toko Anda masih memiliki pesanan aktif <strong class="font-mono">{{ $lockedOrder->kode_transaksi }}</strong> ({{ $lockedOrder->ringkasan_item }}) yang sedang berstatus <strong>Sedang Dikirim</strong>.
                        Mohon lakukan <strong>Konfirmasi Penerimaan Barang &amp; Unggah Foto Bukti</strong> pada menu <em>Pesanan Saya</em> saat barang tiba di toko untuk membuka kembali hak pemesanan baru.
                    </p>
                    <a href="{{ route('pesanan.saya') }}" class="btn btn-gold" style="padding: 10px 20px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-box-open"></i> Buka Menu Pesanan Saya &amp; Konfirmasi Bukti
                    </a>
                </div>
            @endif

            <!-- Banner Akun Mitra Aktif -->
            <div style="background: var(--kk-ivory); border: 1px solid var(--kk-ivory-border); border-radius: 2px; padding: 14px 18px; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div>
                    <div style="color: var(--text-dark); font-weight: 600; font-size: 15px;">
                        {{ $user->nama_toko ?: $user->name }}
                    </div>
                    <div class="font-mono" style="color: var(--text-muted); font-size: 12px;">
                        {{ $user->email }} &bull; {{ $user->telepon ?: 'Belum ada nomor' }}
                    </div>
                </div>
                <a href="{{ route('pesanan.saya') }}" class="btn btn-sm btn-outline-gold" style="font-size: 12.5px;">
                    Riwayat &amp; Pelacakan Pesanan
                </a>
            </div>

            <!-- Skema Tarif Slop & Batas Maksimal -->
            <div class="order-tier-box">
                <div class="order-tier-grid">
                    <div class="order-tier-cell">
                        <div class="order-tier-range">1&ndash;5 Slop</div>
                        <div class="order-tier-price">Rp 70.000 <span style="font-size: 12px; font-weight: 400; color: var(--text-muted);">/ Slop</span></div>
                    </div>
                    <div class="order-tier-cell">
                        <div class="order-tier-range">6&ndash;10 Slop</div>
                        <div class="order-tier-price">Rp 65.000 <span style="font-size: 12px; font-weight: 400; color: var(--text-muted);">/ Slop</span></div>
                    </div>
                    <div class="order-tier-cell">
                        <div class="order-tier-range">11&ndash;19 Slop</div>
                        <div class="order-tier-price">Rp 60.000 <span style="font-size: 12px; font-weight: 400; color: var(--text-muted);">/ Slop</span></div>
                    </div>
                </div>
                <div class="order-tier-foot">
                    <span>Tarif sama untuk semua varian, dihitung dari total gabungan pesanan (maks. <strong>19 Slop</strong>). Skala Bal via Admin.</span>
                    <a href="https://wa.me/{{ $pabrikWa ?? config('pabrik.wa_pabrik', '6285790627104') }}?text={{ urlencode('Halo Admin PR. KERETA KENCANA, saya ingin berkonsultasi mengenai pembelian rokok skala Bal.') }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-gold" style="font-size: 12px; white-space: nowrap;">
                        <i class="fa-brands fa-whatsapp"></i> Tanya Skala Bal
                    </a>
                </div>
            </div>

            <form action="{{ route('pesanan.store') }}" method="POST" id="formOrder">
                @csrf

                <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 14px; margin-bottom: 18px;">
                    <h3 style="color: var(--text-dark); font-family: var(--font-serif); font-size: 19px; margin-bottom: 4px; font-weight: 600;">
                        1. Pilihan Varian Produk (Satuan Slop)
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px; margin: 0; line-height: 1.5;">
                        Isi jumlah <strong>Slop (10 Bungkus)</strong> pada varian yang ingin dipesan. Produk dengan stok habis otomatis dinonaktifkan.
                    </p>
                </div>

                @if($errors->any())
                    <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 2px; padding: 14px 18px; margin-bottom: 20px; color: #b91c1c; font-size: 13.5px;">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first() }}
                    </div>
                @endif

                <!-- Daftar Multi-Produk PR. Kereta Kencana (Khusus Slop) -->
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
                    @foreach($produks as $p)
                        @php
                            $maxTotalSlop = 19;
                            $slopPerBal = $p->slop_per_bal ?: 20;
                            $stokSlop = max(0, (int) $p->stok);
                            $stokBalUtuh = $p->stok_bal_utuh;
                            $stokSisaSlop = $p->stok_sisa_slop;
                            $isOutOfStock = !$p->isTersediaUntukOrder();
                            $maxItemSlop = $isOutOfStock ? 0 : min($maxTotalSlop, $stokSlop);
                            $minItemSlop = max(1, (int) ($p->min_order_slop ?: 1));
                            $isPreselected = !$isOutOfStock && ($preselectedSlug === $p->slug);
                            $oldQty = $isOutOfStock ? 0 : min($maxItemSlop, max(0, (int) old('items.'.$p->id.'.jumlah', $isPreselected ? min($minItemSlop, $maxItemSlop) : 0)));
                        @endphp
                        <div class="order-product-card {{ $isOutOfStock ? 'is-empty' : (($isPreselected || $oldQty > 0) ? 'is-selected' : '') }}" id="card_prod_{{ $p->id }}">
                            <div class="order-product-row">
                                <!-- Info Produk & Thumbnail Foto -->
                                <div class="order-product-info">
                                    <img src="{{ $p->image_url }}" alt="{{ $p->nama }}" class="order-product-thumb">
                                    <div style="min-width: 0;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap;">
                                            <h4 style="color: var(--text-dark); font-size: 16px; margin: 0; font-weight: 700;">
                                                {{ $p->nama }}
                                            </h4>
                                            <span style="background: var(--kk-ivory); color: var(--kk-bronze); border: 1px solid var(--kk-ivory-border); font-size: 11px; padding: 2px 8px; border-radius: 2px; font-weight: 600;">
                                                {{ $p->kategori->nama_kategori }}
                                            </span>
                                            @if($p->best_seller)
                                                <span class="bestseller-tag-inline">
                                                    Paling Diminati
                                                </span>
                                            @endif
                                        </div>

                                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 6px;">
                                            <span class="order-stock-pill {{ $isOutOfStock ? 'is-empty' : '' }}">
                                                @if($isOutOfStock)
                                                    Stok habis
                                                @else
                                                    Stok tersedia: <strong style="color: var(--text-dark);">{{ number_format($stokSlop, 0, ',', '.') }} Slop</strong> @if($stokBalUtuh > 0)({{ $stokBalUtuh }} Bal{{ $stokSisaSlop > 0 ? ' ' . $stokSisaSlop . ' Slop' : '' }})@endif
                                                @endif
                                            </span>
                                            <span style="font-size: 12px; color: var(--text-muted);">
                                                {{ $p->batang_per_bungkus }} Btg/Bks &bull; {{ $p->bungkus_per_slop ?: 10 }} Bks/Slop @if($minItemSlop > 1)&bull; Min. {{ $minItemSlop }} Slop @endif
                                            </span>
                                        </div>

                                        <div style="font-size: 12.5px; color: var(--text-medium);">
                                            Tarif satuan: <strong class="font-mono" style="color: var(--text-dark);">Rp 70.000</strong> / Slop <span style="color: var(--text-muted);">(harga turun otomatis di 6 &amp; 11 Slop)</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Kontrol Jumlah Slop & Subtotal -->
                                <div class="order-product-controls">
                                    <input type="hidden" name="items[{{ $p->id }}][satuan]" value="Slop">

                                    <div class="order-qty-group">
                                        <label for="input_qty_{{ $p->id }}" class="order-control-label">Jumlah (Slop)</label>
                                        <div style="display: flex; align-items: center; gap: 6px;">
                                            <button type="button" class="btn-qty-minus" data-id="{{ $p->id }}" {{ ($isLocked || $isOutOfStock) ? 'disabled' : '' }}
                                                    aria-label="Kurangi {{ $p->nama }}"
                                                    style="width: 36px; height: 38px; background: var(--bg-surface-subtle); border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-minus"></i>
                                            </button>
                                            <input type="number" name="items[{{ $p->id }}][jumlah]" id="input_qty_{{ $p->id }}" 
                                                    min="0" max="{{ $maxItemSlop }}" value="{{ $oldQty }}" 
                                                    class="item-qty-input font-mono" data-id="{{ $p->id }}"
                                                    data-harga-slop="70000"
                                                    data-max-slop="{{ $maxItemSlop }}"
                                                    data-min-slop="{{ $minItemSlop }}"
                                                    data-bungkus-per-slop="{{ $p->bungkus_per_slop ?: 10 }}"
                                                    data-nama="{{ $p->nama }}"
                                                    {{ ($isLocked || $isOutOfStock) ? 'disabled' : '' }}
                                                    style="width: 64px; height: 38px; text-align: center; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px; font-size: 15px; font-weight: 600;">
                                            <button type="button" class="btn-qty-plus" data-id="{{ $p->id }}" {{ ($isLocked || $isOutOfStock) ? 'disabled' : '' }}
                                                    aria-label="Tambah {{ $p->nama }}"
                                                    style="width: 36px; height: 38px; background: var(--bg-surface-subtle); border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="order-subtotal-col">
                                        <span class="order-control-label">Subtotal</span>
                                        <strong id="display_subtotal_{{ $p->id }}" class="font-mono" style="font-size: 15px; color: var(--text-dark); display: block; line-height: 38px;">
                                            Rp 0
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Peringatan Global Batas Total 19 Slop -->
                <div id="globalMaxNotice" style="display: none; margin-bottom: 20px; font-size: 13px; color: #92400e; background: #fef3c7; border: 1px solid #f59e0b; padding: 10px 14px; border-radius: 2px; line-height: 1.5;">
                    <i class="fa-solid fa-circle-info"></i> Total gabungan pemesanan online maksimal <strong>19 Slop</strong> per transaksi. Untuk pembelian skala Bal (20 Slop ke atas), silakan hubungi Admin via WhatsApp.
                </div>

                <!-- Ringkasan Pesanan -->
                <div class="order-summary-box">
                    <div style="flex: 1 1 280px; min-width: 0;">
                        <span style="font-size: 12px; color: var(--text-muted); letter-spacing: 0.04em; display: block; margin-bottom: 6px;">
                            Ringkasan Pesanan (<span id="summaryTotalSlopCount" class="font-mono" style="color: var(--text-dark); font-weight: 600;">0/19 Slop</span>)
                        </span>
                        <div id="summaryItemsList" style="font-size: 14px; color: var(--text-dark); font-weight: 600; line-height: 1.5;">
                            Belum ada produk yang dipilih (Kuantitas 0)
                        </div>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px 18px; margin-top: 8px; font-size: 12px;">
                            <span style="color: var(--text-muted);" id="summaryTotalBungkus">0 Bungkus</span>
                            <span class="font-mono" style="color: var(--kk-bronze); font-weight: 500;" id="summaryTierRate">Tarif aktif: Rp 70.000 / Slop (1&ndash;5 Slop)</span>
                        </div>
                    </div>
                    <div class="order-summary-total">
                        <span style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 2px;">TOTAL PEMBAYARAN</span>
                        <strong id="calcGrandTotal" class="font-mono" style="font-size: 24px; color: var(--text-dark); font-weight: 600;">Rp 0</strong>
                    </div>
                </div>

                <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 20px; margin-bottom: 24px;">
                    <h3 style="color: var(--text-dark); font-family: var(--font-serif); font-size: 19px; margin-bottom: 6px; font-weight: 600;">
                        2. Identitas Pemesan / Toko Mitra
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px;">Data resmi mitra untuk surat jalan, nota fisik, &amp; konfirmasi pengantaran kurir pabrik (terisi otomatis dari akun Anda).</p>
                </div>

                <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-dark); font-weight: 600; font-size: 14px; margin-bottom: 6px;">Nama Toko / Warung / Mitra Distributor <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="nama_mitra" class="form-control" required value="{{ old('nama_mitra', $user->nama_toko ?: $user->name) }}" placeholder="Contoh: Toko Berkah Mandiri / Kios Barokah" {{ $isLocked ? 'disabled' : '' }}
                               style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px;">
                    </div>

                    <div class="form-group">
                        <label style="display: block; color: var(--text-dark); font-weight: 600; font-size: 14px; margin-bottom: 6px;">Nomor WhatsApp Aktif <span style="color: var(--danger);">*</span></label>
                        <input type="tel" name="telepon" class="form-control" required value="{{ old('telepon', $user->telepon) }}" placeholder="08xxxxxxxxxx" {{ $isLocked ? 'disabled' : '' }}
                               style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-dark); font-weight: 600; font-size: 14px; margin-bottom: 6px;">Alamat Lengkap Tujuan Pengiriman <span style="color: var(--danger);">*</span></label>
                    <textarea name="alamat" class="form-control" rows="3" required placeholder="Alamat pengiriman toko/rumah, nama jalan, RT/RW, desa/kelurahan, kecamatan, kabupaten/kota..." {{ $isLocked ? 'disabled' : '' }}
                              style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px;">{{ old('alamat', $user->alamat) }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-dark); font-weight: 600; font-size: 14px; margin-bottom: 6px;">Catatan Khusus Pengiriman (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Kirim via kargo langganan, titip bus/travel, atau ambil sendiri di gudang pabrik Ponggok..." {{ $isLocked ? 'disabled' : '' }}
                              style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 2px;">{{ old('catatan') }}</textarea>
                </div>

                <div style="background: #f8fafc; border: 1px solid var(--border-light); border-left: 3px solid var(--kk-bronze); padding: 12px 16px; margin-bottom: 26px; font-size: 13px; color: var(--text-dark); line-height: 1.6;">
                    <strong><i class="fa-solid fa-truck-fast" style="color: var(--kk-bronze);"></i> Ketentuan Ongkos Kirim:</strong>
                    Pengiriman <strong>area Blitar gratis</strong> (diantar kurir resmi pabrik). Untuk lokasi jarak jauh / luar kota atau pengambilan mandiri di gudang Ponggok, silakan konfirmasi ongkir dan pengiriman dengan Admin via WhatsApp (<span class="font-mono">{{ $pabrikWaDisplay ?? '+62 857-9062-7104' }}</span>).
                </div>

                <!-- 3. Pilihan Metode Pembayaran -->
                <div style="border-bottom: 1px solid var(--border-light); padding-bottom: 16px; margin-bottom: 18px;">
                    <h3 style="color: var(--text-dark); font-family: var(--font-serif); font-size: 19px; margin-bottom: 6px; font-weight: 600;">
                        3. Metode Pembayaran
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px; margin: 0;">Pilih pembayaran langsung di tempat saat barang tiba (COD) atau transfer rekening resmi pabrik.</p>
                </div>

                @php
                    $selectedPayment = old('metode_pembayaran', 'Bayar di Tempat (COD)');
                @endphp
                <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <label class="order-payment-option {{ stripos($selectedPayment, 'transfer') === false ? 'is-selected' : '' }}">
                        <input type="radio" name="metode_pembayaran" value="Bayar di Tempat (COD)" {{ stripos($selectedPayment, 'transfer') === false ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }} class="payment-radio" style="margin-top: 4px;">
                        <div>
                            <div style="font-weight: 700; color: var(--text-dark); font-size: 14px;">Bayar di Tempat (COD)</div>
                            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5; margin-top: 2px;">Pembayaran tunai kepada kurir resmi pabrik saat pesanan tiba di toko.</div>
                        </div>
                    </label>

                    <label class="order-payment-option {{ stripos($selectedPayment, 'transfer') !== false ? 'is-selected' : '' }}">
                        <input type="radio" name="metode_pembayaran" value="Transfer Bank" {{ stripos($selectedPayment, 'transfer') !== false ? 'checked' : '' }} {{ $isLocked ? 'disabled' : '' }} class="payment-radio" style="margin-top: 4px;">
                        <div>
                            <div style="font-weight: 700; color: var(--text-dark); font-size: 14px;">Transfer Bank</div>
                            <div style="font-size: 12px; color: var(--text-muted); line-height: 1.5; margin-top: 2px;">Transfer ke rekening resmi pabrik, lalu unggah foto bukti di menu Pesanan Saya.</div>
                        </div>
                    </label>
                </div>

                <div id="transferBankBox" style="display: {{ stripos($selectedPayment, 'transfer') !== false ? 'block' : 'none' }}; background: var(--kk-ivory); border: 1px solid var(--kk-ivory-border); border-left: 3px solid var(--text-dark); padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: var(--text-dark); line-height: 1.6;">
                    @if(!empty($bankInfo['rekening']))
                        <strong>Rekening Tujuan {{ $bankInfo['nama'] ?? 'Bank Resmi Pabrik' }}:</strong><br>
                        No. Rekening: <span class="font-mono" style="font-weight: 700; font-size: 14px;">{{ $bankInfo['rekening'] }}</span> &bull; a.n. <strong>{{ $bankInfo['atas_nama'] ?? 'PR. KERETA KENCANA' }}</strong><br>
                    @else
                        <strong>Informasi Rekening Transfer Bank:</strong><br>
                        Nomor rekening bank resmi pabrik akan diinformasikan langsung oleh Admin via WhatsApp (<span class="font-mono">{{ $pabrikWaDisplay ?? '+62 857-9062-7104' }}</span>) setelah Anda menerbitkan faktur.<br>
                    @endif
                    <span style="color: var(--text-muted); font-size: 12px;">Setelah transfer, Anda dapat langsung mengunggah bukti transfer melalui halaman <em>Pesanan Saya</em>.</span>
                </div>

                @if($isLocked)
                    <button type="button" class="btn btn-secondary" disabled style="width: 100%; padding: 16px; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: not-allowed; opacity: 0.65;">
                        <i class="fa-solid fa-lock"></i> Pemesanan Terkunci (Selesaikan Pesanan Sebelumnya Terlebih Dahulu)
                    </button>
                @else
                    <button type="submit" class="btn btn-gold" id="btnSubmitOrder" style="width: 100%; padding: 16px; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fa-solid fa-file-invoice"></i> Terbitkan Surat Pesanan &amp; Faktur Resmi
                    </button>
                @endif
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const MAX_TOTAL_SLOP = 19;
    const qtyInputs = document.querySelectorAll('.item-qty-input');
    const formOrder = document.getElementById('formOrder');
    const calcGrandTotal = document.getElementById('calcGrandTotal');
    const summaryItemsList = document.getElementById('summaryItemsList');
    const summaryTotalBungkus = document.getElementById('summaryTotalBungkus');
    const summaryTotalSlopCount = document.getElementById('summaryTotalSlopCount');
    const globalMaxNotice = document.getElementById('globalMaxNotice');
    const paymentRadios = document.querySelectorAll('.payment-radio');
    const transferBankBox = document.getElementById('transferBankBox');

    function syncPaymentBox() {
        const selected = document.querySelector('.payment-radio:checked');
        document.querySelectorAll('.order-payment-option').forEach(opt => {
            const radio = opt.querySelector('.payment-radio');
            opt.classList.toggle('is-selected', Boolean(radio && radio.checked));
        });
        if (transferBankBox && selected) {
            transferBankBox.style.display = selected.value.toLowerCase().includes('transfer') ? 'block' : 'none';
        }
    }

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', syncPaymentBox);
    });
    syncPaymentBox();

    function getTotalSlop(excludeInput = null) {
        let sum = 0;
        qtyInputs.forEach(input => {
            if (input !== excludeInput) {
                sum += Math.max(0, parseInt(input.value || 0) || 0);
            }
        });
        return sum;
    }

    function getMaxItemSlop(input) {
        const parsed = parseInt(input.dataset.maxSlop ?? MAX_TOTAL_SLOP, 10);
        if (isNaN(parsed)) return MAX_TOTAL_SLOP;
        return Math.max(0, Math.min(MAX_TOTAL_SLOP, parsed));
    }

    function hitungSemua(changedInput = null) {
        if (changedInput) {
            let val = parseInt(changedInput.value || 0);
            if (isNaN(val) || val < 0) val = 0;
            const maxItem = changedInput.disabled ? 0 : getMaxItemSlop(changedInput);
            const otherTotal = getTotalSlop(changedInput);
            const remainingAllowed = Math.max(0, MAX_TOTAL_SLOP - otherTotal);
            const allowedCap = Math.min(maxItem, remainingAllowed);
            if (val > allowedCap) {
                val = allowedCap;
            }
            changedInput.value = val;
        }

        let totalSlop = 0;
        qtyInputs.forEach(input => {
            const maxItem = input.disabled ? 0 : getMaxItemSlop(input);
            let qty = parseInt(input.value || 0);
            if (isNaN(qty) || qty < 0) {
                qty = 0;
            }
            const allowedForRow = Math.min(maxItem, Math.max(0, MAX_TOTAL_SLOP - totalSlop));
            if (qty > allowedForRow) {
                qty = allowedForRow;
            }
            input.value = qty;
            totalSlop += qty;
        });

        if (globalMaxNotice) {
            globalMaxNotice.style.display = (totalSlop >= MAX_TOTAL_SLOP) ? 'block' : 'none';
        }

        if (summaryTotalSlopCount) {
            summaryTotalSlopCount.innerText = `${totalSlop}/${MAX_TOTAL_SLOP} Slop`;
        }

        let activeHargaSlop = 70000;
        let tierLabel = '1–5 Slop';
        if (totalSlop > 10) {
            activeHargaSlop = 60000;
            tierLabel = '11–19 Slop';
        } else if (totalSlop >= 6) {
            activeHargaSlop = 65000;
            tierLabel = '6–10 Slop';
        }

        const summaryTierRate = document.getElementById('summaryTierRate');
        if (summaryTierRate) {
            summaryTierRate.innerText = `Tarif aktif: Rp ${activeHargaSlop.toLocaleString('id-ID')} / Slop (${tierLabel})`;
        }

        let grandTotal = 0;
        let totalBungkus = 0;
        let activeSummaryItems = [];

        qtyInputs.forEach(input => {
            const id = input.dataset.id;
            const card = document.getElementById('card_prod_' + id);
            const bungkusPerSlop = parseInt(input.dataset.bungkusPerSlop || 10);
            const nama = input.dataset.nama || 'Produk';
            const qty = parseInt(input.value || 0);

            const subtotal = qty * activeHargaSlop;
            const displaySubtotal = document.getElementById('display_subtotal_' + id);
            if (displaySubtotal) {
                displaySubtotal.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            }

            if (card) {
                card.classList.toggle('is-selected', qty > 0 && !card.classList.contains('is-empty'));
            }

            if (qty > 0) {
                grandTotal += subtotal;
                totalBungkus += qty * bungkusPerSlop;
                activeSummaryItems.push(`${qty} Slop ${nama}`);
            }
        });

        if (calcGrandTotal) {
            calcGrandTotal.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }

        if (summaryItemsList) {
            if (activeSummaryItems.length > 0) {
                summaryItemsList.innerHTML = activeSummaryItems.map(item => `<span style="display: inline-block; background: #e4e4e7; color: #18181b; padding: 3px 8px; border-radius: 2px; margin-right: 6px; margin-bottom: 4px;">&bull; ${item}</span>`).join(' ');
            } else {
                summaryItemsList.innerHTML = '<span style="color: var(--text-muted); font-weight: normal;">Belum ada varian rokok yang dipilih (Kuantitas 0). Silakan masukkan jumlah Slop di atas.</span>';
            }
        }

        if (summaryTotalBungkus) {
            summaryTotalBungkus.innerText = `Total Volume: ${totalBungkus.toLocaleString('id-ID')} Bungkus Kretek SKT`;
        }
    }

    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.getElementById('input_qty_' + id);
            if (input && !input.disabled) {
                const otherTotal = getTotalSlop(input);
                const current = parseInt(input.value || 0) || 0;
                const maxItem = getMaxItemSlop(input);
                const minItem = Math.max(1, parseInt(input.dataset.minSlop || 1, 10) || 1);
                const stepTarget = current === 0 ? minItem : current + 1;
                if (stepTarget > maxItem) {
                    return;
                }
                if (otherTotal + stepTarget > MAX_TOTAL_SLOP) {
                    if (globalMaxNotice) globalMaxNotice.style.display = 'block';
                    return;
                }
                input.value = stepTarget;
                hitungSemua(input);
            }
        });
    });

    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.getElementById('input_qty_' + id);
            if (input && !input.disabled) {
                const current = parseInt(input.value || 0) || 0;
                const minItem = Math.max(1, parseInt(input.dataset.minSlop || 1, 10) || 1);
                if (current > 0) {
                    input.value = current <= minItem ? 0 : current - 1;
                    hitungSemua(input);
                }
            }
        });
    });

    qtyInputs.forEach(input => {
        input.addEventListener('input', () => hitungSemua(input));
        input.addEventListener('change', () => hitungSemua(input));
    });

    if (formOrder) {
        formOrder.addEventListener('submit', function(e) {
            const totalQty = getTotalSlop();
            if (totalQty <= 0) {
                e.preventDefault();
                alert('Silakan tentukan minimal 1 varian rokok dengan total 1 sampai 19 Slop sebelum menerbitkan pesanan.');
                return;
            }
            if (totalQty > MAX_TOTAL_SLOP) {
                e.preventDefault();
                alert('Total pemesanan online maksimal 19 Slop gabungan seluruh varian. Untuk pembelian skala Bal, silakan hubungi Admin.');
            }
        });
    }

    hitungSemua();
});
</script>
@endpush
@endsection
