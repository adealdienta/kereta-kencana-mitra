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

    <!-- Alert Informasi Keamanan Data Mitra -->
    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; font-size: 12.5px; color: #334155; display: flex; align-items: center; gap: 10px;">
        <i class="fa-solid fa-shield-halved" style="font-size: 16px; color: var(--admin-gold);"></i>
        <span>
            <strong>Keamanan & Privasi Data Mitra Terlindungi:</strong> Seluruh kata sandi toko dienkripsi secara aman dan tidak dapat dilihat oleh staf internal. Jika pemilik toko meminta bantuan akibat lupa sandi, staf dapat memicu pengiriman <em>Tautan Atur Ulang Sandi Resmi</em> langsung ke alamat email terdaftar milik toko yang bersangkutan.
        </span>
    </div>
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

                            <button type="button" class="btn btn-sm" onclick='openResetPasswordModal(@json($mitra->id), @json($mitra->nama_toko ?: $mitra->name), @json($mitra->email))'
                                    style="background: #f59e0b; color: #fff; padding: 6px 10px; border-radius: 4px; font-size: 12px; border: none; cursor: pointer;" title="Kirim Tautan Atur Ulang Sandi Resmi ke Email Mitra">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Link Reset
                            </button>
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

<!-- Modal Konfirmasi Kirim Tautan Reset Password (Privasi Mitra Terlindungi) -->
<div id="resetPasswordModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 16px;">
    <div style="background: #fff; border-radius: 8px; width: 100%; max-width: 460px; padding: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
            <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                <i class="fa-solid fa-paper-plane" style="color: #f59e0b; margin-right: 6px;"></i> Kirim Tautan Atur Ulang Sandi
            </h3>
            <button type="button" onclick="closeResetPasswordModal()" style="background: none; border: none; font-size: 18px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 14px; margin-bottom: 18px;">
            <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Toko Mitra Penerima:</div>
            <strong id="modalMitraName" style="color: #0f172a; font-size: 14px; display: block; margin-bottom: 6px;">Toko Mitra</strong>
            <div style="font-size: 12px; color: #64748b; margin-bottom: 2px;">Alamat Email Terdaftar:</div>
            <span id="modalMitraEmail" style="font-family: monospace; font-size: 13px; color: #b45309; font-weight: 700;">email@toko.com</span>
        </div>

        <p style="font-size: 12.5px; color: #475569; line-height: 1.5; margin-bottom: 20px;">
            <i class="fa-solid fa-shield-halved" style="color: #16a34a; margin-right: 4px;"></i> 
            Sistem akan men-<em>generate</em> token keamanan dan mengirimkan email resmi berisi tautan atur ulang kata sandi langsung ke kotak masuk mitra. Staf <strong>tidak dapat melihat atau mengarang</strong> kata sandi mitra demi integritas dan keamanan data akun.
        </p>

        <form id="formResetPassword" method="POST">
            @csrf

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="closeResetPasswordModal()" style="padding: 9px 16px; border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 9px 18px; border: none; background: #f59e0b; border-radius: 6px; font-size: 13px; font-weight: 700; color: #fff; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-paper-plane"></i> Kirimkan Tautan Resmi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetPasswordModal(mitraId, mitraName, mitraEmail) {
    document.getElementById('modalMitraName').innerText = mitraName;
    document.getElementById('modalMitraEmail').innerText = mitraEmail;
    document.getElementById('formResetPassword').action = '/admin/mitra/' + mitraId + '/kirim-reset-link';
    const modal = document.getElementById('resetPasswordModal');
    modal.style.display = 'flex';
}

function closeResetPasswordModal() {
    const modal = document.getElementById('resetPasswordModal');
    modal.style.display = 'none';
}
</script>

@endsection
