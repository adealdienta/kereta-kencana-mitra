<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 Sesi Kedaluwarsa - PR. KERETA KENCANA</title>
    <style>
        body { margin: 0; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: #f8f6f2; color: #16130f; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .err-card { max-width: 520px; width: 100%; background: #ffffff; border: 1px solid #e2ddd3; border-top: 4px solid #b48a4e; padding: 36px 28px; text-align: center; box-shadow: 0 12px 32px rgba(22, 19, 15, 0.08); }
        .err-code { font-family: monospace; font-size: 14px; font-weight: 700; letter-spacing: 0.12em; color: #9a743b; margin-bottom: 8px; }
        h1 { margin: 0 0 12px; font-size: 24px; color: #16130f; }
        p { margin: 0 0 24px; font-size: 14.5px; line-height: 1.65; color: #57534e; }
        .err-actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        .btn-dark { background: #16130f; color: #ffffff; text-decoration: none; padding: 11px 22px; font-size: 13.5px; font-weight: 600; border-radius: 2px; border: none; cursor: pointer; }
        .btn-light { background: #f3efe7; color: #16130f; border: 1px solid #d6cfc2; text-decoration: none; padding: 11px 20px; font-size: 13.5px; font-weight: 600; border-radius: 2px; }
    </style>
</head>
<body>
    <div class="err-card">
        <div class="err-code">HTTP 419 &bull; SESI BERAKHIR</div>
        <h1>Halaman Formulir Telah Kedaluwarsa</h1>
        <p>Demi keamanan akun Anda, formulir yang dibiarkan terbuka terlalu lama akan kedaluwarsa otomatis. Muat ulang halaman untuk melanjutkan.</p>
        <div class="err-actions">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/') }}" class="btn-dark">Muat Ulang &amp; Lanjutkan</a>
            <a href="{{ url('/') }}" class="btn-light">Kembali ke Beranda</a>
        </div>
    </div>
</body>
</html>
