@extends('layouts.app')

@section('title', 'Masuk Akun Mitra & Admin Pabrik')

@section('content')
@php
    $loginBanner = $loginBanner ?? \App\Services\PabrikConfigService::getLoginBanner();
@endphp

<section class="login-split-section">
    <!-- Panel Kanan: Gambar Kustom dari Pengaturan & Media -->
    <div class="login-split-right">
        @if(!empty($loginBanner['exists']))
            <img src="{{ $loginBanner['url'] }}" alt="{{ $loginBanner['caption'] ?? 'PR. Kereta Kencana' }}" class="login-split-bg-img">
        @endif
        <div class="login-split-bg-overlay"></div>

        <!-- Gelombang Halus Mobile (Tampil hanya di layar kecil saat bertumpuk) -->
        <svg class="login-wave-mobile" viewBox="0 0 1200 120" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,40 C260,115 480,0 760,65 C980,115 1100,45 1200,75 L1200,120 L0,120 Z" fill="#ffffff"></path>
        </svg>

        @if(!empty($loginBanner['caption']))
            <div class="login-split-caption">
                <span class="login-split-caption-eyebrow">PR. KERETA KENCANA &bull; BLITAR</span>
                <div class="login-split-caption-text">{{ $loginBanner['caption'] }}</div>
            </div>
        @endif
    </div>

    <!-- Panel Kiri: Formulir Login + Pemisah Gelombang Organik -->
    <div class="login-split-left">
        <!-- Gelombang Vertikal Organik Pembatas Kiri -> Kanan (Desktop) -->
        <svg class="login-wave-desktop" viewBox="0 0 210 800" preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,0 L28,0 C4,95 16,160 66,205 C122,255 126,315 96,368 C66,422 108,468 134,524 C158,576 136,642 166,710 C184,752 196,778 208,800 L0,800 Z" fill="#ffffff"></path>
        </svg>

        <div class="login-split-form-wrap">
            <div class="login-brand-head">
                <img src="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}" alt="PR. Kereta Kencana" class="login-brand-logo">
                <div>
                    <div class="login-brand-title">PORTAL MASUK AKUN</div>
                    <div class="login-brand-sub">PR. KERETA KENCANA &bull; Ponggok, Blitar</div>
                </div>
            </div>

            @if(session('warning'))
                <div class="login-alert login-alert-warning">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="login-alert login-alert-danger">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <div>
                        @foreach($errors->all() as $err)
                            <div>{{ $err }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="login-clean-form">
                @csrf

                <div class="login-field-group">
                    <label for="inputEmail" class="login-field-label">EMAIL</label>
                    <input
                        type="email"
                        name="email"
                        id="inputEmail"
                        class="login-underline-input"
                        required
                        autocomplete="email"
                        value="{{ old('email') }}"
                        placeholder="nama@email.com"
                    >
                </div>

                <div class="login-field-group">
                    <label for="inputPassword" class="login-field-label">PASSWORD</label>
                    <div class="login-password-wrap">
                        <input
                            type="password"
                            name="password"
                            id="inputPassword"
                            class="login-underline-input"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••••"
                        >
                        <button
                            type="button"
                            id="togglePasswordBtn"
                            class="login-password-toggle"
                            onclick="togglePasswordVisibility('inputPassword', 'togglePasswordIcon')"
                            aria-label="Lihat / Sembunyikan Kata Sandi"
                            title="Lihat / Sembunyikan Kata Sandi"
                        >
                            <i id="togglePasswordIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="login-options-row">
                    <label class="login-remember-label">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember Me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="login-forgot-link">
                        <i class="fa-solid fa-key"></i> Lupa Kata Sandi?
                    </a>
                </div>

                <div class="login-action-buttons">
                    <button type="submit" class="login-btn-primary">
                        MASUK
                    </button>
                    <a href="{{ route('register') }}" class="login-btn-secondary">
                        BUAT AKUN
                    </a>
                </div>
            </form>
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
