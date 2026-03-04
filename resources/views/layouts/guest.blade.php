<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Mini Mart POS') }}</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;600&family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">

    <style>
        :root {
            --ink: #0D0D14;
            --blue: #003087;
            --red: #CC0001;
            --gold: #F4A900;
            --muted: #9CA3AF;
            --glass: rgba(255, 255, 255, 0.04);
            --border: rgba(255, 255, 255, 0.08);
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--ink);
            color: #E8E4DC;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.03'/%3E%3C/svg%3E");
            background-size: 200px;
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(130px);
            pointer-events: none;
            z-index: 0;
        }

        .bg-glow-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 48, 135, 0.25) 0%, transparent 70%);
            top: -150px;
            left: -80px;
            animation: drift1 12s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(204, 0, 1, 0.1) 0%, transparent 70%);
            bottom: -100px;
            right: -60px;
            animation: drift2 15s ease-in-out infinite alternate;
        }

        .bg-glow-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(244, 169, 0, 0.07) 0%, transparent 70%);
            top: 50%;
            left: 60%;
            animation: drift3 18s ease-in-out infinite alternate;
        }

        @keyframes drift1 {
            to {
                transform: translate(40px, 60px);
            }
        }

        @keyframes drift2 {
            to {
                transform: translate(-30px, -40px);
            }
        }

        @keyframes drift3 {
            to {
                transform: translate(20px, -40px);
            }
        }

        /* Nav */
        nav {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 48px;
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
            background: rgba(13, 13, 20, 0.6);
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--blue), #1a4db3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 0 20px rgba(0, 48, 135, 0.4);
        }

        .nav-logo-name {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
        }

        .nav-logo-sub {
            font-size: 10px;
            color: var(--muted);
            font-family: 'Noto Sans Khmer', sans-serif;
            font-weight: 300;
            display: block;
            margin-top: -2px;
        }

        /* Main */
        .main {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
        }

        .content-wrap {
            width: 100%;
            max-width: 460px;
            animation: fadeUp 0.5s ease both;
        }

        /* Footer */
        footer {
            position: relative;
            z-index: 1;
            border-top: 1px solid var(--border);
            padding: 20px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-left {
            font-size: 12px;
            color: var(--muted);
        }

        .footer-left strong {
            color: #fff;
        }

        .nbc-badge {
            background: rgba(0, 48, 135, 0.3);
            border: 1px solid rgba(0, 48, 135, 0.5);
            color: #93C5FD;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 5px;
            letter-spacing: 0.5px;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--muted);
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 600px) {
            nav {
                padding: 18px 20px;
            }

            footer {
                padding: 20px 24px;
                flex-direction: column;
                text-align: center;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>

    {{-- NAV --}}
    <nav>
        <a href="{{ url('/') }}" class="nav-logo">
            <div class="nav-logo-icon">🏪</div>
            <div>
                <div class="nav-logo-name">{{ config('app.name', 'Mini Mart') }}</div>
                <span class="nav-logo-sub">ហាងលក់គ្រឿងទំនិញ</span>
            </div>
        </a>
        @stack('nav-actions')
    </nav>

    {{-- MAIN --}}
    <div class="main">
        <div class="content-wrap">
            @yield('content')
        </div>
    </div>

    {{-- FOOTER --}}
    <footer>
        <div class="footer-left">
            <strong>{{ config('app.name', 'Mini Mart') }} POS</strong> — Cambodia 🇰🇭
        </div>
        <div class="footer-right">
            Payments via <span class="nbc-badge">KHQR</span> National Bank of Cambodia
        </div>
    </footer>

    @stack('scripts')

</body>

</html>
