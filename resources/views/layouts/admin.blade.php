<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Panel Manajemen PR. KERETA KENCANA</title>
    <link rel="icon" type="image/png" sizes="256x256" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}?v={{ filemtime(public_path('favicon.png')) }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}?v=mobile2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @stack('styles')
</head>
<body>

<div class="admin-wrapper">
    <!-- Sidebar Backdrop for Mobile Drawer -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-content">
                <img src="{{ asset('assets/img/logo-resmi.png') }}?v={{ filemtime(public_path('assets/img/logo-resmi.png')) }}" alt="PR. Kereta Kencana" style="height: 40px; width: auto; object-fit: contain;">
                <div>
                    <strong style="letter-spacing: 1px;">KERETA KENCANA</strong>
                    <div style="font-size: 11px; color: var(--admin-muted);">Operasional & Distribusi</div>
                </div>
            </div>
            <button type="button" class="sidebar-close-btn" id="sidebarCloseBtn" aria-label="Tutup Menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fa-solid fa-user-tie"></i>
            </div>
            <div class="user-info">
                <span class="user-name">{{ auth()->user()->name }}</span>
                <span class="role-badge role-{{ auth()->user()->role }}">
                    {{ strtoupper(auth()->user()->role) }}
                </span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">MENU UTAMA</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
            <a href="{{ route('admin.transaksis.index') }}" class="sidebar-link {{ request()->routeIs('admin.transaksis.*') ? 'active' : '' }}">
                <i class="fa-solid fa-cart-flatbed"></i> Pesanan Distributor
            </a>
            <a href="{{ route('admin.mitra.index') }}" class="sidebar-link {{ request()->routeIs('admin.mitra.*') ? 'active' : '' }}">
                <i class="fa-solid fa-store"></i> Daftar Mitra Toko
            </a>

            <div class="nav-section-title">KATALOG & INVENTARIS</div>
            <a href="{{ route('admin.barangs.index') }}" class="sidebar-link {{ request()->routeIs('admin.barangs.*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked"></i> Produk Rokok
            </a>
            <a href="{{ route('admin.kategoris.index') }}" class="sidebar-link {{ request()->routeIs('admin.kategoris.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i> Kategori Sigaret (SKT)
            </a>

            <div class="nav-section-title">PERIZINAN & SISTEM</div>
            <a href="{{ route('admin.legalitas.index') }}" class="sidebar-link {{ request()->routeIs('admin.legalitas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-stamp"></i> Legalitas Cukai
            </a>
            <a href="{{ route('admin.pengaturan.index') }}" class="sidebar-link {{ request()->routeIs('admin.pengaturan.*') ? 'active' : '' }}">
                <i class="fa-solid fa-sliders"></i> Pengaturan &amp; Media
            </a>

            @if(auth()->user()->isOwner() || auth()->user()->isSuperAdmin())
                <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-users-gear"></i> Kelola Akun Admin
                </a>
                <a href="{{ route('admin.logs.index') }}" class="sidebar-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Log Audit Trail
                </a>
            @endif

            <div class="nav-section-title">AKUN SAYA</div>
            <a href="{{ route('admin.profile') }}" class="sidebar-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}">
                <i class="fa-solid fa-user-pen"></i> Profil Akun
            </a>
            <a href="{{ route('beranda') }}" target="_blank" class="sidebar-link">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Website Publik
            </a>

            <form action="{{ route('logout') }}" method="POST" style="margin-top: 15px; padding: 0 16px;">
                @csrf
                <button type="submit" class="btn-logout" style="width: 100%; border: none; background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <i class="fa-solid fa-right-from-bracket"></i> Keluar
                </button>
            </form>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Topbar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <button type="button" class="admin-sidebar-toggle" id="adminSidebarToggle" aria-label="Buka Menu Navigasi">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="topbar-title">
                    <h2>@yield('header_title', 'Dashboard Operasional')</h2>
                    <small style="color: var(--admin-muted);">PR. KERETA KENCANA Ponggok, Kabupaten Blitar</small>
                </div>
            </div>
            <div class="topbar-actions">
                <span class="badge-role">{{ auth()->user()->name }} ({{ strtoupper(auth()->user()->role) }})</span>
            </div>
        </header>

        <!-- Body Content -->
        <main class="admin-content">
            @if(session('success'))
                <div class="alert alert-success" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning" style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-circle-info"></i> {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" style="background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;">
                    <strong><i class="fa-solid fa-triangle-exclamation"></i> Terjadi Kesalahan Validasi:</strong>
                    <ul style="margin-top: 6px; padding-left: 20px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('adminSidebarToggle');
        const closeBtn = document.getElementById('sidebarCloseBtn');
        const backdrop = document.getElementById('sidebarBackdrop');

        function openSidebar() {
            if (sidebar) sidebar.classList.add('show');
            if (backdrop) backdrop.classList.add('show');
            document.body.classList.add('sidebar-open');
        }

        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('show');
            if (backdrop) backdrop.classList.remove('show');
            document.body.classList.remove('sidebar-open');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                openSidebar();
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebar();
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSidebar();
            }
        });

        // Close when clicking nav links on mobile devices
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        sidebarLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 992) {
                    closeSidebar();
                }
            });
        });
    });
</script>

@stack('scripts')
</body>
</html>
