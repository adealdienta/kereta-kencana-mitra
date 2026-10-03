@extends('layouts.app')

@section('title', 'Masuk Akun Mitra & Staf Pabrik')

@section('content')
<section class="section-py" style="min-height: 80vh; display: flex; align-items: center; padding: 40px 0;">
    <div class="container" style="max-width: 480px;">
        <div class="auth-card scroll-reveal reveal-zoom">
            
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="text-align: center; margin-bottom: 14px;">
                    <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" style="height: 65px; width: auto; object-fit: contain; border-radius: 8px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light); padding: 3px; background: #fff;">
                </div>
                <h2 style="font-family: var(--font-serif); color: var(--gold-hover); font-size: 22px; margin-bottom: 6px; font-weight: 800;">PORTAL MASUK AKUN</h2>
                <p style="color: var(--text-muted); font-size: 13.5px;">PR. KERETA KENCANA - Ponggok, Kab. Blitar</p>
            </div>


            @if(session('warning'))
                <div style="background: rgba(234, 179, 8, 0.12); border: 1px solid #eab308; color: #854d0e; padding: 12px; border-radius: 8px; margin-bottom: 18px; font-size: 13px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 16px;"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Alamat Email</label>
                    <input type="email" name="email" id="inputEmail" class="form-control" required value="{{ old('email') }}" placeholder="nama@email.com"
                           style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Kata Sandi</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="inputPassword" class="form-control" required placeholder="••••••••"
                               style="width: 100%; padding: 12px 42px 12px 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                        <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility('inputPassword', 'togglePasswordIcon')" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; font-size: 15px;"
                                title="Lihat / Sembunyikan Kata Sandi">
                            <i id="togglePasswordIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; font-size: 13px; flex-wrap: wrap; gap: 8px;">
                    <label style="color: var(--text-muted); display: flex; align-items: center; gap: 6px; cursor: pointer;">
                        <input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini
                    </label>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%20PR.%20Kereta%20Kencana%2C%20saya%20pemilik%20toko%20ingin%20meminta%20bantuan%20terkait%20akun%20mitra%20saya%20(lupa%20email%20atau%20kata%20sandi)." 
                       target="_blank" rel="noopener noreferrer"
                       style="color: var(--gold-hover); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                        <i class="fa-brands fa-whatsapp" style="color: #25D366; font-size: 14px;"></i> Lupa Sandi / Email?
                    </a>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
                </button>
            </form>

            <div style="border-top: 1px dashed var(--border-light); margin-top: 24px; padding-top: 16px; text-align: center;">
                <p style="color: var(--text-muted); font-size: 13.5px; margin-bottom: 10px;">
                    Belum memiliki akun mitra toko?
                </p>
                <a href="{{ route('register') }}" class="btn btn-outline-gold btn-block btn-sm" style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 10px;">
                    <i class="fa-solid fa-user-plus"></i> Daftar Akun Mitra / Toko Baru
                </a>
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
