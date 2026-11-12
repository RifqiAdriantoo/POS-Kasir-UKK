<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RestoKita') - POS Kasir Restoran</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --primary-light: #ccfbf1;
            --secondary: #64748b;
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --sidebar-active: #0d9488;
            --body-bg: #f8fafc;
            --card-border: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--body-bg);
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .app-sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: #94a3b8;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            text-decoration: none;
        }

        .brand-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, #0d9488 0%, #14b8a6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(13, 148, 136, 0.35);
        }

        .sidebar-menu {
            padding: 1rem 0.75rem;
            list-style: none;
            margin: 0;
            flex-grow: 1;
        }

        .menu-header {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            padding: 0.75rem 0.75rem 0.35rem 0.75rem;
        }

        .nav-item-custom {
            margin-bottom: 4px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.92rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            background-color: var(--sidebar-hover);
            color: #ffffff;
        }

        .nav-link-custom.active {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.3);
        }

        .nav-link-custom i {
            font-size: 1.15rem;
        }

        .sidebar-footer {
            padding: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(0, 0, 0, 0.15);
        }

        /* Main Wrapper */
        .app-main {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Topbar Header */
        .app-topbar {
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
            padding: 0.85rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .modern-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.04);
            transition: all 0.25s ease;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .app-sidebar {
                transform: translateX(-100%);
            }
            .app-sidebar.show {
                transform: translateX(0);
            }
            .app-main {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Sidebar Navigation (Sesuai Mockup PDF Page 5 & 6) -->
    <aside class="app-sidebar" id="appSidebar">
        <div>
            <!-- Brand -->
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <div class="brand-icon-box">
                    <i class="bi bi-shop"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold tracking-tight text-white">RestoKita</h5>
                    <small class="text-teal" style="font-size: 0.72rem; color: #2dd4bf;">UKK Junior Coder 2026</small>
                </div>
            </a>

            <!-- Menu Items -->
            <ul class="sidebar-menu">
                <li class="menu-header">Menu Utama</li>

                <li class="nav-item-custom">
                    <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="nav-item-custom">
                    <a href="{{ route('kasir.index') }}" class="nav-link-custom {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
                        <i class="bi bi-calculator"></i>
                        <span>Kasir (POS)</span>
                    </a>
                </li>

                <li class="nav-item-custom">
                    <a href="{{ route('transaksi.index') }}" class="nav-link-custom {{ request()->routeIs('transaksi.*') ? 'active' : '' }}">
                        <i class="bi bi-receipt"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                </li>

                <li class="menu-header mt-3">Katalog & Menu</li>

                <li class="nav-item-custom">
                    <a href="{{ route('produk.index') }}" class="nav-link-custom {{ request()->routeIs('produk.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Manajemen Menu</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar Footer / User Profile & Logout -->
        <div class="sidebar-footer">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #0d9488;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="text-white fw-bold small text-truncate" style="max-width: 120px;">
                            {{ Auth::check() ? Auth::user()->name : 'Kasir Resto' }}
                        </div>
                        <small class="text-muted" style="font-size: 0.72rem;">Online</small>
                    </div>
                </div>

                @if(Auth::check())
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle p-2" title="Keluar / Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-2 py-1" style="font-size: 0.75rem;">
                    Login
                </a>
                @endif
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="app-main">
        <!-- Topbar -->
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none border shadow-none" type="button" onclick="document.getElementById('appSidebar').classList.toggle('show')">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@yield('page_heading', 'Point of Sale')</h5>
                    <small class="text-muted">@yield('page_subheading', 'Sistem Kasir Operasional Restoran')</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Live Clock -->
                <div class="d-none d-md-flex align-items-center gap-2 bg-light px-3 py-2 rounded-pill border">
                    <i class="bi bi-clock text-primary"></i>
                    <span id="appClock" class="small fw-semibold text-secondary">Loading...</span>
                </div>

                <!-- Quick Button to Kasir -->
                @if(!request()->routeIs('kasir.*'))
                <a href="{{ route('kasir.index') }}" class="btn btn-teal text-white btn-sm rounded-pill px-3 py-2 fw-semibold d-flex align-items-center gap-2" style="background-color: var(--primary);">
                    <i class="bi bi-cart-plus-fill"></i> Buka Kasir
                </a>
                @endif
            </div>
        </header>

        <!-- Body Content -->
        <main class="p-3 p-lg-4 flex-grow-1">

            <!-- Flash Notifications -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm rounded-4 mb-3 d-flex align-items-center gap-2" role="alert">
                <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                <div>{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 px-4 text-center text-muted small">
            RestoKita POS &copy; 2026 • Modul Uji Kompetensi Keahlian (MUK) Pemrogram Junior SMK
        </footer>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Live Clock
        function updateAppClock() {
            const clockEl = document.getElementById('appClock');
            if (clockEl) {
                const now = new Date();
                const options = { 
                    weekday: 'short', 
                    day: 'numeric', 
                    month: 'short', 
                    year: 'numeric', 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit' 
                };
                clockEl.textContent = now.toLocaleDateString('id-ID', options) + ' WIB';
            }
        }
        setInterval(updateAppClock, 1000);
        updateAppClock();
    </script>
    @stack('scripts')
</body>
</html>
