@extends('layouts.app')

@section('title', 'Formulir Pemesanan Distributor B2B')

@section('content')
<!-- Header Banner -->
<section class="page-header" style="background: radial-gradient(ellipse at top, rgba(197, 160, 89, 0.12) 0%, transparent 60%), linear-gradient(180deg, #0d1012 0%, #15191d 100%); border-bottom: 1px solid rgba(197, 160, 89, 0.2); padding: 60px 0;">
    <div class="container text-center">
        <span class="header-badge">LAYANAN PEMESANAN RESMI PABRIK</span>
        <h1 class="page-title">FORMULIR PEMESANAN ROKOK</h1>
        <p class="page-subtitle">Tersedia Pilihan Pesan Eceran Per Slop (Minimal 1 Slop) & Paket Grosir Bal (B2B Distributor)</p>
    </div>
</section>

<section class="section-py">
    <div class="container" style="max-width: 900px;">
        <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-lg);">
            
            @if($isLocked)
                <!-- Banner Kunci Pemesanan (Order Lock) -->
                <div style="background: rgba(239, 68, 68, 0.12); border: 2px solid #ef4444; border-radius: var(--radius-lg); padding: 26px; margin-bottom: 28px; text-align: center;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: rgba(239, 68, 68, 0.2); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 14px;">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <h3 style="color: #ffffff; font-size: 20px; margin-bottom: 8px;">Pemesanan Baru Terkunci Sementara</h3>
                    <p style="color: #cbd5e1; font-size: 14px; max-width: 620px; margin: 0 auto 18px; line-height: 1.6;">
                        Toko Anda masih memiliki pesanan aktif <strong>{{ $lockedOrder->kode_transaksi }}</strong> ({{ $lockedOrder->ringkasan_item }}) yang sedang berstatus <strong>Sedang Dikirim</strong>.
                        <br><br>
                        Sesuai standar operasional PR. KERETA KENCANA, mohon lakukan <strong>Konfirmasi Penerimaan Barang & Unggah Foto Bukti</strong> pada menu <em>Pesanan Saya</em> saat barang tiba di toko untuk membuka kembali hak pemesanan baru.
                    </p>
                    <a href="{{ route('pesanan.saya') }}" class="btn btn-gold" style="padding: 12px 24px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-box-open"></i> Buka Menu Pesanan Saya & Konfirmasi Bukti
                    </a>
                </div>
            @endif

            <!-- Banner Akun Mitra Aktif -->
            <div style="background: rgba(212,175,55,0.06); border: 1px solid rgba(212,175,55,0.25); border-radius: 8px; padding: 14px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(212,175,55,0.2); color: var(--gold); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        <i class="fa-solid fa-store"></i>
                    </div>
                    <div>
                        <div style="color: #ffffff; font-weight: 700; font-size: 14px;">
                            {{ $user->nama_toko ?: $user->name }}
                        </div>
                        <div style="color: var(--text-muted); font-size: 12px;">
                            Akun Mitra: {{ $user->email }} &bull; {{ $user->telepon ?: 'Belum ada nomor' }}
                        </div>
                    </div>
                </div>
                <a href="{{ route('pesanan.saya') }}" class="btn btn-sm btn-outline-gold" style="font-size: 12px;">
                    <i class="fa-solid fa-list-check"></i> Riwayat & Pelacakan Pesanan
                </a>
            </div>

            <form action="{{ route('pesanan.store') }}" method="POST" id="formOrder">
                @csrf

                <div style="border-bottom: 1px solid var(--charcoal-border); padding-bottom: 18px; margin-bottom: 24px;">
                    <h3 style="color: var(--gold); font-size: 18px; margin-bottom: 6px;">
                        <i class="fa-solid fa-boxes-stacked"></i> 1. Pilihan Varian Produk & Satuan Pemesanan
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px; margin: 0; line-height: 1.5;">
                        Anda dapat memesan <strong>lebih dari 1 jenis produk</strong> dalam satu surat pesanan resmi. 
                        Tentukan pilihan satuan secara mandiri untuk tiap produk: <strong>Slop (10 Bungkus)</strong> atau <strong>Paket Grosir Bal (200 Bungkus)</strong>.
                    </p>
                </div>

                @if($errors->has('items') || $errors->has('order_lock'))
                    <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; border-radius: 8px; padding: 14px 18px; margin-bottom: 20px; color: #fca5a5; font-size: 13.5px;">
                        <i class="fa-solid fa-triangle-exclamation"></i> {{ $errors->first('items') ?: $errors->first('order_lock') }}
                    </div>
                @endif

                <!-- Daftar Multi-Produk PR. Kereta Kencana -->
                <div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 24px;">
                    @foreach($produks as $p)
                        @php
                            $isPreselected = ($preselectedSlug === $p->slug);
                            $oldQty = old('items.'.$p->id.'.jumlah', $isPreselected ? 1 : 0);
                            $oldSatuan = old('items.'.$p->id.'.satuan', 'Slop');
                        @endphp
                        <div class="product-item-card" id="card_prod_{{ $p->id }}" 
                             style="background: #121619; border: 1px solid {{ ($isPreselected || $oldQty > 0) ? 'var(--gold)' : 'var(--charcoal-border)' }}; border-radius: 10px; padding: 18px; transition: all 0.2s ease;">
                            
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px;">
                                <!-- Info Produk -->
                                <div style="flex: 1 1 260px; min-width: 240px;">
                                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                        <h4 style="color: #ffffff; font-size: 16px; margin: 0; font-weight: 700;">
                                            {{ $p->nama }}
                                        </h4>
                                        <span style="background: rgba(212, 175, 55, 0.15); color: var(--gold); border: 1px solid rgba(212, 175, 55, 0.3); font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 600;">
                                            {{ $p->kategori->nama_kategori }}
                                        </span>
                                    </div>
                                    <div style="font-size: 12.5px; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 4px;">
                                        <span><strong style="color: var(--text-light);">Per Slop:</strong> {{ $p->formatted_harga_slop }}</span>
                                        <span>&bull;</span>
                                        <span><strong style="color: var(--text-light);">Per Bal:</strong> {{ $p->formatted_harga }}</span>
                                    </div>
                                    <div style="font-size: 11.5px; color: #10b981;">
                                        <i class="fa-solid fa-circle-check"></i> Stok Pabrik: <strong>{{ $p->stok }} Bal</strong> (Tersedia)
                                    </div>
                                </div>

                                <!-- Kontrol Satuan & Jumlah -->
                                <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap; justify-content: flex-end;">
                                    <!-- Satuan Toggle Pill -->
                                    <div style="background: rgba(255,255,255,0.04); border: 1px solid var(--charcoal-border); border-radius: 8px; padding: 4px; display: inline-flex; gap: 4px;">
                                        <label style="cursor: pointer; margin: 0; padding: 6px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: 0.2s;" 
                                               id="label_satuan_{{ $p->id }}_slop"
                                               class="satuan-toggle-label {{ $oldSatuan === 'Slop' ? 'active-satuan' : '' }}">
                                            <input type="radio" name="items[{{ $p->id }}][satuan]" value="Slop" 
                                                   class="item-satuan" data-id="{{ $p->id }}" 
                                                   {{ $oldSatuan === 'Slop' ? 'checked' : '' }} 
                                                   {{ $isLocked ? 'disabled' : '' }} style="display: none;">
                                            <span>Slop (10 Bks)</span>
                                        </label>
                                        <label style="cursor: pointer; margin: 0; padding: 6px 12px; border-radius: 6px; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: 0.2s;" 
                                               id="label_satuan_{{ $p->id }}_bal"
                                               class="satuan-toggle-label {{ $oldSatuan === 'Bal' ? 'active-satuan' : '' }}">
                                            <input type="radio" name="items[{{ $p->id }}][satuan]" value="Bal" 
                                                   class="item-satuan" data-id="{{ $p->id }}" 
                                                   {{ $oldSatuan === 'Bal' ? 'checked' : '' }} 
                                                   {{ $isLocked ? 'disabled' : '' }} style="display: none;">
                                            <span>Bal (200 Bks)</span>
                                        </label>
                                    </div>

                                    <!-- Stepper Kuantitas -->
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <button type="button" class="btn-qty-minus" data-id="{{ $p->id }}" {{ $isLocked ? 'disabled' : '' }}
                                                style="width: 34px; height: 36px; background: rgba(255,255,255,0.06); border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <input type="number" name="items[{{ $p->id }}][jumlah]" id="input_qty_{{ $p->id }}" 
                                               min="0" max="{{ $p->stok * ($p->slop_per_bal ?: 20) }}" value="{{ $oldQty }}" 
                                               class="item-qty-input" data-id="{{ $p->id }}"
                                               data-harga-slop="{{ $p->harga_per_slop }}"
                                               data-harga-bal="{{ $p->harga_per_bal }}"
                                               data-slop-per-bal="{{ $p->slop_per_bal ?: 20 }}"
                                               data-bungkus-per-slop="{{ $p->bungkus_per_slop ?: 10 }}"
                                               data-nama="{{ $p->nama }}"
                                               {{ $isLocked ? 'disabled' : '' }}
                                               style="width: 65px; height: 36px; text-align: center; background: #0c0f12; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px; font-size: 15px; font-weight: 700;">
                                        <button type="button" class="btn-qty-plus" data-id="{{ $p->id }}" {{ $isLocked ? 'disabled' : '' }}
                                                style="width: 34px; height: 36px; background: rgba(255,255,255,0.06); border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px; font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>

                                    <!-- Subtotal Baris -->
                                    <div style="min-width: 130px; text-align: right;">
                                        <span style="font-size: 11px; color: var(--text-muted); display: block;">Subtotal:</span>
                                        <strong id="display_subtotal_{{ $p->id }}" style="font-size: 14.5px; color: var(--gold); display: block;">
                                            Rp 0
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Estimasi Total Kalkulasi Realtime Multi-Item -->
                <div style="background: rgba(212, 175, 55, 0.06); border: 1px dashed var(--gold); border-radius: 10px; padding: 22px; margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                        <div>
                            <span style="font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;">
                                Ringkasan Seluruh Pesanan:
                            </span>
                            <div id="summaryItemsList" style="font-size: 13.5px; color: #ffffff; font-weight: 600; line-height: 1.5;">
                                Belum ada produk yang dipilih (Kuantitas 0)
                            </div>
                            <div style="font-size: 12px; color: var(--text-muted); margin-top: 4px;" id="summaryTotalBungkus">
                                0 Bungkus
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <span style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 2px;">TOTAL PEMESANAN:</span>
                            <strong id="calcGrandTotal" style="font-size: 26px; color: var(--gold); font-weight: 800;">Rp 0</strong>
                        </div>
                    </div>
                </div>

                <div style="border-bottom: 1px solid var(--charcoal-border); padding-bottom: 20px; margin-bottom: 24px;">
                    <h3 style="color: var(--gold); font-size: 18px; margin-bottom: 6px;">
                        <i class="fa-solid fa-address-card"></i> 2. Identitas Pemesan / Toko Mitra
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px;">Data resmi mitra untuk surat jalan, nota fisik, & konfirmasi pengiriman armada (terisi otomatis dari akun Anda).</p>
                </div>

                <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Nama Toko / Warung / Mitra Distributor <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="nama_mitra" class="form-control" required value="{{ old('nama_mitra', $user->nama_toko ?: $user->name) }}" placeholder="Contoh: Toko Berkah Mandiri / Kios Barokah" {{ $isLocked ? 'disabled' : '' }}
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>

                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Nomor WhatsApp Aktif <span style="color: var(--danger);">*</span></label>
                        <input type="tel" name="telepon" class="form-control" required value="{{ old('telepon', $user->telepon) }}" placeholder="08xxxxxxxxxx" {{ $isLocked ? 'disabled' : '' }}
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Alamat Lengkap Tujuan Pengiriman <span style="color: var(--danger);">*</span></label>
                    <textarea name="alamat" class="form-control" rows="3" required placeholder="Alamat pengiriman toko/rumah, nama jalan, RT/RW, desa/kelurahan, kecamatan, kabupaten/kota..." {{ $isLocked ? 'disabled' : '' }}
                              style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">{{ old('alamat', $user->alamat) }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Catatan Khusus Pengiriman (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Kirim via kargo langganan, titip bus/travel, atau ambil sendiri di gudang pabrik Ponggok..." {{ $isLocked ? 'disabled' : '' }}
                              style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">{{ old('catatan') }}</textarea>
                </div>

                @if($isLocked)
                    <button type="button" class="btn btn-secondary" disabled style="width: 100%; padding: 16px; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px; cursor: not-allowed; opacity: 0.65;">
                        <i class="fa-solid fa-lock"></i> Pemesanan Terkunci (Selesaikan Pesanan Sebelumnya Terlebih Dahulu)
                    </button>
                @else
                    <button type="submit" class="btn btn-gold" id="btnSubmitOrder" style="width: 100%; padding: 16px; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fa-solid fa-file-invoice"></i> Terbitkan Surat Pesanan & Faktur Resmi
                    </button>
                @endif
            </form>
        </div>
    </div>
</section>

@push('styles')
<style>
.active-satuan {
    background: var(--gold) !important;
    color: #0c0f12 !important;
}
.satuan-toggle-label:not(.active-satuan) {
    color: var(--text-muted);
}
.satuan-toggle-label:not(.active-satuan):hover {
    color: #ffffff;
    background: rgba(255,255,255,0.06);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInputs = document.querySelectorAll('.item-qty-input');
    const satuanRadios = document.querySelectorAll('.item-satuan');
    const formOrder = document.getElementById('formOrder');
    const calcGrandTotal = document.getElementById('calcGrandTotal');
    const summaryItemsList = document.getElementById('summaryItemsList');
    const summaryTotalBungkus = document.getElementById('summaryTotalBungkus');

    function hitungSemua() {
        let grandTotal = 0;
        let totalBungkus = 0;
        let activeSummaryItems = [];

        qtyInputs.forEach(input => {
            const id = input.dataset.id;
            const card = document.getElementById('card_prod_' + id);
            const hargaSlop = parseFloat(input.dataset.hargaSlop || 0);
            const hargaBal = parseFloat(input.dataset.hargaBal || 0);
            const slopPerBal = parseInt(input.dataset.slopPerBal || 20);
            const bungkusPerSlop = parseInt(input.dataset.bungkusPerSlop || 10);
            const nama = input.dataset.nama || 'Produk';

            let qty = parseInt(input.value || 0);
            if (qty < 0 || isNaN(qty)) {
                qty = 0;
                input.value = 0;
            }

            // Dapatkan satuan terpilih untuk produk ini
            const radioSatuan = document.querySelector(`input[name="items[${id}][satuan]"]:checked`);
            const satuan = radioSatuan ? radioSatuan.value : 'Slop';

            // Update style tombol toggle satuan
            const labelSlop = document.getElementById(`label_satuan_${id}_slop`);
            const labelBal = document.getElementById(`label_satuan_${id}_bal`);
            if (satuan === 'Slop') {
                if (labelSlop) labelSlop.classList.add('active-satuan');
                if (labelBal) labelBal.classList.remove('active-satuan');
            } else {
                if (labelBal) labelBal.classList.add('active-satuan');
                if (labelSlop) labelSlop.classList.remove('active-satuan');
            }

            // Hitung subtotal produk ini
            const hargaSatuan = (satuan === 'Slop') ? hargaSlop : hargaBal;
            const subtotal = qty * hargaSatuan;

            const displaySubtotal = document.getElementById('display_subtotal_' + id);
            if (displaySubtotal) {
                displaySubtotal.innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            }

            // Highlight kartu jika qty > 0
            if (card) {
                if (qty > 0) {
                    card.style.borderColor = 'var(--gold)';
                    card.style.background = 'rgba(212, 175, 55, 0.05)';
                } else {
                    card.style.borderColor = 'var(--charcoal-border)';
                    card.style.background = '#121619';
                }
            }

            if (qty > 0) {
                grandTotal += subtotal;
                const bksItem = (satuan === 'Slop') ? (qty * bungkusPerSlop) : (qty * slopPerBal * bungkusPerSlop);
                totalBungkus += bksItem;
                activeSummaryItems.push(`${qty} ${satuan} ${nama}`);
            }
        });

        // Update Grand Total UI
        if (calcGrandTotal) {
            calcGrandTotal.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
        }

        // Update Summary Bar
        if (summaryItemsList) {
            if (activeSummaryItems.length > 0) {
                summaryItemsList.innerHTML = activeSummaryItems.map(item => `<span style="display: inline-block; background: rgba(255,255,255,0.08); padding: 3px 8px; border-radius: 4px; margin-right: 6px; margin-bottom: 4px;">• ${item}</span>`).join(' ');
            } else {
                summaryItemsList.innerHTML = '<span style="color: var(--text-muted); font-weight: normal;">Belum ada varian rokok yang dipilih (Kuantitas 0). Silakan masukkan kuantitas di atas.</span>';
            }
        }

        if (summaryTotalBungkus) {
            summaryTotalBungkus.innerText = `Total Volume: ${totalBungkus.toLocaleString('id-ID')} Bungkus Kretek SKT`;
        }
    }

    // Event listener kuantitas stepper
    document.querySelectorAll('.btn-qty-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.getElementById('input_qty_' + id);
            if (input) {
                input.value = parseInt(input.value || 0) + 1;
                hitungSemua();
            }
        });
    });

    document.querySelectorAll('.btn-qty-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.getElementById('input_qty_' + id);
            if (input) {
                let current = parseInt(input.value || 0);
                if (current > 0) {
                    input.value = current - 1;
                    hitungSemua();
                }
            }
        });
    });

    qtyInputs.forEach(input => {
        input.addEventListener('input', hitungSemua);
        input.addEventListener('change', hitungSemua);
    });

    satuanRadios.forEach(radio => {
        radio.addEventListener('change', hitungSemua);
    });

    // Validasi form saat disubmit
    if (formOrder) {
        formOrder.addEventListener('submit', function(e) {
            let totalQty = 0;
            qtyInputs.forEach(input => {
                totalQty += parseInt(input.value || 0);
            });

            if (totalQty <= 0) {
                e.preventDefault();
                alert('Silakan tentukan minimal 1 varian rokok dengan kuantitas lebih dari 0 sebelum menerbitkan pesanan.');
            }
        });
    }

    // Jalankan kalkulasi awal saat halaman dimuat
    hitungSemua();
});
</script>
@endpush
@endsection
