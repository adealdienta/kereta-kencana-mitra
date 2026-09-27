@extends('layouts.app')

@section('title', 'Masuk Akun Mitra & Staf Pabrik')

@section('content')
<section class="section-py" style="min-height: 80vh; display: flex; align-items: center; padding: 40px 0;">
    <div class="container" style="max-width: 480px;">
        <div class="auth-card" style="background: var(--bg-card); border: 1px solid var(--charcoal-border); border-radius: var(--radius-lg); padding: 32px; box-shadow: var(--shadow-lg);">
            
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="text-align: center; margin-bottom: 14px;">
                    <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" style="height: 65px; width: auto; object-fit: contain; border-radius: 8px; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);">
                </div>
                <h2 style="font-family: var(--font-serif); color: var(--gold); font-size: 22px; margin-bottom: 6px;">PORTAL MASUK AKUN</h2>
                <p style="color: var(--text-muted); font-size: 13.5px;">PR. KERETA KENCANA - Ponggok, Kab. Blitar</p>
            </div>

            <!-- Quick Demo Role Switches -->
            <div style="background: rgba(255,255,255,0.03); border: 1px dashed var(--charcoal-border); padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <div style="font-size: 11.5px; color: var(--gold); font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Klik Cepat Demo Akun:
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <button type="button" class="btn btn-sm btn-secondary" onclick="fillDemo('owner@keretakencana.com', 'password123')">
                        <i class="fa-solid fa-crown"></i> Owner Pabrik
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="fillDemo('staff@keretakencana.com', 'password123')">
                        <i class="fa-solid fa-user-gear"></i> Staf Gudang
                    </button>
                </div>
            </div>

            @if(session('warning'))
                <div style="background: rgba(234, 179, 8, 0.12); border: 1px solid #eab308; color: #fde047; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 16px;"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Alamat Email</label>
                    <input type="email" name="email" id="inputEmail" class="form-control" required value="{{ old('email') }}" placeholder="nama@email.com"
                           style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-light); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Kata Sandi</label>
                    <input type="password" name="password" id="inputPassword" class="form-control" required placeholder="••••••••"
                           style="width: 100%; padding: 12px; background: #121619; border: 1px solid var(--charcoal-border); color: #fff; border-radius: 6px;">
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 13px;">
                    <label style="color: var(--text-muted); display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
                </button>
            </form>

            <div style="border-top: 1px dashed var(--charcoal-border); margin-top: 24px; padding-top: 16px; text-align: center;">
                <p style="color: var(--text-muted); font-size: 13.5px; margin-bottom: 10px;">
                    Belum memiliki akun mitra toko?
                </p>
                <a href="{{ route('register') }}" class="btn btn-outline-gold btn-block btn-sm" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px;">
                    <i class="fa-solid fa-user-plus"></i> Daftar Akun Mitra / Toko Baru
                </a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function fillDemo(email, pass) {
    document.getElementById('inputEmail').value = email;
    document.getElementById('inputPassword').value = pass;
}
</script>
@endpush
@endsection
