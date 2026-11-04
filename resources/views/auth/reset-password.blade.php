@extends('layouts.app')

@section('title', 'Atur Ulang Kata Sandi - PR. KERETA KENCANA')

@section('content')
<section class="section-py" style="min-height: 80vh; display: flex; align-items: center; padding: 40px 0;">
    <div class="container" style="max-width: 480px;">
        <div class="auth-card scroll-reveal reveal-zoom">
            
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="text-align: center; margin-bottom: 14px;">
                    <img src="{{ asset('assets/img/logo-resmi.png') }}" alt="PR. Kereta Kencana" style="height: 65px; width: auto; object-fit: contain; border-radius: 8px; box-shadow: var(--shadow-sm); border: 1px solid var(--border-light); padding: 3px; background: #fff;">
                </div>
                <h2 style="font-family: var(--font-serif); color: var(--gold-hover); font-size: 22px; margin-bottom: 6px; font-weight: 800;">KATA SANDI BARU</h2>
                <p style="color: var(--text-muted); font-size: 13.5px;">Silakan buat kata sandi baru yang aman untuk akun mitra Anda.</p>
            </div>

            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #ef4444; color: #b91c1c; padding: 14px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;">
                    <ul style="margin: 0; padding-left: 18px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Alamat Email</label>
                    <input type="email" name="email" id="inputEmail" class="form-control" required readonly value="{{ $email ?? old('email') }}"
                           style="width: 100%; padding: 12px; background: #f8fafc; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px; font-weight: 600;">
                </div>

                <div class="form-group" style="margin-bottom: 16px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Kata Sandi Baru</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="inputPassword" class="form-control" required placeholder="Minimal 6 karakter" minlength="6"
                               style="width: 100%; padding: 12px 42px 12px 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                        <button type="button" onclick="togglePasswordVisibility('inputPassword', 'togglePasswordIcon')" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; font-size: 15px;"
                                title="Lihat / Sembunyikan Kata Sandi">
                            <i id="togglePasswordIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 24px;">
                    <label style="display: block; color: var(--text-dark); font-size: 13.5px; margin-bottom: 6px; font-weight: 600;">Ulangi Kata Sandi Baru</label>
                    <div style="position: relative;">
                        <input type="password" name="password_confirmation" id="inputPasswordConfirm" class="form-control" required placeholder="Ulangi kata sandi baru" minlength="6"
                               style="width: 100%; padding: 12px 42px 12px 12px; background: #ffffff; border: 1px solid var(--border-light); color: var(--text-dark); border-radius: 6px;">
                        <button type="button" onclick="togglePasswordVisibility('inputPasswordConfirm', 'toggleConfirmIcon')" 
                                style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-muted); cursor: pointer; padding: 4px; font-size: 15px;"
                                title="Lihat / Sembunyikan Kata Sandi">
                            <i id="toggleConfirmIcon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-gold" style="width: 100%; padding: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-key"></i> Simpan & Perbarui Kata Sandi
                </button>
            </form>

            <div style="border-top: 1px dashed var(--border-light); margin-top: 24px; padding-top: 16px; text-align: center;">
                <a href="{{ route('login') }}" style="color: var(--gold-hover); font-weight: 600; text-decoration: none; font-size: 13.5px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-arrow-left"></i> Batalkan & Kembali ke Masuk
                </a>
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
