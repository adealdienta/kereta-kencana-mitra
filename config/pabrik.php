<?php

return [
    'wa_pabrik' => env('WA_PABRIK', '6285790627104') ?: '6285790627104',
    'nppbkc' => env('NPPBKC', '750******-070700-221221*******') ?: '750******-070700-221221*******',
    'jam_operasional' => env('JAM_OPERASIONAL', 'Buka: 09.00 - 16.00 WIB (Senin - Sabtu)') ?: 'Buka: 09.00 - 16.00 WIB (Senin - Sabtu)',
    'bank_nama' => env('BANK_NAMA') ?: null,
    'bank_rekening' => env('BANK_REKENING') ?: null,
    'bank_atas_nama' => env('BANK_ATAS_NAMA') ?: null,

    // Ubah tanggal penggantian gambar fasilitas produksi ('YYYY-MM-DD') di sini setiap kali foto diganti.
    'fasilitas_images' => [
        'pabrik-beranda' => [
            'file' => 'assets/img/pabrik-beranda.jpg',
            'alt' => 'Gedung Pabrik PR. Kereta Kencana',
            'caption' => 'Fasilitas Pabrik PR. KERETA KENCANA • Ponggok, Blitar',
            'updated_at' => '2026-11-20',
        ],
        'proses-bahan-baku' => [
            'file' => 'assets/img/proses-bahan-baku.jpg',
            'alt' => 'Bahan Baku Tembakau dan Cengkeh',
            'caption' => 'Gudang Bahan Baku Tembakau & Cengkeh',
            'updated_at' => '2026-11-20',
        ],
        'proses-linting-skt' => [
            'file' => 'assets/img/proses-linting-skt.jpg',
            'alt' => 'Lini Linting Tangan SKT & Ketentuan Pita Cukai',
            'icon' => 'fa-solid fa-hands-holding-circle',
            'caption' => 'Ketentuan Pita Cukai Resmi SKT (KPPBC Blitar)',
            'updated_at' => '2026-11-20',
        ],
        'proses-gudang-distribusi' => [
            'file' => 'assets/img/proses-gudang-distribusi.jpg',
            'alt' => 'Kemasan Resmi PR. Kereta Kencana Siap Distribusi',
            'icon' => 'fa-solid fa-boxes-stacked',
            'caption' => 'Kemasan Produk Resmi Siap Distribusi',
            'updated_at' => '2026-11-20',
        ],
        'pabrik-ponggok' => [
            'file' => 'assets/img/pabrik-ponggok.jpg',
            'alt' => 'Pabrik PR. Kereta Kencana Ponggok',
            'caption' => 'Pabrik PR. KERETA KENCANA • Ponggok, Kab. Blitar',
            'updated_at' => '2026-11-20',
        ],
        'fasilitas-tembakau' => [
            'file' => 'assets/img/fasilitas-tembakau.jpg',
            'alt' => 'Gudang Bahan Baku & Racikan Tembakau',
            'caption' => 'Gudang Penyimpanan Tembakau & Cengkeh',
            'updated_at' => '2026-11-20',
        ],
        'galeri-produksi-skt' => [
            'file' => 'assets/img/galeri-produksi-skt.jpg',
            'alt' => 'Ruang Produksi Linting SKT',
            'caption' => 'Aktivitas Lini Produksi SKT',
            'updated_at' => '2026-11-20',
        ],
        'fasilitas-cukai' => [
            'file' => 'assets/img/fasilitas-cukai.jpg',
            'alt' => 'Pengawasan & Edukasi Pita Cukai Resmi',
            'caption' => 'Standar Pita Cukai Resmi & Kemasan Legal',
            'updated_at' => '2026-11-20',
        ],
        'fasilitas-gudang' => [
            'file' => 'assets/img/fasilitas-gudang.jpg',
            'alt' => 'Stok Produk Jadi Siap Kirim',
            'caption' => 'Kesiapan Stok Produk & Distribusi Mitra',
            'updated_at' => '2026-11-20',
        ],
    ],
];
