@extends('layouts.admin')

@section('title', 'Input Transaksi Langsung di Pabrik (Offline)')
@section('header_title', 'Kasir Penjualan Langsung Pabrik (Offline / Walk-in)')

@section('content')
<div style="max-width: 850px; background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 28px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid #f1f5f9; padding-bottom: 16px;">
        <div>
            <h3 style="font-size: 18px; color: #1e293b; margin: 0 0 4px 0;">
                <i class="fa-solid fa-cash-register" style="color: #10b981;"></i> Catat Penjualan Langsung di Pabrik (Cash & Carry)
            </h3>
            <p style="color: #64748b; font-size: 13px; margin: 0;">
                Digunakan untuk mitra toko atau pembeli umum yang datang langsung ke gudang/kantor pabrik Ponggok Blitar.
            </p>
        </div>
        <a href="{{ route('admin.transaksis.index') }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #f87171; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px;">
            <ul style="color: #dc2626; font-size: 13px; margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.transaksis.store_offline') }}" method="POST">
        @csrf

        <!-- 1. Pilihan Produk & Satuan -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
            <h4 style="font-size: 14px; color: #334155; margin: 0 0 14px 0; text-transform: uppercase; font-weight: 700;">
                1. Produk Rokok & Kuantitas
            </h4>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                    Pilih Varian Rokok SKT <span style="color: #ef4444;">*</span>
                </label>
                <select name="barang_id" id="selectBarang" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    <option value="">-- Pilih Varian Rokok --</option>
                    @foreach($barangs as $b)
                        @php $tersedia = $b->isTersediaUntukOrder(); @endphp
                        <option value="{{ $b->id }}"
                                data-harga-slop="{{ $b->harga_per_slop }}"
                                data-harga-bal="{{ $b->harga_per_bal }}"
                                data-stok="{{ $b->stok }}"
                                data-slop-per-bal="{{ $b->slop_per_bal ?: 20 }}"
                                {{ old('barang_id') == $b->id ? 'selected' : '' }}
                                {{ !$tersedia ? 'disabled' : '' }}>
                            {{ $b->nama }} (SKT {{ $b->batang_per_bungkus }}) &bull; Stok: {{ $tersedia ? $b->formatted_stok : 'Habis' }} &bull; Rp {{ number_format($b->harga_per_bal, 0, ',', '.') }}/Bal
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        Satuan Penjualan <span style="color: #ef4444;">*</span>
                    </label>
                    <select name="satuan" id="selectSatuan" class="form-control" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        <option value="Slop" {{ old('satuan', 'Slop') === 'Slop' ? 'selected' : '' }}>Per Slop (10 Bungkus)</option>
                        <option value="Bal" {{ old('satuan') === 'Bal' ? 'selected' : '' }}>Per Bal (Kemasan Bal)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        Jumlah Pembelian <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" name="jumlah" id="inputJumlah" class="form-control" min="1" value="{{ old('jumlah', 1) }}" required
                           style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-weight: 700;">
                    <small id="displayStokInfo" style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;"></small>
                </div>

                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        Total Tagihan Kasir
                    </label>
                    <div id="displayTotal" style="padding: 10px; background: #e2e8f0; border-radius: 6px; font-weight: 800; font-size: 15px; color: #0f172a;">
                        Rp 0
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Data Pembeli / Mitra -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
            <h4 style="font-size: 14px; color: #334155; margin: 0 0 14px 0; text-transform: uppercase; font-weight: 700;">
                2. Data Pembeli di Tempat
            </h4>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                    Hubungkan ke Akun Mitra Terdaftar (Opsional)
                </label>
                <select name="user_id" id="selectUser" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;" onchange="fillUserData(this)">
                    <option value="">-- Bukan Akun Terdaftar (Pembeli Walk-in / Langsung) --</option>
                    @foreach($pelanggans as $u)
                        <option value="{{ $u->id }}"
                                data-nama-toko="{{ $u->nama_toko ?: $u->name }}"
                                data-telepon="{{ $u->telepon }}"
                                data-alamat="{{ $u->alamat }}"
                                {{ old('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nama_toko ?: $u->name }} &bull; {{ $u->name }} ({{ $u->telepon ?: $u->email }})
                        </option>
                    @endforeach
                </select>
                <small style="color: #64748b; font-size: 12px; display: block; margin-top: 4px;">Jika dipilih, transaksi akan otomatis tercatat ke riwayat akun pembeli.</small>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        Nama Toko / Nama Pembeli <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nama_mitra" id="inputNamaMitra" class="form-control" value="{{ old('nama_mitra') }}" required placeholder="Contoh: Toko Barokah / Pak Slamet"
                           style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                </div>
                <div class="form-group">
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                        Nomor HP / WhatsApp
                    </label>
                    <input type="tel" name="telepon" id="inputTelepon" class="form-control" value="{{ old('telepon') }}" placeholder="Contoh: 081234567890"
                           style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                    Alamat / Asal Pembeli
                </label>
                <input type="text" name="alamat" id="inputAlamat" class="form-control" value="{{ old('alamat', 'Serah Terima Langsung di Pabrik (Ponggok, Blitar)') }}"
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
            </div>

            <div class="form-group">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px;">
                    Catatan Kasir / Keterangan Pembayaran
                </label>
                <input type="text" name="catatan" class="form-control" value="{{ old('catatan', 'Lunas Tunai / Kasir Pabrik') }}"
                       style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
            </div>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 14px 24px; font-size: 15px; font-weight: 700; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; background: #0f172a; color: #ffffff; border: none; border-radius: 8px; cursor: pointer;">
            <i class="fa-solid fa-receipt"></i> Simpan Transaksi Lunas &amp; Cetak Faktur Kasir
        </button>
    </form>
</div>

@push('scripts')
<script>
const selectBarang = document.getElementById('selectBarang');
const selectSatuan = document.getElementById('selectSatuan');
const inputJumlah = document.getElementById('inputJumlah');
const displayTotal = document.getElementById('displayTotal');
const displayStokInfo = document.getElementById('displayStokInfo');

function hitungHargaSlopBertingkat(qty) {
    if (qty > 10) return 60000;
    if (qty >= 6) return 65000;
    return 70000;
}

function hitungTotal() {
    const opt = selectBarang.options[selectBarang.selectedIndex];
    if (!opt || !opt.value) {
        displayTotal.innerText = 'Rp 0';
        displayStokInfo.innerText = '';
        inputJumlah.removeAttribute('max');
        return;
    }

    const satuan = selectSatuan.value;
    const stokSlop = parseInt(opt.dataset.stok || 0, 10) || 0;
    const slopPerBal = parseInt(opt.dataset.slopPerBal || 20, 10) || 20;
    const maxUnit = satuan === 'Bal' ? Math.floor(stokSlop / slopPerBal) : stokSlop;

    if (maxUnit > 0) {
        inputJumlah.max = maxUnit;
    } else {
        inputJumlah.removeAttribute('max');
    }

    if (satuan === 'Bal') {
        displayStokInfo.innerText = maxUnit > 0
            ? `Maks. ${maxUnit} Bal (${stokSlop} Slop tersedia)`
            : `Stok tersisa ${stokSlop} Slop (< 1 Bal), silakan pilih satuan Slop.`;
        displayStokInfo.style.color = maxUnit > 0 ? '#64748b' : '#b45309';
    } else {
        displayStokInfo.innerText = `Maks. ${stokSlop} Slop tersedia`;
        displayStokInfo.style.color = '#64748b';
    }

    let qty = Math.max(1, parseInt(inputJumlah.value || 1, 10) || 1);
    if (maxUnit > 0 && qty > maxUnit) {
        qty = maxUnit;
        inputJumlah.value = maxUnit;
    }

    const harga = satuan === 'Slop'
        ? hitungHargaSlopBertingkat(qty)
        : parseFloat(opt.dataset.hargaBal || 0);
    const total = harga * qty;

    displayTotal.innerText = 'Rp ' + total.toLocaleString('id-ID');
}

function fillUserData(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    if (opt.dataset.namaToko) document.getElementById('inputNamaMitra').value = opt.dataset.namaToko;
    if (opt.dataset.telepon) document.getElementById('inputTelepon').value = opt.dataset.telepon;
    if (opt.dataset.alamat) document.getElementById('inputAlamat').value = opt.dataset.alamat;
}

selectBarang.addEventListener('change', hitungTotal);
selectSatuan.addEventListener('change', hitungTotal);
inputJumlah.addEventListener('input', hitungTotal);
hitungTotal();
</script>
@endpush
@endsection
