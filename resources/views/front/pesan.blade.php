@extends('layouts.app')

@section('title', 'Formulir Pemesanan Distributor B2B')

@section('content')
<!-- Header Banner -->
<section class="page-header" style="background: linear-gradient(rgba(13, 16, 18, 0.85), rgba(13, 16, 18, 0.95)), url('{{ asset('assets/img/hero-pabrik.jpg') }}') center/cover no-repeat; padding: 60px 0;">
    <div class="container text-center">
        <span class="header-badge">LAYANAN PEMESANAN RESMI PABRIK</span>
        <h1 class="page-title">FORMULIR PEMESANAN ROKOK</h1>
        <p class="page-subtitle">Tersedia Pilihan Pesan Eceran Per Slop (Minimal 1 Slop) & Paket Grosir Bal (B2B Distributor)</p>
    </div>
</section>

<section class="section-py">
    <div class="container" style="max-width: 900px;">
        <div style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-lg);">
            
            <form action="{{ route('pesanan.store') }}" method="POST" id="formOrder">
                @csrf

                <div style="border-bottom: 1px solid var(--charcoal-border); padding-bottom: 20px; margin-bottom: 24px;">
                    <h3 style="color: var(--gold); font-size: 18px; margin-bottom: 6px;">
                        <i class="fa-solid fa-boxes-stacked"></i> 1. Pilihan Varian & Satuan Pemesanan
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px;">Pabrik melayani pembelian mulai dari <strong>minimal 1 Slop</strong> untuk mendukung kemitraan toko, warung, hingga distributor bal partai besar.</p>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Pilih Varian Rokok <span style="color: var(--danger);">*</span></label>
                    <select name="barang_id" id="selectBarang" class="form-control" required 
                            style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                        <option value="">-- Pilih Varian Rokok (Dwipantara / Sembada / Kereta Kencana) --</option>
                        @foreach($produks as $p)
                            <option value="{{ $p->id }}" 
                                    data-harga-slop="{{ $p->harga_per_slop }}"
                                    data-harga-bal="{{ $p->harga_per_bal }}" 
                                    data-min-slop="{{ $p->min_order_slop ?? 1 }}"
                                    data-min-bal="{{ $p->min_order_bal ?? 1 }}"
                                    data-stok="{{ $p->stok }}"
                                    data-slop="{{ $p->slop_per_bal }}"
                                    data-bungkus="{{ $p->bungkus_per_slop }}"
                                    {{ (old('barang_id') == $p->id || (isset($selectedProduk) && $selectedProduk->id == $p->id)) ? 'selected' : '' }}>
                                {{ $p->nama }} ({{ $p->kategori->nama_kategori }}) &bull; {{ $p->formatted_harga_slop }}/Slop &bull; {{ $p->formatted_harga }}/Bal
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilihan Satuan (Slop vs Bal) -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 8px;">Pilihan Satuan Pembelian <span style="color: var(--danger);">*</span></label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <label class="satuan-card" id="cardSatuanSlop" style="background: #121619; border: 2px solid var(--gold); border-radius: 8px; padding: 16px; cursor: pointer; display: flex; align-items: flex-start; gap: 12px; transition: 0.2s;">
                            <input type="radio" name="satuan" value="Slop" id="satuanSlop" {{ old('satuan', $selectedSatuan ?? 'Slop') === 'Slop' ? 'checked' : '' }} style="margin-top: 4px;">
                            <div>
                                <strong style="color: var(--text-white); font-size: 15px; display: block;">Per Slop (10 Bungkus)</strong>
                                <span style="color: var(--gold); font-size: 12px; font-weight: 600; display: block; margin-top: 2px;">★ Rekomendasi: Minimal Cuma 1 Slop</span>
                                <small style="color: var(--text-muted); font-size: 11px; display: block; margin-top: 4px;">Cocok untuk toko kelontong, warung kopi, atau uji pasar daerah baru.</small>
                            </div>
                        </label>

                        <label class="satuan-card" id="cardSatuanBal" style="background: #121619; border: 1px solid var(--charcoal-border); border-radius: 8px; padding: 16px; cursor: pointer; display: flex; align-items: flex-start; gap: 12px; transition: 0.2s;">
                            <input type="radio" name="satuan" value="Bal" id="satuanBal" {{ old('satuan', $selectedSatuan ?? 'Slop') === 'Bal' ? 'checked' : '' }} style="margin-top: 4px;">
                            <div>
                                <strong style="color: var(--text-white); font-size: 15px; display: block;">Per Bal (20 Slop / 200 Bungkus)</strong>
                                <span style="color: #94a3b8; font-size: 12px; font-weight: 600; display: block; margin-top: 2px;">Paket Grosir Distributor (B2B)</span>
                                <small style="color: var(--text-muted); font-size: 11px; display: block; margin-top: 4px;">Kemasan dus segel pabrik untuk pasokan distributor & agen partai besar.</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">
                            Jumlah Pesanan (<span id="labelSatuanJumlah">Slop</span>) <span style="color: var(--danger);">*</span>
                        </label>
                        <input type="number" name="jumlah" id="inputJumlah" class="form-control" min="1" value="{{ old('jumlah', 1) }}" required
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                        <small id="minOrderNotice" style="display: block; color: var(--gold); font-size: 12px; margin-top: 4px;">Min. Order: 1 Slop (10 Bungkus)</small>
                    </div>

                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Harga Satuan Berlaku</label>
                        <input type="text" id="displayHargaSatuan" class="form-control" readonly value="Rp 0"
                               style="width: 100%; padding: 12px; background: #0d1012; border: 1px solid var(--charcoal-border); color: var(--gold); font-weight: 700; border-radius: 6px;">
                        <small style="display: block; color: var(--text-muted); font-size: 12px; margin-top: 4px;">Harga resmi pabrik berpita cukai</small>
                    </div>
                </div>

                <!-- Kalkulator Ringkasan Nilai Order Dinamis -->
                <div style="background: rgba(197, 160, 89, 0.08); border: 1px solid var(--gold); border-radius: var(--radius); padding: 18px 24px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <span style="display: block; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Total Volume Rokok Didapat:</span>
                        <strong id="calcSlop" style="font-size: 18px; color: var(--text-white);">1 Slop (10 Bungkus)</strong>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; color: var(--text-muted); font-size: 12px; text-transform: uppercase;">Estimasi Total Faktur:</span>
                        <strong id="calcTotal" style="font-size: 26px; color: var(--gold); font-weight: 800;">Rp 0</strong>
                    </div>
                </div>

                <div style="border-bottom: 1px solid var(--charcoal-border); padding-bottom: 20px; margin-bottom: 24px;">
                    <h3 style="color: var(--gold); font-size: 18px; margin-bottom: 6px;">
                        <i class="fa-solid fa-address-card"></i> 2. Identitas Pemesan / Toko Mitra
                    </h3>
                    <p style="color: var(--text-muted); font-size: 13px;">Data resmi mitra untuk surat jalan, nota fisik, & konfirmasi pengiriman armada.</p>
                </div>

                <div class="grid-2col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Nama Toko / Warung / Mitra Distributor <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="nama_mitra" class="form-control" required value="{{ old('nama_mitra') }}" placeholder="Contoh: Toko Berkah Mandiri / Kios Barokah"
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>

                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Nomor WhatsApp Aktif <span style="color: var(--danger);">*</span></label>
                        <input type="tel" name="telepon" class="form-control" required value="{{ old('telepon') }}" placeholder="08xxxxxxxxxx"
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Alamat Lengkap Tujuan Pengiriman <span style="color: var(--danger);">*</span></label>
                    <textarea name="alamat" class="form-control" rows="3" required placeholder="Alamat pengiriman toko/rumah, nama jalan, RT/RW, desa/kelurahan, kecamatan, kabupaten/kota..."
                              style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">{{ old('alamat') }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display: block; color: var(--text-light); font-size: 14px; margin-bottom: 6px;">Catatan Khusus Pengiriman (Opsional)</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Kirim via kargo langganan, titip bus/travel, atau ambil sendiri di gudang pabrik Ponggok..."
                              style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">{{ old('catatan') }}</textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 16px; font-weight: 700; font-size: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="fa-solid fa-file-invoice"></i> Terbitkan Surat Pesanan & Faktur Resmi
                </button>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('selectBarang');
    const inputJumlah = document.getElementById('inputJumlah');
    const calcTotal = document.getElementById('calcTotal');
    const calcSlop = document.getElementById('calcSlop');
    const notice = document.getElementById('minOrderNotice');
    const labelSatuan = document.getElementById('labelSatuanJumlah');
    const displayHargaSatuan = document.getElementById('displayHargaSatuan');
    const radioSlop = document.getElementById('satuanSlop');
    const radioBal = document.getElementById('satuanBal');
    const cardSlop = document.getElementById('cardSatuanSlop');
    const cardBal = document.getElementById('cardSatuanBal');

    function updateCards() {
        if (radioSlop.checked) {
            cardSlop.style.borderColor = 'var(--gold)';
            cardSlop.style.background = 'rgba(197, 160, 89, 0.08)';
            cardSlop.style.boxShadow = '0 6px 18px rgba(197, 160, 89, 0.22)';
            cardSlop.style.transform = 'scale(1.01)';
            cardBal.style.borderColor = 'var(--charcoal-border)';
            cardBal.style.background = '#121619';
            cardBal.style.boxShadow = 'none';
            cardBal.style.transform = 'scale(1)';
            labelSatuan.innerText = 'Slop';
        } else {
            cardBal.style.borderColor = 'var(--gold)';
            cardBal.style.background = 'rgba(197, 160, 89, 0.08)';
            cardBal.style.boxShadow = '0 6px 18px rgba(197, 160, 89, 0.22)';
            cardBal.style.transform = 'scale(1.01)';
            cardSlop.style.borderColor = 'var(--charcoal-border)';
            cardSlop.style.background = '#121619';
            cardSlop.style.boxShadow = 'none';
            cardSlop.style.transform = 'scale(1)';
            labelSatuan.innerText = 'Bal';
        }
    }

    function hitung() {
        updateCards();
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) {
            calcTotal.innerText = 'Rp 0';
            calcSlop.innerText = '0 Bungkus';
            displayHargaSatuan.value = 'Rp 0';
            return;
        }

        const isSlop = radioSlop.checked;
        const hargaSlop = parseFloat(opt.dataset.hargaSlop || 0);
        const hargaBal = parseFloat(opt.dataset.hargaBal || 0);
        const minSlop = parseInt(opt.dataset.minSlop || 1);
        const minBal = parseInt(opt.dataset.minBal || 1);
        const slopPerBal = parseInt(opt.dataset.slop || 20);
        const bungkusPerSlop = parseInt(opt.dataset.bungkus || 10);

        let jumlah = parseInt(inputJumlah.value || 1);
        if (jumlah < 1) jumlah = 1;

        if (isSlop) {
            notice.innerText = `Min. Order: ${minSlop} Slop (${minSlop * bungkusPerSlop} Bungkus)`;
            inputJumlah.min = minSlop;
            displayHargaSatuan.value = 'Rp ' + hargaSlop.toLocaleString('id-ID') + ' / Slop';

            const total = hargaSlop * jumlah;
            const totalBungkus = jumlah * bungkusPerSlop;

            calcTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');
            calcSlop.innerText = `${jumlah.toLocaleString('id-ID')} Slop (${totalBungkus.toLocaleString('id-ID')} Bungkus)`;
        } else {
            notice.innerText = `Min. Order: ${minBal} Bal (${minBal * slopPerBal} Slop / ${minBal * slopPerBal * bungkusPerSlop} Bungkus)`;
            inputJumlah.min = minBal;
            displayHargaSatuan.value = 'Rp ' + hargaBal.toLocaleString('id-ID') + ' / Bal';

            const total = hargaBal * jumlah;
            const totalSlop = jumlah * slopPerBal;
            const totalBungkus = totalSlop * bungkusPerSlop;

            calcTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');
            calcSlop.innerText = `${jumlah.toLocaleString('id-ID')} Bal (${totalSlop.toLocaleString('id-ID')} Slop / ${totalBungkus.toLocaleString('id-ID')} Bks)`;
        }
    }

    radioSlop.addEventListener('change', hitung);
    radioBal.addEventListener('change', hitung);
    select.addEventListener('change', hitung);
    inputJumlah.addEventListener('input', hitung);
    hitung();
});
</script>
@endpush
@endsection
