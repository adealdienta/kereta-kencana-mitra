@extends('layouts.admin')

@section('title', 'Log Aktivitas Audit Trail')
@section('header_title', 'Jejak Rekam Aktivitas Pengguna (Audit Trail)')

@section('content')
<div style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; padding: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h3 style="font-size: 16px; color: #1e293b;">Catatan Log Sistem & Keamanan</h3>
            <p style="color: #64748b; font-size: 13px;">Merekam setiap tindakan autentikasi, transaksi, dan perubahan data.</p>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569;">
                <th style="padding: 10px;">Waktu & Tanggal</th>
                <th style="padding: 10px;">Pengguna</th>
                <th style="padding: 10px;">Role</th>
                <th style="padding: 10px;">Aksi</th>
                <th style="padding: 10px;">Rincian Keterangan</th>
                <th style="padding: 10px;">Alamat IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 10px; color: #64748b; font-size: 12px;">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td style="padding: 10px; font-weight: 600;">{{ $log->user_nama }}</td>
                    <td style="padding: 10px;">
                        <span class="role-badge role-{{ $log->role }}">{{ strtoupper($log->role) }}</span>
                    </td>
                    <td style="padding: 10px; font-weight: 700; color: #0f1316;">{{ $log->action }}</td>
                    <td style="padding: 10px; color: #475569;">{{ $log->details ?? '-' }}</td>
                    <td style="padding: 10px; font-family: monospace; font-size: 11px; color: #64748b;">{{ $log->ip_address ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 24px; color: #94a3b8;">Belum ada log aktivitas tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        {{ $logs->links() }}
    </div>
</div>
@endsection
