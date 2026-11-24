<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Services\PabrikConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        return view('admin.pengaturan.index', [
            'waPabrik' => PabrikConfigService::waPabrik(),
            'waPabrikDisplay' => PabrikConfigService::waPabrikDisplay(),
            'nppbkc' => PabrikConfigService::nppbkc(),
            'jamOperasional' => PabrikConfigService::jamOperasional(),
            'bankInfo' => PabrikConfigService::bankInfo(),
            'heroSlides' => PabrikConfigService::getHeroSlides(true),
            'loginBanner' => PabrikConfigService::getLoginBanner(),
            'fasilitasImages' => PabrikConfigService::getFasilitasImages(),
        ]);
    }

    public function updateUmum(Request $request)
    {
        $validated = $request->validate([
            'wa_pabrik' => 'required|string|max:30',
            'nppbkc' => 'required|string|max:80',
            'jam_operasional' => 'required|string|max:120',
            'bank_nama' => 'nullable|string|max:100',
            'bank_rekening' => 'nullable|string|max:50',
            'bank_atas_nama' => 'nullable|string|max:120',
        ]);

        $waNormalized = PabrikConfigService::normalizePhone($validated['wa_pabrik']);

        $pairs = [
            'wa_pabrik' => $waNormalized,
            'nppbkc' => trim($validated['nppbkc']),
            'jam_operasional' => trim($validated['jam_operasional']),
            'bank_nama' => trim((string) ($validated['bank_nama'] ?? '')),
            'bank_rekening' => trim((string) ($validated['bank_rekening'] ?? '')),
            'bank_atas_nama' => trim((string) ($validated['bank_atas_nama'] ?? '')),
        ];

        foreach ($pairs as $key => $val) {
            CompanySetting::updateOrCreate(
                ['setting_key' => $key],
                ['setting_value' => $val]
            );
        }

        ActivityLogger::log('Ubah Pengaturan Pabrik', "Memperbarui pengaturan resmi pabrik (WA: {$waNormalized}, NPPBKC: {$pairs['nppbkc']}, Operasional: {$pairs['jam_operasional']}).");

        return back()->with('success', 'Pengaturan WhatsApp, jam operasional, NPPBKC, dan rekening bank pabrik berhasil disimpan & disinkronkan ke Web & Mobile.');
    }

    public function storeHero(Request $request)
    {
        $validated = $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'caption' => 'nullable|string|max:180',
        ], [
            'foto.required' => 'Silakan pilih foto banner baru terlebih dahulu.',
            'foto.image' => 'Berkas banner harus berupa gambar (JPG, PNG, WebP).',
            'foto.max' => 'Ukuran foto banner maksimal 4 MB.',
        ]);

        $extraKeys = PabrikConfigService::getExtraHeroKeys();
        $nextIndex = 3;
        foreach ($extraKeys as $existingKey) {
            if (preg_match('/^hero_(\d+)$/', $existingKey, $matches)) {
                $nextIndex = max($nextIndex, (int) $matches[1] + 1);
            }
        }
        $newKey = "hero_{$nextIndex}";
        while (in_array($newKey, $extraKeys, true) || array_key_exists($newKey, PabrikConfigService::HERO_SLOTS)) {
            $nextIndex++;
            $newKey = "hero_{$nextIndex}";
        }

        $storedPath = $request->file('foto')->store('pabrik-media', 'public');
        $slideNumber = count(PabrikConfigService::HERO_SLOTS) + count($extraKeys) + 1;
        $caption = trim((string) ($validated['caption'] ?? '')) ?: "Banner Tambahan PR. KERETA KENCANA (#{$slideNumber})";

        CompanySetting::updateOrCreate(['setting_key' => "media_file_{$newKey}"], ['setting_value' => $storedPath]);
        CompanySetting::updateOrCreate(['setting_key' => "media_caption_{$newKey}"], ['setting_value' => $caption]);
        CompanySetting::updateOrCreate(['setting_key' => "media_updated_at_{$newKey}"], ['setting_value' => now()->toDateString()]);

        $extraKeys[] = $newKey;
        CompanySetting::updateOrCreate(
            ['setting_key' => 'hero_extra_keys'],
            ['setting_value' => json_encode(array_values($extraKeys))]
        );

        ActivityLogger::log('Tambah Slide Banner', "Menambahkan slide banner beranda baru [{$newKey}] ({$caption}).");

        return back()->with('success', "Slide banner baru (Slide #{$slideNumber}) berhasil ditambahkan ke slider beranda.");
    }

    public function updateMedia(Request $request, string $key)
    {
        $this->ensureValidMediaKey($key);

        $validated = $request->validate([
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'caption' => 'nullable|string|max:180',
            'updated_at' => 'nullable|date',
        ]);

        if ($request->hasFile('foto')) {
            $oldPath = CompanySetting::getVal("media_file_{$key}");
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $storedPath = $request->file('foto')->store('pabrik-media', 'public');
            CompanySetting::updateOrCreate(
                ['setting_key' => "media_file_{$key}"],
                ['setting_value' => $storedPath]
            );

            $dateVal = !empty($validated['updated_at']) ? $validated['updated_at'] : now()->toDateString();
            CompanySetting::updateOrCreate(
                ['setting_key' => "media_updated_at_{$key}"],
                ['setting_value' => $dateVal]
            );
        } elseif (!empty($validated['updated_at'])) {
            CompanySetting::updateOrCreate(
                ['setting_key' => "media_updated_at_{$key}"],
                ['setting_value' => $validated['updated_at']]
            );
        }

        if (array_key_exists('caption', $validated) && trim((string) $validated['caption']) !== '') {
            CompanySetting::updateOrCreate(
                ['setting_key' => "media_caption_{$key}"],
                ['setting_value' => trim((string) $validated['caption'])]
            );
        }

        ActivityLogger::log('Ubah Media Pabrik', "Memperbarui foto/keterangan media situs slot [{$key}].");

        return back()->with('success', "Media galeri [{$key}] berhasil diperbarui.");
    }

    public function resetMedia(Request $request, string $key)
    {
        $this->ensureValidMediaKey($key);

        $oldPath = CompanySetting::getVal("media_file_{$key}");
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        CompanySetting::whereIn('setting_key', [
            "media_file_{$key}",
            "media_caption_{$key}",
            "media_updated_at_{$key}",
        ])->delete();

        if (PabrikConfigService::isExtraHeroKey($key)) {
            $remaining = array_values(array_filter(
                PabrikConfigService::getExtraHeroKeys(),
                fn ($k) => $k !== $key
            ));

            CompanySetting::updateOrCreate(
                ['setting_key' => 'hero_extra_keys'],
                ['setting_value' => json_encode($remaining)]
            );

            ActivityLogger::log('Hapus Slide Banner', "Menghapus slide banner tambahan [{$key}] dari beranda.");

            return back()->with('success', "Slide banner tambahan [{$key}] berhasil dihapus dari slider beranda.");
        }

        ActivityLogger::log('Reset Media Pabrik', "Mengembalikan media situs slot [{$key}] ke foto bawaan.");

        return back()->with('success', "Media galeri [{$key}] dikembalikan ke gambar bawaan.");
    }

    private function ensureValidMediaKey(string $key): void
    {
        $validKeys = array_merge(
            PabrikConfigService::getAllHeroKeys(),
            [PabrikConfigService::LOGIN_BANNER_KEY],
            array_keys(config('pabrik.fasilitas_images', []))
        );

        abort_unless(in_array($key, $validKeys, true), 404);
    }
}
