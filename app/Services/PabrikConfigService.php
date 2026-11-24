<?php

namespace App\Services;

use App\Models\CompanySetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PabrikConfigService
{
    public const LOGIN_BANNER_KEY = 'login-banner';

    public const HERO_SLOTS = [
        'hero_1' => [
            'label' => 'Banner Hero Beranda (Slide 1)',
            'location' => 'Beranda • Latar Slider Atas #1',
            'default_file' => 'assets/img/pro-produksi-hero.jpg',
            'default_title' => 'Lini Produksi SKT PR. KERETA KENCANA',
        ],
        'hero_2' => [
            'label' => 'Banner Hero Beranda (Slide 2)',
            'location' => 'Beranda • Latar Slider Atas #2',
            'default_file' => 'assets/img/pro-cengkeh-tembakau.jpg',
            'default_title' => 'Racikan Tembakau & Cengkeh Pilihan',
        ],
    ];

    public const FASILITAS_LOCATIONS = [
        'pabrik-beranda' => 'Beranda • Tentang Pabrik',
        'proses-bahan-baku' => 'Beranda • Tahap 1 Penyiapan Bahan',
        'proses-linting-skt' => 'Beranda • Tahap 2 Linting & Cukai',
        'proses-gudang-distribusi' => 'Beranda • Tahap 3 Pengemasan & Gudang',
        'pabrik-ponggok' => 'Profil Pabrik • Visual Utama Pabrik',
        'fasilitas-tembakau' => 'Profil Pabrik • Pilar 01 Bahan Baku',
        'galeri-produksi-skt' => 'Profil Pabrik • Pilar 02 Ruang Produksi',
        'fasilitas-cukai' => 'Profil Pabrik • Pilar 03 Pita Cukai',
        'fasilitas-gudang' => 'Profil Pabrik • Pilar 04 Gudang Distribusi',
    ];

    /**
     * Ambil map setting dari DB secara aman bahkan jika tabel belum tersedia saat bootstrap CLI.
     */
    public static function getSettingsMap(): array
    {
        try {
            return CompanySetting::query()
                ->pluck('setting_value', 'setting_key')
                ->toArray();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function waPabrik(): string
    {
        $map = static::getSettingsMap();
        $val = trim((string) ($map['wa_pabrik'] ?? ''));
        if ($val !== '') {
            return self::normalizePhone($val);
        }

        return self::normalizePhone((string) (config('pabrik.wa_pabrik', '6285790627104') ?: '6285790627104'));
    }

    public static function waPabrikDisplay(): string
    {
        $digits = self::normalizePhone(static::waPabrik());
        if (str_starts_with($digits, '62') && strlen($digits) >= 10) {
            $rest = substr($digits, 2);
            return '+62 ' . substr($rest, 0, 3) . '-' . substr($rest, 3, 4) . '-' . substr($rest, 7);
        }

        return '+' . ltrim($digits, '+');
    }

    public static function nppbkc(): string
    {
        $map = static::getSettingsMap();
        $val = trim((string) ($map['nppbkc'] ?? ''));

        return $val !== ''
            ? $val
            : (string) (config('pabrik.nppbkc', '750******-070700-221221*******') ?: '750******-070700-221221*******');
    }

    public static function jamOperasional(): string
    {
        $map = static::getSettingsMap();
        $val = trim((string) ($map['jam_operasional'] ?? ''));

        return $val !== ''
            ? $val
            : (string) (config('pabrik.jam_operasional', 'Buka: 09.00 - 16.00 WIB (Senin - Sabtu)') ?: 'Buka: 09.00 - 16.00 WIB (Senin - Sabtu)');
    }

    public static function bankInfo(): array
    {
        $map = static::getSettingsMap();

        $nama = array_key_exists('bank_nama', $map)
            ? (trim((string) $map['bank_nama']) ?: null)
            : (config('pabrik.bank_nama') ?: null);
        $rekening = array_key_exists('bank_rekening', $map)
            ? (trim((string) $map['bank_rekening']) ?: null)
            : (config('pabrik.bank_rekening') ?: null);
        $atasNama = array_key_exists('bank_atas_nama', $map)
            ? (trim((string) $map['bank_atas_nama']) ?: null)
            : (config('pabrik.bank_atas_nama') ?: null);

        return [
            'nama' => $nama,
            'rekening' => $rekening,
            'atas_nama' => $atasNama,
        ];
    }

    public static function getExtraHeroKeys(): array
    {
        $map = static::getSettingsMap();
        $raw = trim((string) ($map['hero_extra_keys'] ?? ''));
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        $keys = [];
        foreach ($decoded as $k) {
            $k = trim((string) $k);
            if (
                $k !== ''
                && !array_key_exists($k, self::HERO_SLOTS)
                && preg_match('/^hero_\d+$/', $k)
                && !in_array($k, $keys, true)
            ) {
                $keys[] = $k;
            }
        }

        return $keys;
    }

    public static function getAllHeroKeys(): array
    {
        return array_merge(array_keys(self::HERO_SLOTS), self::getExtraHeroKeys());
    }

    public static function isExtraHeroKey(string $key): bool
    {
        return !array_key_exists($key, self::HERO_SLOTS) && in_array($key, self::getExtraHeroKeys(), true);
    }

    public static function getHeroSlides(bool $includeMissingExtra = false): array
    {
        $map = static::getSettingsMap();
        $slides = [];

        foreach (self::HERO_SLOTS as $key => $meta) {
            $customFile = trim((string) ($map["media_file_{$key}"] ?? ''));
            $title = trim((string) ($map["media_caption_{$key}"] ?? '')) ?: $meta['default_title'];
            $updatedAt = trim((string) ($map["media_updated_at_{$key}"] ?? ''));

            $resolved = self::resolveMediaAsset($customFile, $meta['default_file']);

            $slides[] = [
                'key' => $key,
                'label' => $meta['label'],
                'location' => $meta['location'],
                'img' => $resolved['url'],
                'url' => $resolved['url'],
                'exists' => $resolved['exists'],
                'is_custom' => $resolved['is_custom'],
                'is_built_in' => true,
                'title' => $title,
                'caption' => $title,
                'updated_at' => $updatedAt ?: '2026-11-20',
                'updated_at_label' => ($updatedAt ?: '2026-11-20')
                    ? Carbon::parse($updatedAt ?: '2026-11-20')->locale('id')->translatedFormat('d F Y')
                    : null,
            ];
        }

        $slideNumber = count($slides);
        foreach (self::getExtraHeroKeys() as $key) {
            $customFile = trim((string) ($map["media_file_{$key}"] ?? ''));
            $resolved = self::resolveMediaAsset($customFile, '');
            if (!$includeMissingExtra && !$resolved['exists']) {
                continue;
            }

            $slideNumber++;
            $title = trim((string) ($map["media_caption_{$key}"] ?? '')) ?: "Banner Tambahan PR. KERETA KENCANA (#{$slideNumber})";
            $updatedAt = trim((string) ($map["media_updated_at_{$key}"] ?? ''));

            $slides[] = [
                'key' => $key,
                'label' => "Banner Hero Beranda (Slide {$slideNumber})",
                'location' => "Beranda • Latar Slider Atas #{$slideNumber}",
                'img' => $resolved['url'],
                'url' => $resolved['url'],
                'exists' => $resolved['exists'],
                'is_custom' => true,
                'is_built_in' => false,
                'title' => $title,
                'caption' => $title,
                'updated_at' => $updatedAt ?: now()->toDateString(),
                'updated_at_label' => ($updatedAt ?: now()->toDateString())
                    ? Carbon::parse($updatedAt ?: now()->toDateString())->locale('id')->translatedFormat('d F Y')
                    : null,
            ];
        }

        return $slides;
    }

    public static function getLoginBanner(): array
    {
        $map = static::getSettingsMap();
        $key = self::LOGIN_BANNER_KEY;
        $customFile = trim((string) ($map["media_file_{$key}"] ?? ''));
        $caption = trim((string) ($map["media_caption_{$key}"] ?? ''))
            ?: 'Portal Resmi Distribusi Sigaret Kretek Tangan • Ponggok, Kab. Blitar';
        $updatedAt = trim((string) ($map["media_updated_at_{$key}"] ?? ''));

        $resolved = self::resolveMediaAsset($customFile, 'assets/img/pro-produksi-hero.jpg');

        return [
            'key' => $key,
            'label' => 'Banner Panel Kanan Halaman Login',
            'location' => 'Halaman Login (/login) • Sisi Kanan',
            'url' => $resolved['url'],
            'exists' => $resolved['exists'],
            'is_custom' => $resolved['is_custom'],
            'caption' => $caption,
            'updated_at' => $updatedAt ?: '2026-11-24',
            'updated_at_label' => ($updatedAt ?: '2026-11-24')
                ? Carbon::parse($updatedAt ?: '2026-11-24')->locale('id')->translatedFormat('d F Y')
                : null,
        ];
    }

    public static function getFasilitasImages(): array
    {
        $map = static::getSettingsMap();
        $items = config('pabrik.fasilitas_images', []);

        foreach ($items as $key => $item) {
            $customFile = trim((string) ($map["media_file_{$key}"] ?? ''));
            $caption = trim((string) ($map["media_caption_{$key}"] ?? '')) ?: ($item['caption'] ?? '');
            $updatedAt = trim((string) ($map["media_updated_at_{$key}"] ?? '')) ?: ($item['updated_at'] ?? null);

            $resolved = self::resolveMediaAsset($customFile, (string) ($item['file'] ?? ''));

            $items[$key]['key'] = $key;
            $items[$key]['location'] = self::FASILITAS_LOCATIONS[$key] ?? 'Halaman Publik';
            $items[$key]['caption'] = $caption;
            $items[$key]['updated_at'] = $updatedAt;
            $items[$key]['updated_at_label'] = $updatedAt
                ? Carbon::parse($updatedAt)->locale('id')->translatedFormat('d F Y')
                : null;
            $items[$key]['url'] = $resolved['url'];
            $items[$key]['exists'] = $resolved['exists'];
            $items[$key]['is_custom'] = $resolved['is_custom'];
        }

        return $items;
    }

    public static function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/[^0-9]/', '', $raw) ?? '';
        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (!str_starts_with($digits, '62') && $digits !== '') {
            $digits = '62' . $digits;
        }

        return $digits ?: '6285790627104';
    }

    private static function resolveMediaAsset(string $customStoragePath, string $defaultPublicRelativePath): array
    {
        if ($customStoragePath !== '' && Storage::disk('public')->exists($customStoragePath)) {
            $full = Storage::disk('public')->path($customStoragePath);
            $v = is_file($full) ? filemtime($full) : time();

            return [
                'url' => asset('storage/' . $customStoragePath) . '?v=' . $v,
                'exists' => true,
                'is_custom' => true,
            ];
        }

        if ($defaultPublicRelativePath !== '' && file_exists(public_path($defaultPublicRelativePath))) {
            return [
                'url' => asset($defaultPublicRelativePath) . '?v=' . filemtime(public_path($defaultPublicRelativePath)),
                'exists' => true,
                'is_custom' => false,
            ];
        }

        return [
            'url' => $defaultPublicRelativePath !== '' ? asset($defaultPublicRelativePath) : '',
            'exists' => false,
            'is_custom' => false,
        ];
    }
}
