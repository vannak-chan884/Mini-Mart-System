<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Mini Mart') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        .font-playfair {
            font-family: 'Playfair Display', serif;
        }

        .font-mono-ibm {
            font-family: 'IBM Plex Mono', monospace;
        }

        .font-khmer {
            font-family: 'Noto Sans Khmer', sans-serif;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
            background-size: 200px;
            pointer-events: none;
            z-index: 0;
            opacity: 1;
            transition: opacity 0.25s ease;
        }

        html:not(.dark) body::before {
            opacity: 0.01;
        }

        .sidebar-glow::after {
            content: '';
            position: absolute;
            top: 0;
            right: -1px;
            width: 1px;
            height: 100%;
            background: linear-gradient(180deg, transparent 0%, rgba(0, 48, 135, 0.4) 30%, rgba(0, 48, 135, 0.4) 70%, transparent 100%);
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        html:not(.dark) .sidebar-glow::after {
            opacity: 0;
        }

        .nav-active-bar::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            bottom: 20%;
            width: 3px;
            background: linear-gradient(180deg, #1a4db3, #4a90d9);
            border-radius: 0 2px 2px 0;
        }

        #themeToggle {
            position: relative;
            overflow: hidden;
        }

        #themeToggle .icon-dark,
        #themeToggle .icon-light {
            transition: transform 0.3s ease, opacity 0.3s ease;
            position: absolute;
        }

        html:not(.dark) #themeToggle .icon-dark {
            transform: translateY(0);
            opacity: 1;
        }

        html:not(.dark) #themeToggle .icon-light {
            transform: translateY(120%);
            opacity: 0;
        }

        html.dark #themeToggle .icon-dark {
            transform: translateY(-120%);
            opacity: 0;
        }

        html.dark #themeToggle .icon-light {
            transform: translateY(0);
            opacity: 1;
        }

        #sidebar {
            position: relative;
        }

        @media (max-width: 767px) {
            #sidebar {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                z-index: 60;
                transform: translateX(-100%);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            #sidebar.sidebar-open {
                transform: translateX(0);
                box-shadow: 8px 0 32px rgba(0, 0, 0, 0.35);
            }

            #sidebarOverlay {
                display: none;
            }

            #sidebarOverlay.overlay-open {
                display: block;
            }
        }

        .scrollbar-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .scrollbar-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 2px;
        }

        .scrollbar-content::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-content::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-content::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 3px;
        }

        body,
        aside,
        header,
        nav a,
        #userTrigger,
        #userDropdown {
            transition: background 0.25s ease, color 0.25s ease, border-color 0.25s ease;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-[#F0F2F8] dark:bg-[#0D0D14] text-[#1A1A2E] dark:text-[#E8E4DC] h-screen overflow-hidden">

    <div class="flex h-screen relative z-10">

        {{-- ══ SIDEBAR ══════════════════════════════════════════ --}}
        <aside id="sidebar"
            class="sidebar-glow
                   w-60 flex-shrink-0
                   bg-white dark:bg-[#13131F]
                   border-r border-black/[0.08] dark:border-white/[0.07]
                   flex flex-col
                   relative z-50">

            {{-- Logo --}}
            <a href="{{ route('admin.dashboard.index') }}"
                class="flex items-center gap-[11px] px-5 py-6
                       border-b border-black/[0.07] dark:border-white/[0.07]
                       no-underline flex-shrink-0">
                <div
                    class="w-9 h-9 rounded-[10px] flex items-center justify-center text-[17px] flex-shrink-0
                            bg-gradient-to-br from-[#003087] to-[#1a4db3]
                            shadow-[0_0_16px_rgba(0,48,135,0.5)]">
                    🏪
                </div>
                <div class="overflow-hidden">
                    <div class="font-playfair text-base font-bold text-[#0D0D14] dark:text-white whitespace-nowrap">
                        {{ config('app.name', 'Mini Mart') }}
                    </div>
                    <span class="font-khmer text-[9px] text-[#9CA3AF] dark:text-[#6B7280] font-light block -mt-px">
                        ហាងលក់គ្រឿងទំនិញ
                    </span>
                </div>
            </a>

            {{-- Navigation --}}
            <nav class="flex-1 px-3 py-4 overflow-y-auto scrollbar-sidebar flex flex-col gap-0.5">

                {{-- ── MAIN ────────────────────────────────── --}}
                @canDo('dashboard.view', 'pos.view')
                <div
                    class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] dark:text-[#9CA3AF] px-3 pt-3 pb-1.5">
                    Main
                </div>
                @endCanDo

                @canDo('dashboard.view')
                <a href="{{ route('admin.dashboard.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.dashboard.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">📊</span>
                    Dashboard
                </a>
                @endCanDo

                @canDo('pos.view')
                <a href="{{ route('admin.pos.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.pos.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">🖥️</span>
                    POS Terminal
                </a>
                @endCanDo

                {{-- ── INVENTORY ───────────────────────────── --}}
                @canDo('products.view', 'categories.view')
                <div
                    class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] dark:text-[#9CA3AF] px-3 pt-4 pb-1.5">
                    Inventory
                </div>
                @endCanDo

                @canDo('products.view')
                <a href="{{ route('admin.products.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.products.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">📦</span>
                    Products
                </a>
                @endCanDo

                @canDo('categories.view')
                <a href="{{ route('admin.categories.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.categories.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">🗂️</span>
                    Categories
                </a>
                @endCanDo

                {{-- ── EXPENSES ────────────────────────────── --}}
                @canDo('expenses.view', 'expense_categories.view')
                <div
                    class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] dark:text-[#9CA3AF] px-3 pt-4 pb-1.5">
                    Expenses
                </div>
                @endCanDo

                @canDo('expenses.view')
                <a href="{{ route('admin.expenses.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.expenses.index')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">💸</span>
                    Expenses
                </a>
                @endCanDo

                @canDo('expense_categories.view')
                <a href="{{ route('admin.expense-categories.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.expense-categories.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">🗃️</span>
                    Expense Categories
                </a>
                @endCanDo

                {{-- ── REPORTS ─────────────────────────────── --}}
                @canDo('sales.view', 'expenses.view')
                <div
                    class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] dark:text-[#9CA3AF] px-3 pt-4 pb-1.5">
                    Reports
                </div>
                @endCanDo

                @canDo('sales.view')
                @if (Route::has('admin.sales.index'))
                    <a href="{{ route('admin.sales.index') }}"
                        class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.sales.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                        <span class="text-base w-5 text-center flex-shrink-0">🧾</span>
                        Sales History
                    </a>
                @endif
                @endCanDo

                @canDo('expenses.view')
                <a href="{{ route('admin.expenses.monthlyReport') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.expenses.monthlyReport')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">📅</span>
                    Expense History
                </a>
                @endCanDo

                {{-- ── ADMIN (admin role only) ─────────────── --}}
                @canDo('users.view')
                <div
                    class="text-[10px] font-bold tracking-[1.2px] uppercase text-[#6B7280] dark:text-[#9CA3AF] px-3 pt-4 pb-1.5">
                    Admin
                </div>

                @if (Route::has('admin.users.index'))
                    <a href="{{ route('admin.users.index') }}"
                        class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.users.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                        <span class="text-base w-5 text-center flex-shrink-0">👥</span>
                        Users
                    </a>
                @endif

                <a href="{{ route('admin.permissions.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                           text-[13.5px] font-medium transition-all duration-[180ms] relative
                           {{ request()->routeIs('admin.permissions.*')
                               ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                               : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">🔐</span>
                    Permissions
                </a>

                <a href="{{ route('admin.activity-logs.index') }}"
                    class="nav-active-bar flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline
                            text-[13.5px] font-medium transition-all duration-[180ms] relative
                            {{ request()->routeIs('admin.activity-logs.*')
                                ? 'bg-[rgba(0,48,135,0.1)] dark:bg-[rgba(0,48,135,0.25)] text-[#003087] dark:text-white border border-[rgba(0,48,135,0.2)] dark:border-[rgba(0,48,135,0.35)]'
                                : 'text-[#6B7280] dark:text-[#9CA3AF] hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04] hover:text-[#003087] dark:hover:text-white border border-transparent' }}">
                    <span class="text-base w-5 text-center flex-shrink-0">📋</span>
                    Activity Log
                </a>
                @endCanDo

            </nav>

            {{-- User Footer --}}
            <div class="px-3 py-3.5 border-t border-black/[0.08] dark:border-white/[0.07] flex-shrink-0">
                <div id="userTrigger" onclick="toggleUserMenu()"
                    class="flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] cursor-pointer relative
                           bg-black/[0.03] dark:bg-white/[0.04]
                           border border-black/[0.08] dark:border-white/[0.07]
                           hover:bg-black/[0.06] dark:hover:bg-white/[0.07]
                           transition-all duration-150 select-none">

                    <div
                        class="w-8 h-8 rounded-[9px] flex items-center justify-center text-[13px] font-bold
                                text-white flex-shrink-0 bg-gradient-to-br from-[#003087] to-[#1a4db3]">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>

                    <div class="flex-1 overflow-hidden min-w-0">
                        <div class="text-[13px] font-semibold text-[#0D0D14] dark:text-white truncate leading-tight">
                            {{ Auth::user()->name }}
                        </div>
                        <div class="text-[10px] text-[#6B7280] font-light capitalize">
                            {{ Auth::user()->role ?? 'User' }}
                        </div>
                    </div>

                    <span class="text-[#6B7280] text-xs flex-shrink-0 leading-none">⌃</span>

                    {{-- Dropdown --}}
                    <div id="userDropdown"
                        class="hidden absolute bottom-[calc(100%+8px)] left-0 right-0 z-[100]
                               bg-white dark:bg-[#1C1C2E]
                               border border-black/[0.08] dark:border-white/[0.07]
                               rounded-xl p-1.5
                               shadow-[0_-8px_32px_rgba(0,0,0,0.12)] dark:shadow-[0_-8px_32px_rgba(0,0,0,0.4)]">

                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-[13px]
                                   text-[#6B7280] dark:text-[#9CA3AF] no-underline transition-all duration-150
                                   hover:bg-[rgba(0,48,135,0.06)] dark:hover:bg-white/[0.04]
                                   hover:text-[#003087] dark:hover:text-white">
                            👤 Profile Settings
                        </a>

                        <div class="h-px bg-black/[0.07] dark:bg-white/[0.07] my-1.5"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="flex items-center gap-2.5 w-full px-3 py-2.5 rounded-lg text-[13px]
                                       font-[inherit] text-[#6B7280] dark:text-[#9CA3AF]
                                       bg-transparent border-0 text-left cursor-pointer
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
        <div id="sidebarOverlay" onclick="closeSidebar()" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[55]"
            style="display:none;"></div>

        {{-- ══ MAIN AREA ════════════════════════════════════════ --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0">

            {{-- Topbar --}}
            <header
                class="h-[60px] flex-shrink-0 flex items-center justify-between
                       px-7 md:px-7
                       border-b border-black/[0.08] dark:border-white/[0.07]
                       bg-[rgba(240,242,248,0.9)] dark:bg-[rgba(13,13,20,0.8)]
                       backdrop-blur-xl sticky top-0 z-20">

                <div class="flex items-center gap-3.5">
                    <button id="sidebarToggleBtn" onclick="openSidebar()"
                        class="md:hidden flex items-center justify-center w-9 h-9
                               rounded-lg bg-transparent border-0
                               text-[#6B7280] dark:text-[#9CA3AF] text-xl cursor-pointer
                               transition-all duration-150
                               hover:bg-black/[0.05] dark:hover:bg-white/[0.04]
                               hover:text-[#0D0D14] dark:hover:text-white">
                        ☰
                    </button>
                    <div>
                        <div class="font-playfair text-xl font-bold text-[#0D0D14] dark:text-white leading-tight">
                            @yield('title', 'Dashboard')
                        </div>
                        <div class="text-xs text-[#9CA3AF] dark:text-[#6B7280] font-light mt-0.5">
                            Mini Mart POS · Cambodia 🇰🇭
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">

                    {{-- Clock --}}
                    <div
                        class="hidden md:flex items-center gap-2 h-9 px-3.5 rounded-[9px]
                                bg-black/[0.04] dark:bg-white/[0.04]
                                border border-black/[0.08] dark:border-white/[0.07]
                                font-mono-ibm text-xs text-[#9CA3AF] whitespace-nowrap">
                        <span id="clock-date"></span>
                        <span class="text-black/[0.15] dark:text-white/[0.07]">|</span>
                        <span id="clock-time"></span>
                    </div>

                    {{-- Theme toggle --}}
                    <button id="themeToggle" onclick="toggleTheme()" title="Toggle theme"
                        class="w-9 h-9 rounded-[9px] flex items-center justify-center text-base cursor-pointer
                               bg-black/[0.04] dark:bg-white/[0.04]
                               border border-black/[0.08] dark:border-white/[0.07]
                               text-[#9CA3AF]
                               hover:bg-black/[0.08] dark:hover:bg-white/[0.08]
                               hover:text-[#0D0D14] dark:hover:text-white
                               transition-all duration-[180ms]">
                        <span id="themeIcon">🌙</span>
                    </button>

                    {{-- POS quick link --}}
                    @canDo('pos.view')
                    <a href="{{ route('admin.pos.index') }}" title="Open POS"
                        class="w-9 h-9 rounded-[9px] flex items-center justify-center text-base no-underline
                               bg-black/[0.04] dark:bg-white/[0.04]
                               border border-black/[0.08] dark:border-white/[0.07]
                               text-[#9CA3AF]
                               hover:bg-black/[0.08] dark:hover:bg-white/[0.08]
                               hover:text-[#0D0D14] dark:hover:text-white
                               transition-all duration-[180ms]">
                        🖥️
                    </a>
                    @endCanDo

                    {{-- Profile --}}
                    <a href="{{ route('profile.edit') }}" title="Profile"
                        class="w-9 h-9 rounded-[9px] flex items-center justify-center text-base no-underline
                               bg-black/[0.04] dark:bg-white/[0.04]
                               border border-black/[0.08] dark:border-white/[0.07]
                               text-[#9CA3AF]
                               hover:bg-black/[0.08] dark:hover:bg-white/[0.08]
                               hover:text-[#0D0D14] dark:hover:text-white
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
        const THEME_KEY = 'minimart_theme';

        function applyTheme(theme) {
            const html = document.documentElement;
            const icon = document.getElementById('themeIcon');
            if (theme === 'dark') {
                html.classList.add('dark');
                if (icon) icon.textContent = '☀️';
            } else {
                html.classList.remove('dark');
                if (icon) icon.textContent = '🌙';
            }
        }

        function toggleTheme() {
            const current = localStorage.getItem(THEME_KEY) || 'light';
            const next = current === 'light' ? 'dark' : 'light';
            localStorage.setItem(THEME_KEY, next);
            applyTheme(next);
        }

        applyTheme(localStorage.getItem(THEME_KEY) || 'light');

        function updateClock() {
            const now = new Date();
            const d = document.getElementById('clock-date');
            const t = document.getElementById('clock-time');
            if (d) d.textContent = now.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                timeZone: 'Asia/Phnom_Penh'
            });
            if (t) t.textContent = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZone: 'Asia/Phnom_Penh'
            });
        }
        updateClock();
        setInterval(updateClock, 1000);

        function toggleUserMenu() {
            document.getElementById('userDropdown').classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const trigger = document.getElementById('userTrigger');
            const dd = document.getElementById('userDropdown');
            if (dd && trigger && !trigger.contains(e.target)) dd.classList.add('hidden');
        });

        function openSidebar() {
            document.getElementById('sidebar').classList.add('sidebar-open');
            const ov = document.getElementById('sidebarOverlay');
            ov.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            document.getElementById('sidebar').classList.remove('sidebar-open');
            const ov = document.getElementById('sidebarOverlay');
            ov.style.display = 'none';
            document.body.style.overflow = '';
        }
    </script>

    {{-- ── Global Loading Overlay ──────────────────────────────── --}}
    <div id="globalLoader"
        style="display:none; position:fixed; inset:0; z-index:9999;
               background:rgba(0,0,0,0.45); backdrop-filter:blur(3px);
               align-items:center; justify-content:center; flex-direction:column; gap:16px;">

        {{-- Spinner card --}}
        <div
            style="background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.12);
                    border-radius:20px; padding:28px 36px;
                    display:flex; flex-direction:column; align-items:center; gap:14px;
                    backdrop-filter:blur(16px); box-shadow:0 8px 40px rgba(0,0,0,0.4);">

            {{-- Animated ring --}}
            <div style="position:relative; width:48px; height:48px;">
                <svg style="animation:spin 0.9s linear infinite; width:48px; height:48px;" viewBox="0 0 48 48"
                    fill="none">
                    <circle cx="24" cy="24" r="20" stroke="rgba(255,255,255,0.12)" stroke-width="4" />
                    <path d="M24 4 A20 20 0 0 1 44 24" stroke="url(#lg)" stroke-width="4" stroke-linecap="round" />
                    <defs>
                        <linearGradient id="lg" x1="24" y1="4" x2="44" y2="24"
                            gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#3B82F6" />
                            <stop offset="100%" stop-color="#003087" />
                        </linearGradient>
                    </defs>
                </svg>
                {{-- Store icon in center --}}
                <span
                    style="position:absolute; inset:0; display:flex; align-items:center;
                             justify-content:center; font-size:16px;">🏪</span>
            </div>

            {{-- Text --}}
            <div style="text-align:center;">
                <div id="loaderText"
                    style="font-family:'DM Sans',sans-serif; font-size:13px; font-weight:600;
                            color:rgba(255,255,255,0.9); letter-spacing:0.2px;">
                    Loading…
                </div>
                <div
                    style="font-family:'DM Sans',sans-serif; font-size:11px;
                            color:rgba(255,255,255,0.4); margin-top:3px;">
                    Mini Mart POS
                </div>
            </div>

            {{-- Progress bar --}}
            <div
                style="width:160px; height:3px; background:rgba(255,255,255,0.08);
                        border-radius:999px; overflow:hidden;">
                <div id="loaderBar"
                    style="height:100%; width:0%; border-radius:999px;
                            background:linear-gradient(90deg,#003087,#3B82F6);
                            transition:width 0.4s ease;">
                </div>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const loader = document.getElementById('globalLoader');
            const loaderText = document.getElementById('loaderText');
            const loaderBar = document.getElementById('loaderBar');

            const messages = {
                nav: 'Navigating…',
                form: 'Saving…',
                update: 'Updating…',
                delete: 'Deleting…',
                login: 'Signing in…',
                logout: 'Signing out…',
                default: 'Loading…',
            };

            let hideTimer;

            function showLoader(type) {
                clearTimeout(hideTimer);
                loader.style.display = 'flex';
                loaderText.textContent = messages[type] || messages.default;
                loaderBar.style.transition = 'none';
                loaderBar.style.width = '0%';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    loaderBar.style.transition = 'width 10s cubic-bezier(0.1,0.4,0.2,1)';
                    loaderBar.style.width = '85%';
                }));
            }

            function hideLoader() {
                loaderBar.style.transition = 'width 0.3s ease';
                loaderBar.style.width = '100%';
                hideTimer = setTimeout(() => {
                    loader.style.display = 'none';
                    loaderBar.style.width = '0%';
                }, 300);
            }

            // ── Link clicks ───────────────────────────────────────────
            document.addEventListener('click', function(e) {
                if (e.target.closest('[data-no-loader]')) return;
                const link = e.target.closest('a[href]');
                if (!link) return;
                const href = link.getAttribute('href');
                if (!href || href === '#' || href.startsWith('javascript') ||
                    href.startsWith('http') || href.startsWith('//') ||
                    link.target === '_blank') return;
                const type = href.includes('logout') ? 'logout' :
                    href.includes('login') ? 'login' :
                    'nav';
                showLoader(type);
            }, true);

            // ── Form submits ──────────────────────────────────────────
            document.addEventListener('submit', function(e) {
                const form = e.target;
                // If form has data-no-loader, skip
                if (form.closest('[data-no-loader]') || form.hasAttribute('data-no-loader')) return;
                const methodInput = form.querySelector('input[name="_method"]');
                const method = methodInput ? methodInput.value.toUpperCase() : form.method.toUpperCase();
                const action = form.action || '';
                const type = method === 'DELETE' ? 'delete' :
                    method === 'PUT' ? 'update' :
                    method === 'PATCH' ? 'update' :
                    action.includes('logout') ? 'logout' :
                    action.includes('login') ? 'login' :
                    'form';
                showLoader(type);
            }, true);

            // ── Hide on back/forward cache ────────────────────────────
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) hideLoader();
            });

        })();
    </script>

    @stack('scripts')
</body>

</html>
