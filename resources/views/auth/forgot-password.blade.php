@extends('layouts.app')

@section('title', 'Lupa Kata Sandi - PR. KERETA KENCANA')

@section('content')
<section class="section-py" style="min-height: 80vh; display: flex; align-items: center; padding: 40px 0;">
    <div class="container" style="max-width: 480px;">
        <div class="auth-card scroll-reveal reveal-zoom">
            
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="text-align: center; margin-bottom: 14px;">
                    <img src="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}" alt="PR. Kereta Kencana" style="height: 65px; width: auto; object-fit: contain; margin: 0 auto;">
                </div>
                <h2 style="font-family: var(--font-serif); color: var(--gold-hover); font-size: 22px; margin-bottom: 6px; font-weight: 800;">LUPA KATA SANDI</h2>
                <p style="color: var(--text-muted); font-size: 13.5px;">Masukkan alamat email terdaftar untuk menerima tautan atur ulang kata sandi secara aman.</p>
            </div>

            @if(session('success') || session('status'))
                <div style="background: rgba(34, 197, 94, 0.12); border: 1px solid #22c55e; color: #15803d; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-circle-check" style="font-size: 16px; margin-top: 2px;"></i>
                    <span>{{ session('success') ?: session('status') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; color: #b91c1c; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px; display: flex; align-items: flex-start; gap: 10px;">
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 16px; margin-top: 2px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; color: #b91c1c; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 20px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Alamat Email Terdaftar</label>
                    <input type="email" name="email" id="inputEmail" class="form-control" required value="{{ old('email') }}" placeholder="nama@email.com"
                           style="width: 100%; padding: 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                    <small style="color: var(--text-muted); font-size: 12px; display: block; margin-top: 5px;">
                        <i class="fa-solid fa-shield-halved" style="color: var(--gold-hover);"></i> Tautan reset rahasia akan dikirimkan langsung ke inbox email ini.
                    </small>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Tautan Atur Ulang Sandi
                </button>
            </form>

            <div style="border-top: 1px dashed var(--border-light); margin-top: 24px; padding-top: 16px; text-align: center;">
                <a href="{{ route('login') }}" style="color: var(--gold-hover); font-weight: 600; text-decoration: none; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Masuk
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
