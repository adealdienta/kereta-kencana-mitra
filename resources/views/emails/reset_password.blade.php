<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - PR. KERETA KENCANA</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 15px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="100%" style="max-width: 580px; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
                    
                    <!-- Header with Gold Accent -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); padding: 32px 30px; text-align: center; border-bottom: 3px solid #d97706;">
                            <div style="font-size: 22px; font-weight: 800; letter-spacing: 2px; color: #f59e0b; margin-bottom: 4px; text-transform: uppercase;">
                                PR. KERETA KENCANA
                            </div>
                            <div style="font-size: 11px; letter-spacing: 1.5px; color: #cbd5e1; text-transform: uppercase;">
                                Pabrik Sigaret Kretek Tangan (SKT) &bull; Ponggok, Blitar
                            </div>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 32px;">
                            <h2 style="margin-top: 0; margin-bottom: 16px; font-size: 20px; color: #0f172a; font-weight: 700;">
                                Permintaan Atur Ulang Kata Sandi
                            </h2>

                            <p style="font-size: 14px; color: #475569; margin-bottom: 20px;">
                                Yth. Mitra Usaha <strong>{{ $user->nama_toko ?: $user->name }}</strong>,
                            </p>

                            <p style="font-size: 14px; color: #475569; margin-bottom: 24px;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun portal mitra B2B Anda (Email: <span style="font-family: monospace; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; color: #0f172a;">{{ $user->email }}</span>).
                            </p>

                            <!-- CTA Button -->
                            <div style="text-align: center; margin: 32px 0;">
                                <a href="{{ $resetUrl }}" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #ffffff; text-decoration: none; font-size: 15px; font-weight: 700; padding: 14px 32px; border-radius: 6px; box-shadow: 0 4px 10px rgba(180, 83, 9, 0.3);">
                                    Atur Ulang Kata Sandi Sekarang &rarr;
                                </a>
                            </div>

                            @if(!empty($otpCode))
                            <!-- Mobile OTP Box (Untuk Persiapan Aplikasi Mobile) -->
                            <div style="background-color: #fffbeb; border: 1px dashed #f59e0b; border-radius: 8px; padding: 18px; text-align: center; margin-bottom: 24px;">
                                <div style="font-size: 12px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px;">
                                    Kode Verifikasi (Aplikasi Mobile)
                                </div>
                                <div style="font-family: monospace; font-size: 28px; font-weight: 800; letter-spacing: 6px; color: #b45309;">
                                    {{ $otpCode }}
                                </div>
                                <div style="font-size: 11.5px; color: #b45309; margin-top: 6px;">
                                    Gunakan kode 6 digit di atas jika Anda melakukan reset melalui Aplikasi Mobile.
                                </div>
                            </div>
                            @endif

                            <p style="font-size: 13px; color: #64748b; line-height: 1.5; margin-bottom: 20px;">
                                Tautan pengaturan ulang kata sandi ini hanya berlaku selama <strong>60 menit</strong>. Jika tautan kedaluwarsa, Anda dapat mengajukan permohonan baru di halaman masuk.
                            </p>

                            <div style="border-top: 1px solid #e2e8f0; padding-top: 18px; font-size: 12px; color: #94a3b8;">
                                <p style="margin: 0 0 6px 0;">
                                    <strong>Peringatan Keamanan:</strong> Jika Anda tidak merasa mengajukan permintaan ini, mohon abaikan email ini. Kata sandi akun Anda tidak akan berubah tanpa tindakan dari tautan di atas.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f1f5f9; padding: 20px 30px; text-align: center; font-size: 11.5px; color: #64748b; border-top: 1px solid #e2e8f0;">
                            <div>&copy; {{ date('Y') }} PR. KERETA KENCANA. Hak Cipta Dilindungi Undang-Undang.</div>
                            <div style="margin-top: 4px; color: #94a3b8;">
                                Dusun Subontoro RT. 001 RW. 015, Desa Kebonduren, Kec. Ponggok, Kabupaten Blitar, Jawa Timur
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
