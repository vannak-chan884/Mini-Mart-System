<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Mini Mart') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;600&family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Font family utilities */
        body { font-family: 'DM Sans', sans-serif; }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .font-mono-ibm  { font-family: 'IBM Plex Mono', monospace; }
        .font-khmer     { font-family: 'Noto Sans Khmer', sans-serif; }

        /* Noise texture */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
            background-size: 200px;
            pointer-events: none;
            z-index: 0;
        }

        /* Sidebar right-edge glow — not replicable in Tailwind */
        .sidebar-glow::after {
            content: '';
            position: absolute;
            top: 0; right: -1px;
            width: 1px; height: 100%;
            background: linear-gradient(180deg, transparent 0%, rgba(0,48,135,0.4) 30%, rgba(0,48,135,0.4) 70%, transparent 100%);
            pointer-events: none;
        }

        /* Active nav left bar indicator */
        .nav-active-bar::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: linear-gradient(180deg, #1a4db3, #4a90d9);
            border-radius: 0 2px 2px 0;
        }

        /* ── Light mode overrides ─────────────────────────── */
        .light body,
        html.light body          { background-color: #F0F2F8 !important; color: #1A1A2E !important; }
        html.light .bg-\[#0D0D14\] { background-color: #F0F2F8 !important; }
        html.light .bg-\[#13131F\] { background-color: #FFFFFF !important; }
        html.light .bg-\[#1C1C2E\] { background-color: #F4F6FB !important; }
        html.light .border-white\/\[0\.07\] { border-color: rgba(0,0,0,0.08) !important; }
        html.light .text-\[#E8E4DC\] { color: #1A1A2E !important; }
        html.light .text-white      { color: #0D0D2B !important; }
        html.light .text-\[#9CA3AF\] { color: #6B7280 !important; }
        html.light .text-\[#6B7280\] { color: #9CA3AF !important; }
        html.light .bg-white\/\[0\.04\]  { background-color: rgba(0,0,0,0.04) !important; }
        html.light .bg-white\/\[0\.07\]  { background-color: rgba(0,0,0,0.06) !important; }
        html.light .bg-white\/\[0\.08\]  { background-color: rgba(0,0,0,0.08) !important; }
        html.light .hover\:bg-white\/\[0\.04\]:hover { background-color: rgba(0,0,0,0.05) !important; }
        html.light .hover\:bg-white\/\[0\.08\]:hover { background-color: rgba(0,0,0,0.09) !important; }
        html.light .bg-\[rgba\(13\,13\,20\,0\.8\)\] { background-color: rgba(255,255,255,0.85) !important; }
        html.light .text-white\/\[0\.07\] { color: rgba(0,0,0,0.15) !important; }
        html.light .sidebar-glow::after { background: linear-gradient(180deg, transparent 0%, rgba(0,48,135,0.15) 30%, rgba(0,48,135,0.15) 70%, transparent 100%); }
        html.light body::before { opacity: 0.01; }

        /* Scrollbars */
        .scrollbar-sidebar::-webkit-scrollbar { width: 4px; }
        .scrollbar-sidebar::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 2px; }

        .scrollbar-content::-webkit-scrollbar { width: 6px; }
        .scrollbar-content::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.07); border-radius: 3px; }

        /* ── LIGHT MODE ─────────────────────────────────── */
        html.light body { background: #F0F2F8; color: #1a1a2e; }

        /* Noise lighter in light mode */
        html.light body::before { opacity: 0.01; }

        /* Sidebar */
        html.light aside#sidebar {
            background: #FFFFFF;
            border-right-color: rgba(0,0,0,0.08);
        }
        html.light .sidebar-glow::after { opacity: 0; }

        /* Sidebar logo area */
        html.light aside#sidebar a[href] {
            border-bottom-color: rgba(0,0,0,0.07);
        }
        html.light .font-playfair.text-white { color: #0D0D14 !important; }
        html.light .font-khmer { color: #9CA3AF !important; }

        /* Nav items */
        html.light nav .text-\[#6B7280\] { color: #9CA3AF; }
        html.light nav a.text-\[#9CA3AF\] { color: #6B7280; }
        html.light nav a:hover { background: rgba(0,48,135,0.06) !important; color: #003087 !important; }
        html.light nav a.bg-\[rgba\(0\,48\,135\,0\.25\)\] {
            background: rgba(0,48,135,0.1) !important;
            color: #003087 !important;
            border-color: rgba(0,48,135,0.2) !important;
        }

        /* User footer */
        html.light #userTrigger {
            background: rgba(0,0,0,0.03);
            border-color: rgba(0,0,0,0.08);
        }
        html.light #userTrigger:hover { background: rgba(0,0,0,0.06) !important; }
        html.light .text-white.truncate { color: #0D0D14 !important; }
        html.light #userDropdown {
            background: #FFFFFF;
            border-color: rgba(0,0,0,0.08);
            box-shadow: 0 -8px 32px rgba(0,0,0,0.12);
        }
        html.light #userDropdown a,
        html.light #userDropdown button { color: #6B7280; }
        html.light #userDropdown a:hover { background: rgba(0,48,135,0.06) !important; color: #003087 !important; }

        /* Topbar */
        html.light header {
            background: rgba(240,242,248,0.9) !important;
            border-bottom-color: rgba(0,0,0,0.08);
        }
        html.light header .font-playfair { color: #0D0D14 !important; }
        html.light header .text-\[#6B7280\] { color: #9CA3AF !important; }

        /* Topbar buttons */
        html.light header a.bg-white\/\[0\.04\],
        html.light header div.bg-white\/\[0\.04\] {
            background: rgba(0,0,0,0.04) !important;
            border-color: rgba(0,0,0,0.08) !important;
            color: #6B7280 !important;
        }
        html.light header a:hover {
            background: rgba(0,0,0,0.08) !important;
            color: #0D0D14 !important;
        }
        html.light #clock-date, html.light #clock-time { color: #6B7280; }

        /* Smooth theme transition */
        body, aside, header, nav a, #userTrigger, #userDropdown {
            transition: background 0.25s ease, color 0.25s ease, border-color 0.25s ease;
        }

        /* Theme toggle button styles */
        #themeToggle {
            position: relative;
            overflow: hidden;
        }
        #themeToggle .icon-dark,
        #themeToggle .icon-light {
            transition: transform 0.3s ease, opacity 0.3s ease;
            position: absolute;
        }
        html.light #themeToggle .icon-dark  { transform: translateY(-120%); opacity: 0; }
        html.light #themeToggle .icon-light { transform: translateY(0);    opacity: 1; }
        html:not(.light) #themeToggle .icon-dark  { transform: translateY(0);    opacity: 1; }
        html:not(.light) #themeToggle .icon-light { transform: translateY(120%); opacity: 0; }
    </style>
    @stack('styles')
</head>

<body class="bg-[#0D0D14] text-[#E8E4DC] h-screen overflow-hidden">

<div class="flex h-screen relative z-10">

    {{-- ══ SIDEBAR ══════════════════════════════════════════ --}}
    <aside id="sidebar"
           class="sidebar-glow
                  w-60 flex-shrink-0
                  bg-[#13131F] border-r border-white/[0.07]
                  flex flex-col
                  transition-transform duration-300
                  z-50 relative
                  max-md:fixed max-md:inset-y-0 max-md:left-0 max-md:-translate-x-full">

        {{-- Logo --}}
        <a href="{{ route('admin.dashboard.index') }}"
           class="flex items-center gap-[11px] px-5 py-6
                  border-b border-white/[0.07] no-underline flex-shrink-0">
            <div class="w-9 h-9 rounded-[10px] flex items-center justify-center text-[17px] flex-shrink-0
                        bg-gradient-to-br from-[#003087] to-[#1a4db3]
                        shadow-[0_0_16px_rgba(0,48,135,0.5)]">
                🏪
            </div>
            <div class="overflow-hidden">
                <div class="font-playfair text-base font-bold text-white whitespace-nowrap">
                    {{ config('app.name', 'Mini Mart') }}
                </div>
                <span class="font-khmer text-[9px] text-[#6B7280] font-light block -mt-px">
                    ហាងលក់គ្រឿងទំនិញ
                </span>
            </div>
        </a>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 overflow-y-auto scrollbar-sidebar flex flex-col gap-0.5">

            <div class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] px-3 pt-3 pb-1.5">
                Main
            </div>

            <a href="{{ route('admin.dashboard.index') }}"
               class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                      text-[13.5px] font-medium transition-all duration-[180ms] relative
                      {{ request()->routeIs('admin.dashboard.*')
                         ? 'bg-[rgba(0,48,135,0.25)] text-white border border-[rgba(0,48,135,0.35)]'
                         : 'text-[#9CA3AF] hover:bg-white/[0.04] hover:text-white border border-transparent' }}">
                <span class="text-base w-5 text-center flex-shrink-0">📊</span>
                Dashboard
            </a>

            <a href="{{ route('admin.pos.index') }}"
               class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                      text-[13.5px] font-medium transition-all duration-[180ms] relative
                      {{ request()->routeIs('admin.pos.*')
                         ? 'bg-[rgba(0,48,135,0.25)] text-white border border-[rgba(0,48,135,0.35)]'
                         : 'text-[#9CA3AF] hover:bg-white/[0.04] hover:text-white border border-transparent' }}">
                <span class="text-base w-5 text-center flex-shrink-0">🖥️</span>
                POS Terminal
            </a>

            <div class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] px-3 pt-4 pb-1.5">
                Inventory
            </div>

            <a href="{{ route('admin.products.index') }}"
               class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                      text-[13.5px] font-medium transition-all duration-[180ms] relative
                      {{ request()->routeIs('admin.products.*')
                         ? 'bg-[rgba(0,48,135,0.25)] text-white border border-[rgba(0,48,135,0.35)]'
                         : 'text-[#9CA3AF] hover:bg-white/[0.04] hover:text-white border border-transparent' }}">
                <span class="text-base w-5 text-center flex-shrink-0">📦</span>
                Products
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                      text-[13.5px] font-medium transition-all duration-[180ms] relative
                      {{ request()->routeIs('admin.categories.*')
                         ? 'bg-[rgba(0,48,135,0.25)] text-white border border-[rgba(0,48,135,0.35)]'
                         : 'text-[#9CA3AF] hover:bg-white/[0.04] hover:text-white border border-transparent' }}">
                <span class="text-base w-5 text-center flex-shrink-0">🗂️</span>
                Categories
            </a>

            <div class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] px-3 pt-4 pb-1.5">
                Reports
            </div>

            @if(Route::has('admin.sales.index'))
            <a href="{{ route('admin.sales.index') }}"
               class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                      text-[13.5px] font-medium transition-all duration-[180ms] relative
                      {{ request()->routeIs('admin.sales.*')
                         ? 'bg-[rgba(0,48,135,0.25)] text-white border border-[rgba(0,48,135,0.35)]'
                         : 'text-[#9CA3AF] hover:bg-white/[0.04] hover:text-white border border-transparent' }}">
                <span class="text-base w-5 text-center flex-shrink-0">🧾</span>
                Sales History
            </a>
            @endif

        </nav>

        {{-- User Footer --}}
        <div class="px-3 py-3.5 border-t border-white/[0.07] flex-shrink-0">
            <div id="userTrigger" onclick="toggleUserMenu()"
                 class="flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] cursor-pointer relative
                        bg-white/[0.04] border border-white/[0.07]
                        hover:bg-white/[0.07] transition-all duration-150 select-none">

                {{-- Avatar --}}
                <div class="w-8 h-8 rounded-[9px] flex items-center justify-center text-[13px] font-bold
                            text-white flex-shrink-0 bg-gradient-to-br from-[#003087] to-[#1a4db3]">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <div class="flex-1 overflow-hidden min-w-0">
                    <div class="text-[13px] font-semibold text-white truncate leading-tight">
                        {{ Auth::user()->name }}
                    </div>
                    <div class="text-[10px] text-[#6B7280] font-light">Administrator</div>
                </div>

                <span class="text-[#6B7280] text-xs flex-shrink-0 leading-none">⌃</span>

                {{-- Dropdown --}}
                <div id="userDropdown"
                     class="hidden absolute bottom-[calc(100%+8px)] left-0 right-0 z-[100]
                            bg-[#1C1C2E] border border-white/[0.07] rounded-xl p-1.5
                            shadow-[0_-8px_32px_rgba(0,0,0,0.4)]">

                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[13px]
                              text-[#9CA3AF] no-underline transition-all duration-150
                              hover:bg-white/[0.04] hover:text-white">
                        👤 Profile Settings
                    </a>

                    <div class="h-px bg-white/[0.07] my-1.5"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg text-[13px]
                                       font-[inherit] text-[#9CA3AF] bg-transparent border-0 text-left cursor-pointer
                                       transition-all duration-150
                                       hover:bg-[rgba(204,0,1,0.1)] hover:text-[#FCA5A5]">
                            ↩ Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </aside>

    {{-- Mobile overlay --}}
    <div id="sidebarOverlay" onclick="closeSidebar()"
         class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-40"></div>

    {{-- ══ MAIN AREA ════════════════════════════════════════ --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Topbar --}}
        <header class="h-[60px] flex-shrink-0 flex items-center justify-between
                       px-7 max-md:px-4
                       border-b border-white/[0.07]
                       bg-[rgba(13,13,20,0.8)] backdrop-blur-xl
                       sticky top-0 z-20">

            <div class="flex items-center gap-3.5">
                {{-- Mobile menu toggle --}}
                <button onclick="openSidebar()"
                        class="hidden max-md:flex items-center justify-center w-9 h-9
                               rounded-lg bg-transparent border-0 text-[#9CA3AF] text-xl
                               cursor-pointer transition-all duration-150
                               hover:bg-white/[0.04] hover:text-white">
                    ☰
                </button>

                <div>
                    <div class="font-playfair text-xl font-bold text-white leading-tight">
                        @yield('title', 'Dashboard')
                    </div>
                    <div class="text-xs text-[#6B7280] font-light mt-0.5">
                        Mini Mart POS · Cambodia 🇰🇭
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5">

                {{-- Cambodian flag stripe --}}
                <div class="flex flex-col h-4 w-[3px] rounded-sm overflow-hidden gap-px flex-shrink-0">
                    <span class="bg-[#CC0001] flex-1"></span>
                    <span class="bg-[#4a90d9]" style="flex:2"></span>
                    <span class="bg-[#CC0001] flex-1"></span>
                </div>

                {{-- Clock --}}
                <div class="hidden md:flex items-center gap-2 h-9 px-3.5
                            rounded-[9px] bg-white/[0.04] border border-white/[0.07]
                            font-mono-ibm text-xs text-[#9CA3AF] whitespace-nowrap">
                    <span id="clock-date"></span>
                    <span class="text-white/[0.07]">|</span>
                    <span id="clock-time"></span>
                </div>

                {{-- Dark / Light mode toggle --}}
                <button id="themeToggle" onclick="toggleTheme()" title="Toggle theme"
                        class="w-9 h-9 rounded-[9px] flex items-center justify-center text-base
                               bg-white/[0.04] border border-white/[0.07] text-[#9CA3AF] cursor-pointer
                               hover:bg-white/[0.08] hover:text-white hover:border-white/[0.14]
                               transition-all duration-[180ms]">
                    <span id="themeIcon">🌙</span>
                </button>

                {{-- POS quick link --}}
                <a href="{{ route('admin.pos.index') }}" title="Open POS"
                   class="w-9 h-9 rounded-[9px] flex items-center justify-center text-base no-underline
                          bg-white/[0.04] border border-white/[0.07] text-[#9CA3AF]
                          hover:bg-white/[0.08] hover:text-white hover:border-white/[0.14]
                          transition-all duration-[180ms]">
                    🖥️
                </a>

                {{-- Profile --}}
                <a href="{{ route('profile.edit') }}" title="Profile"
                   class="w-9 h-9 rounded-[9px] flex items-center justify-center text-base no-underline
                          bg-white/[0.04] border border-white/[0.07] text-[#9CA3AF]
                          hover:bg-white/[0.08] hover:text-white hover:border-white/[0.14]
                          transition-all duration-[180ms]">
                    👤
                </a>

            </div>
        </header>

        {{-- Page Content --}}
        <main class="content-area scrollbar-content flex-1 overflow-y-auto p-7 max-md:p-4 max-md:px-4">
            @yield('content')
        </main>

    </div>
</div>

<script>
    // ── Theme toggle ──────────────────────────────────────
    const THEME_KEY = 'minimart_theme';

    function applyTheme(theme) {
        const html = document.documentElement;
        const icon = document.getElementById('themeIcon');
        if (theme === 'light') {
            html.classList.add('light');
            if (icon) icon.textContent = '☀️';
        } else {
            html.classList.remove('light');
            if (icon) icon.textContent = '🌙';
        }
    }

    function toggleTheme() {
        const current = localStorage.getItem(THEME_KEY) || 'dark';
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem(THEME_KEY, next);
        applyTheme(next);
    }

    // Apply saved theme immediately on load
    applyTheme(localStorage.getItem(THEME_KEY) || 'dark');

    // ── Clock ──────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        const d = document.getElementById('clock-date');
        const t = document.getElementById('clock-time');
        if (d) d.textContent = now.toLocaleDateString('en-GB', { day:'2-digit', month:'short', year:'numeric', timeZone:'Asia/Phnom_Penh' });
        if (t) t.textContent = now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit', hour12:true, timeZone:'Asia/Phnom_Penh' });
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── User dropdown ──────────────────────────────────────
    function toggleUserMenu() {
        document.getElementById('userDropdown').classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        const trigger = document.getElementById('userTrigger');
        const dd = document.getElementById('userDropdown');
        if (dd && trigger && !trigger.contains(e.target)) dd.classList.add('hidden');
    });

    // ── Mobile sidebar ─────────────────────────────────────
    function openSidebar() {
        const s = document.getElementById('sidebar');
        s.classList.remove('-translate-x-full');
        s.classList.add('translate-x-0', 'shadow-[8px_0_32px_rgba(0,0,0,0.5)]');
        document.getElementById('sidebarOverlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        const s = document.getElementById('sidebar');
        s.classList.add('-translate-x-full');
        s.classList.remove('translate-x-0', 'shadow-[8px_0_32px_rgba(0,0,0,0.5)]');
        document.getElementById('sidebarOverlay').classList.add('hidden');
        document.body.style.overflow = '';
    }
</script>

@stack('scripts')
</body>
</html>