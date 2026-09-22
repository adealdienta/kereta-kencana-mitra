@echo off
title Cloudflare Tunnel - PR. Kereta Kencana
echo ========================================================
echo   Membuka Cloudflare Tunnel untuk PR. Kereta Kencana
echo   Pastikan php artisan serve sedang berjalan di port 8000
echo ========================================================
echo.
echo Menghubungkan ke http://127.0.0.1:8000 ...
echo Silakan tunggu link https://....trycloudflare.com muncul di bawah:
echo.
.\cloudflared.exe tunnel --url http://127.0.0.1:8000
pause
