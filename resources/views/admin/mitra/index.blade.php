@extends('layouts.admin')

@section('title', 'Daftar Mitra Toko B2B')
@section('header_title', 'Direktori Mitra Usaha & Toko Terdaftar')

@section('content')
<!-- Header Card & Search Filter -->
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 16px;">
        <div>
            <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 4px;">
                <i class="fa-solid fa-store" style="color: var(--admin-gold); margin-right: 6px;"></i> Direktori Toko & Agen Rokok
            </h3>
            <p style="font-size: 13px; color: #64748b; margin: 0;">
                Gunakan pencarian untuk verifikasi identitas, pengecekan email terdaftar, atau koordinasi pengiriman armada.
            </p>
        </div>

        <!-- Filter / Pencarian Mitra -->
        <form action="{{ route('admin.mitra.index') }}" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 13px;"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama toko, pemilik, no WA, email..."
                       style="padding: 9px 12px 9px 34px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; width: 280px;">
            </div>
            <button type="submit" style="background: var(--admin-gold); color: #fff; border: none; padding: 9px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer;">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.mitra.index') }}" style="background: #f1f5f9; color: #475569; padding: 9px 14px; border-radius: 6px; font-size: 13px; text-decoration: none; font-weight: 600;">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Alert Informasi Hak Akses -->
    @if(auth()->user()->isStaff())
        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 12px 16px; font-size: 12.5px; color: #166534; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-shield-halved" style="font-size: 16px;"></i>
            <span>
                <strong>Akses Staf Gudang (Hanya Lihat / Read-Only):</strong> Anda dapat melihat informasi toko dan nomor WhatsApp untuk membantu mitra yang lupa email atau koordinasi pesanan. Penggantian kata sandi hanya dapat dilakukan oleh Owner atau Super Admin.
            </span>
        </div>
    @else
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; font-size: 12.5px; color: #334155; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-user-shield" style="font-size: 16px; color: var(--admin-gold);"></i>
            <span>
                <strong>Akses Manajemen Penuh (Owner & Super Admin):</strong> Anda berwenang mereset kata sandi toko jika ada permohonan bantuan resmi dari mitra melalui tombol aksi di tabel.
            </span>
        </div>
    @endif
</div>

<!-- Tabel Daftar Mitra -->
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 20px; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                <th style="padding: 12px 10px; width: 50px; text-align: center;">No</th>
                <th style="padding: 12px 10px;">Mitra / Nama Toko</th>
                <th style="padding: 12px 10px;">Email Login Akun</th>
                <th style="padding: 12px 10px;">Kontak WhatsApp</th>
                <th style="padding: 12px 10px;">Alamat Pengiriman</th>
                <th style="padding: 12px 10px; text-align: center;">Bergabung</th>
                <th style="padding: 12px 10px; text-align: center;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mitras as $index => $mitra)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 12px 10px; text-align: center; color: #94a3b8;">
                        {{ $mitras->firstItem() + $index }}
                    </td>
                    <td style="padding: 12px 10px;">
                        <strong style="color: #1e293b; font-size: 14px; display: block;">{{ $mitra->nama_toko ?: 'Toko Mitra' }}</strong>
                        <span style="color: #64748b; font-size: 12px;"><i class="fa-solid fa-user" style="font-size: 11px;"></i> {{ $mitra->name }}</span>
                    </td>
                    <td style="padding: 12px 10px;">
                        <span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 12px; color: #0f172a; font-weight: 600;">
                            {{ $mitra->email }}
                        </span>
                    </td>
                    <td style="padding: 12px 10px;">
                        @if($mitra->telepon)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mitra->telepon) }}" target="_blank" 
                               style="color: #16a34a; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-brands fa-whatsapp"></i> {{ $mitra->telepon }}
                            </a>
                        @else
                            <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td style="padding: 12px 10px; max-width: 250px; color: #475569; font-size: 12px; line-height: 1.4;">
                        {{ Str::limit($mitra->alamat ?: '-', 80) }}
                    </td>
                    <td style="padding: 12px 10px; text-align: center; color: #64748b; font-size: 12px;">
                        {{ $mitra->created_at ? $mitra->created_at->format('d/m/Y') : '-' }}
                    </td>
                    <td style="padding: 12px 10px; text-align: center;">
                        <div style="display: inline-flex; gap: 6px; align-items: center;">
                            @if($mitra->telepon)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $mitra->telepon) }}?text=Halo%20{{ urlencode($mitra->nama_toko ?? $mitra->name) }}%2C%20kami%20dari%20PR.%20Kereta%20Kencana." 
                                   target="_blank" class="btn btn-sm" 
                                   style="background: #25D366; color: #fff; padding: 6px 10px; border-radius: 4px; font-size: 12px; text-decoration: none;" title="Kirim Pesan WhatsApp">
                                    <i class="fa-brands fa-whatsapp"></i>
                                </a>
                            @endif

                            @if(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
                                <button type="button" class="btn btn-sm" onclick="openResetPasswordModal({{ $mitra->id }}, '{{ addslashes($mitra->nama_toko ?: $mitra->name) }}', '{{ $mitra->email }}')"
                                        style="background: #f59e0b; color: #fff; padding: 6px 10px; border-radius: 4px; font-size: 12px; border: none; cursor: pointer;" title="Reset Kata Sandi Mitra">
                                    <i class="fa-solid fa-key"></i> Reset Sandi
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                        <i class="fa-solid fa-store-slash" style="font-size: 32px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i>
                        Belum ada mitra toko yang terdaftar dalam sistem.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $mitras->links() }}
    </div>
</div>

@if(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
<!-- Modal Reset Password (Hanya untuk Owner & Super Admin) -->
<div id="resetPasswordModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; border-radius: 8px; width: 100%; max-width: 440px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                <i class="fa-solid fa-key" style="color: #f59e0b; margin-right: 6px;"></i> Reset Kata Sandi Mitra
            </h3>
            <button type="button" onclick="closeResetPasswordModal()" style="background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <p style="font-size: 13px; color: #475569; margin-bottom: 16px;">
            Setel kata sandi baru untuk <strong id="modalMitraName">Toko Mitra</strong> (<span id="modalMitraEmail" style="color: #64748b;">email</span>).
        </p>

        <form id="formResetPassword" method="POST">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; font-size: 13px; color: #1e293b; margin-bottom: 6px;">
                    Kata Sandi Baru <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <input type="password" name="new_password" id="modalNewPassword" required minlength="6" placeholder="Masukkan sandi baru (min. 6 karakter)"
                           style="width: 100%; padding: 10px 38px 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px;">
                    <button type="button" onclick="togglePasswordVisibility('modalNewPassword', 'toggleModalEyeIcon')" 
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; cursor: pointer; padding: 4px; font-size: 14px;">
                        <i id="toggleModalEyeIcon" class="fa-solid fa-eye"></i>
                    </button>
                </div>
                <small style="color: #64748b; font-size: 11.5px; display: block; margin-top: 4px;">
                    Contoh: Sandi mudah seperti <code>Mitra123#</code> agar pemilik toko mudah mengingatnya.
                </small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeResetPasswordModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 18px; border: none; background: #f59e0b; border-radius: 6px; font-size: 13px; font-weight: 700; color: #fff; cursor: pointer;">
                    Simpan Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetPasswordModal(mitraId, mitraName, mitraEmail) {
    document.getElementById('modalMitraName').innerText = mitraName;
    document.getElementById('modalMitraEmail').innerText = mitraEmail;
    document.getElementById('formResetPassword').action = '/admin/mitra/' + mitraId + '/reset-password';
    document.getElementById('modalNewPassword').value = '';
    const modal = document.getElementById('resetPasswordModal');
    modal.style.display = 'flex';
}

function closeResetPasswordModal() {
    const modal = document.getElementById('resetPasswordModal');
    modal.style.display = 'none';
}

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
@endif

@endsection
