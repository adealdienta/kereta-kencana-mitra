@extends('layouts.app')

@section('title', 'Pendaftaran Akun Mitra & Toko')

@section('content')
<section class="section-py" style="min-height: 80vh; display: flex; align-items: center; padding: 40px 0;">
    <div class="container" style="max-width: 580px;">
        <div class="auth-card" style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 36px; box-shadow: var(--shadow-lg);">
            
            <div style="text-align: center; margin-bottom: 26px;">
                <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" style="height: 65px; width: auto; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4); margin-bottom: 12px;">
                <span class="eyebrow" style="color: var(--gold); display: block; margin-bottom: 4px;"><i class="fa-solid fa-handshake"></i> KEMITRAAN B2B PABRIK</span>
                <h2 style="font-family: var(--font-serif); color: #ffffff; font-size: 24px; margin-bottom: 6px;">Pendaftaran Akun Mitra Toko</h2>
                <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
                    Daftarkan toko, warung, atau keagenan Anda untuk memesan langsung rokok resmi berpita cukai dari PR. KERETA KENCANA Blitar.
                </p>
            </div>

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; border-radius: 8px; padding: 14px; margin-bottom: 20px;">
                    <div style="color: #ef4444; font-weight: 600; font-size: 14px; margin-bottom: 6px;">
                        <i class="fa-solid fa-circle-exclamation"></i> Terdapat kendala pengisian formulir:
                    </div>
                    <ul style="color: #fca5a5; font-size: 13px; margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                        Nama Lengkap Pemilik / Penanggung Jawab <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="Contoh: Budi Santoso"
                           style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                        Nama Toko / Warung / Agen Distributor <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nama_toko" class="form-control" required value="{{ old('nama_toko') }}" placeholder="Contoh: Toko Berkah Barokah"
                           style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Email Aktif <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="toko@gmail.com"
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Nomor WhatsApp <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="tel" name="telepon" class="form-control" required value="{{ old('telepon') }}" placeholder="Contoh: 081234567890"
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                        Alamat Lengkap Pengiriman Toko <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="alamat" class="form-control" rows="2" required placeholder="Nama jalan, RT/RW, kelurahan, kecamatan, kota/kabupaten..."
                              style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">{{ old('alamat') }}</textarea>
                    <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">Alamat ini akan digunakan kurir armada pabrik/ekspedisi logistik untuk pengiriman rokok.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Kata Sandi <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="password" name="password" class="form-control" required placeholder="Minimal 6 karakter"
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Ulangi Sandi <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="password" name="password_confirmation" class="form-control" required placeholder="Ulangi kata sandi"
                               style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                    </div>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-user-plus"></i> Daftar Sebagai Mitra & Mulai Pesan
                </button>
            </form>

            <div style="border-top: 1px dashed var(--charcoal-border); margin-top: 24px; padding-top: 16px; text-align: center;">
                <p style="color: var(--text-muted); font-size: 13.5px; margin-bottom: 0;">
                    Sudah memiliki akun toko terdaftar? 
                    <a href="{{ route('login') }}" style="color: var(--gold); font-weight: 600; text-decoration: underline;">
                        Masuk ke Akun Anda
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
