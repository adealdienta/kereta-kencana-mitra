@extends('layouts.app')

@section('title', 'Pendaftaran Akun Mitra & Toko')

@section('content')
<section class="section-py" style="min-height: 80vh; display: flex; align-items: center; padding: 40px 0;">
    <div class="container" style="max-width: 580px;">
        <div class="auth-card scroll-reveal reveal-zoom">
            
            <div style="text-align: center; margin-bottom: 24px;">
                <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" style="height: 65px; width: auto; object-fit: contain; border-radius: 8px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light); padding: 3px; background: #fff; margin-bottom: 12px;">
                <span class="eyebrow" style="color: var(--gold); display: block; margin-bottom: 4px;"><i class="fa-solid fa-handshake"></i> KEMITRAAN B2B PABRIK</span>
                <h2 style="font-family: var(--font-serif); color: var(--gold-hover); font-size: 22px; margin-bottom: 6px; font-weight: 800;">PENDAFTARAN AKUN MITRA TOKO</h2>
                <p style="color: var(--text-muted); font-size: 13.5px; line-height: 1.6;">
                    Daftarkan toko, warung, atau keagenan Anda untuk memesan langsung rokok resmi berpita cukai dari PR. KERETA KENCANA Blitar.
                </p>
            </div>

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; border-radius: 8px; padding: 14px; margin-bottom: 20px;">
                    <div style="color: #ef4444; font-weight: 600; font-size: 14px; margin-bottom: 6px;">
                        <i class="fa-solid fa-circle-exclamation"></i> Terdapat kendala pengisian formulir:
                    </div>
                    <ul style="color: #b91c1c; font-size: 13px; margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                        Nama Lengkap Pemilik / Penanggung Jawab <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="Contoh: Budi Santoso"
                           style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                        Nama Toko / Warung / Agen Distributor <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="nama_toko" class="form-control" required value="{{ old('nama_toko') }}" placeholder="Contoh: Toko Berkah Barokah"
                           style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Email Aktif <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}" placeholder="toko@gmail.com"
                               style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                    </div>
                    <div class="form-group">
                        <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Nomor WhatsApp <span style="color: #ef4444;">*</span>
                        </label>
                        <input type="tel" name="telepon" class="form-control" required value="{{ old('telepon') }}" placeholder="Contoh: 081234567890"
                               style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                        Alamat Lengkap Pengiriman Toko <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea name="alamat" class="form-control" rows="2" required placeholder="Nama jalan, RT/RW, kelurahan, kecamatan, kota/kabupaten..."
                              style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">{{ old('alamat') }}</textarea>
                    <small style="color: var(--text-muted); font-size: 11.5px; display: block; margin-top: 4px;">Alamat ini akan digunakan kurir armada pabrik/ekspedisi logistik untuk pengiriman rokok.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px;">
                    <div class="form-group">
                        <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Kata Sandi <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative;">
                            <input type="password" name="password" id="inputRegPassword" class="form-control" required placeholder="Minimal 6 karakter"
                                   style="width: 100%; padding: 12px 42px 12px 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                            <button type="button" onclick="togglePasswordVisibility('inputRegPassword', 'toggleRegPassIcon')" 
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; font-size: 15px;"
                                    title="Lihat / Sembunyikan Kata Sandi">
                                <i id="toggleRegPassIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">
                            Ulangi Sandi <span style="color: #ef4444;">*</span>
                        </label>
                        <div style="position: relative;">
                            <input type="password" name="password_confirmation" id="inputRegConfirm" class="form-control" required placeholder="Ulangi kata sandi"
                                   style="width: 100%; padding: 12px 42px 12px 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                            <button type="button" onclick="togglePasswordVisibility('inputRegConfirm', 'toggleRegConfirmIcon')" 
                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; font-size: 15px;"
                                    title="Lihat / Sembunyikan Kata Sandi">
                                <i id="toggleRegConfirmIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-user-plus"></i> Daftar Sebagai Mitra & Mulai Pesan
                </button>
            </form>

            <div style="border-top: 1px dashed var(--border-light); margin-top: 24px; padding-top: 16px; text-align: center;">
                <p style="color: var(--text-muted); font-size: 13.5px; margin-bottom: 0;">
                    Sudah memiliki akun toko terdaftar? 
                    <a href="{{ route('login') }}" style="color: var(--gold-hover); font-weight: 700; text-decoration: underline;">
                        Masuk ke Akun Anda
                    </a>
                </p>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function togglePasswordVisibility(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endpush
@endsection
