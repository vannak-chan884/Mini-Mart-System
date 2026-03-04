<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Mini Mart') }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;600&family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #0D0D14;
            --surface: #13131F;
            --panel: #16162A;
            --blue: #003087;
            --blue-mid: #1a4db3;
            --red: #CC0001;
            --gold: #F4A900;
            --green: #16A34A;
            --muted: #6B7280;
            --muted-lt: #9CA3AF;
            --text: #E8E4DC;
            --glass: rgba(255, 255, 255, 0.04);
            --border: rgba(255, 255, 255, 0.07);
            --sidebar-w: 240px;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--ink);
            color: var(--text);
        }

        /* Noise overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
            background-size: 200px;
            pointer-events: none;
            z-index: 0;
        }

        /* ─── LAYOUT SHELL ─────────────────────────────── */
        .app-shell {
            display: flex;
            height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* ─── SIDEBAR ──────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            z-index: 50;
            position: relative;
        }

        /* Sidebar ambient glow */
        .sidebar::after {
            content: '';
            position: absolute;
            top: 0;
            right: -1px;
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg,
                    transparent 0%,
                    rgba(0, 48, 135, 0.4) 30%,
                    rgba(0, 48, 135, 0.4) 70%,
                    transparent 100%);
            pointer-events: none;
        }

        /* Logo area */
        .sidebar-logo {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 11px;
            text-decoration: none;
            flex-shrink: 0;
        }

        .sidebar-logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--blue), var(--blue-mid));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            box-shadow: 0 0 16px rgba(0, 48, 135, 0.5);
            flex-shrink: 0;
        }

        .sidebar-logo-text {
            overflow: hidden;
        }

        .sidebar-logo-name {
            font-family: 'Playfair Display', serif;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            white-space: nowrap;
        }

        .sidebar-logo-sub {
            font-family: 'Noto Sans Khmer', sans-serif;
            font-size: 9px;
            color: var(--muted);
            font-weight: 300;
            display: block;
            margin-top: -1px;
        }

        /* Nav section */
        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 2px;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--muted);
            padding: 12px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--muted-lt);
            transition: all 0.18s ease;
            position: relative;
        }

        .nav-item:hover {
            background: var(--glass);
            color: #fff;
        }

        .nav-item.active {
            background: rgba(0, 48, 135, 0.25);
            color: #fff;
            border: 1px solid rgba(0, 48, 135, 0.35);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: linear-gradient(180deg, var(--blue-mid), #4a90d9);
            border-radius: 0 2px 2px 0;
        }

        .nav-icon {
            font-size: 16px;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .nav-badge {
            margin-left: auto;
            background: rgba(204, 0, 1, 0.2);
            border: 1px solid rgba(204, 0, 1, 0.35);
            color: #FCA5A5;
            font-size: 10px;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 999px;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--glass);
            border: 1px solid var(--border);
            cursor: pointer;
            position: relative;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--blue), var(--blue-mid));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            overflow: hidden;
            min-width: 0;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 10px;
            color: var(--muted);
            font-weight: 300;
        }

        .user-chevron {
            color: var(--muted);
            font-size: 12px;
            flex-shrink: 0;
        }

        /* User dropdown */
        .user-dropdown {
            position: absolute;
            bottom: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #1C1C2E;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 6px;
            display: none;
            z-index: 100;
            box-shadow: 0 -8px 32px rgba(0, 0, 0, 0.4);
        }

        .user-dropdown.open {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 13px;
            color: var(--muted-lt);
            text-decoration: none;
            transition: all 0.15s;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            font-family: 'DM Sans', sans-serif;
        }

        .dropdown-item:hover {
            background: var(--glass);
            color: #fff;
        }

        .dropdown-item.danger:hover {
            background: rgba(204, 0, 1, 0.1);
            color: #FCA5A5;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }

        /* ─── MAIN AREA ────────────────────────────────── */
        .main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-width: 0;
        }

        /* Topbar */
        .topbar {
            height: 60px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            border-bottom: 1px solid var(--border);
            background: rgba(13, 13, 20, 0.8);
            backdrop-filter: blur(12px);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--muted-lt);
            font-size: 20px;
            cursor: pointer;
            padding: 6px;
            border-radius: 8px;
            transition: all 0.15s;
        }

        .menu-toggle:hover {
            background: var(--glass);
            color: #fff;
        }

        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .page-breadcrumb {
            font-size: 12px;
            color: var(--muted);
            font-weight: 300;
            margin-top: 1px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Topbar icon buttons */
        .topbar-btn {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            background: var(--glass);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.18s;
            text-decoration: none;
            color: var(--muted-lt);
            position: relative;
        }

        .topbar-btn:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.14);
        }

        .topbar-btn .notif-dot {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 7px;
            height: 7px;
            background: var(--red);
            border-radius: 50%;
            border: 2px solid var(--ink);
        }

        /* Date/time pill */
        .topbar-time {
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 9px;
            padding: 0 14px;
            height: 36px;
            font-family: 'IBM Plex Mono', monospace;
            font-size: 12px;
            color: var(--muted-lt);
        }

        .topbar-time .sep {
            color: var(--border);
        }

        /* Cambodian flag stripe accent in topbar */
        .topbar-flag {
            display: flex;
            height: 16px;
            width: 3px;
            border-radius: 2px;
            overflow: hidden;
            flex-direction: column;
            gap: 1px;
        }

        .topbar-flag span:nth-child(1),
        .topbar-flag span:nth-child(3) {
            background: var(--red);
            flex: 1;
        }

        .topbar-flag span:nth-child(2) {
            background: #4a90d9;
            flex: 2;
        }

        /* ─── CONTENT ──────────────────────────────────── */
        .content-area {
            flex: 1;
            overflow-y: auto;
            padding: 28px;
        }

        .content-area::-webkit-scrollbar {
            width: 6px;
        }

        .content-area::-webkit-scrollbar-track {
            background: transparent;
        }

        .content-area::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }

        /* ─── MOBILE OVERLAY ───────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 40;
        }

        /* ─── MOBILE ───────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0, 0, 0, 0.5);
            }

            .sidebar-overlay.open {
                display: block;
            }

            .menu-toggle {
                display: flex;
            }

            .content-area {
                padding: 20px 16px;
            }

            .topbar {
                padding: 0 16px;
            }

            .topbar-time {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <div class="app-shell">

        {{-- ── SIDEBAR ────────────────────────────────────────── --}}
        <aside class="sidebar" id="sidebar">

            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="sidebar-logo">
                <div class="sidebar-logo-icon">🏪</div>
                <div class="sidebar-logo-text">
                    <div class="sidebar-logo-name">{{ config('app.name', 'Mini Mart') }}</div>
                    <span class="sidebar-logo-sub">ហាងលក់គ្រឿងទំនិញ</span>
                </div>
            </a>

            {{-- Navigation --}}
            <nav class="sidebar-nav">
                <div class="nav-section-label">Main</div>

                <a href="{{ route('dashboard') }}"
                    class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span>
                    Dashboard
                </a>

                <a href="{{ route('admin.pos.index') }}"
                    class="nav-item {{ request()->routeIs('admin.pos.*') ? 'active' : '' }}">
                    <span class="nav-icon">🖥️</span>
                    POS Terminal
                </a>

                <div class="nav-section-label">Inventory</div>

                <a href="{{ route('admin.products.index') }}"
                    class="nav-item {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <span class="nav-icon">📦</span>
                    Products
                </a>

                <a href="{{ route('admin.categories.index') }}"
                    class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <span class="nav-icon">🗂️</span>
                    Categories
                </a>

                <div class="nav-section-label">Reports</div>

                @if (Route::has('admin.sales.index'))
                    <a href="{{ route('admin.sales.index') }}"
                        class="nav-item {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
                        <span class="nav-icon">🧾</span>
                        Sales History
                    </a>
                @endif

            </nav>

            {{-- User footer --}}
            <div class="sidebar-footer">
                <div class="sidebar-user" onclick="toggleUserMenu()" id="userTrigger">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                    <span class="user-chevron">⌃</span>

                    {{-- Dropdown --}}
                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ route('profile.edit') }}" class="dropdown-item">
                            👤 Profile Settings
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item danger">
                                ↩ Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </aside>

        {{-- Mobile overlay --}}
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        {{-- ── MAIN AREA ──────────────────────────────────────── --}}
        <div class="main-area">

            {{-- Topbar --}}
            <header class="topbar">
                <div class="topbar-left">
                    <button class="menu-toggle" onclick="openSidebar()">☰</button>
                    <div>
                        <div class="page-title">@yield('title', 'Dashboard')</div>
                        <div class="page-breadcrumb">Mini Mart POS · Cambodia 🇰🇭</div>
                    </div>
                </div>

                <div class="topbar-right">
                    {{-- Cambodian flag accent --}}
                    <div class="topbar-flag">
                        <span></span><span></span><span></span>
                    </div>

                    {{-- Clock --}}
                    <div class="topbar-time">
                        <span id="clock-date"></span>
                        <span class="sep">|</span>
                        <span id="clock-time"></span>
                    </div>

                    {{-- POS quick link --}}
                    <a href="{{ route('admin.pos.index') }}" class="topbar-btn" title="Open POS">🖥️</a>

                    {{-- Profile --}}
                    <a href="{{ route('profile.edit') }}" class="topbar-btn" title="Profile">👤</a>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="content-area">
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            const dateOpts = {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                timeZone: 'Asia/Phnom_Penh'
            };
            const timeOpts = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZone: 'Asia/Phnom_Penh'
            };
            const d = document.getElementById('clock-date');
            const t = document.getElementById('clock-time');
            if (d) d.textContent = now.toLocaleDateString('en-GB', dateOpts);
            if (t) t.textContent = now.toLocaleTimeString('en-US', timeOpts);
        }
        updateClock();
        setInterval(updateClock, 1000);

        // User dropdown
        function toggleUserMenu() {
            const dd = document.getElementById('userDropdown');
            dd.classList.toggle('open');
        }
        document.addEventListener('click', function(e) {
            const trigger = document.getElementById('userTrigger');
            const dd = document.getElementById('userDropdown');
            if (dd && trigger && !trigger.contains(e.target)) {
                dd.classList.remove('open');
            }
        });

        // Mobile sidebar
        function openSidebar() {
            document.getElementById('sidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }
    </script>

    @stack('scripts')
</body>

</html>
