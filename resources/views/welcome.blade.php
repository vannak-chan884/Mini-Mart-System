<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mini Mart — POS System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --ink: #0D0D14;
            --deep: #111827;
            --blue: #003087;
            --red: #CC0001;
            --gold: #F4A900;
            --cream: #F5F0E8;
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

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Nunito Sans', sans-serif;
            background: var(--ink);
            color: #E8E4DC;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Noise texture overlay ── */
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

        /* ── Background glow blobs ── */
        .bg-glow {
            position: fixed;
            border-radius: 50%;
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
        }

        .bg-glow-1 {
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(0, 48, 135, 0.25) 0%, transparent 70%);
            top: -200px;
            left: -100px;
            animation: drift1 12s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(204, 0, 1, 0.12) 0%, transparent 70%);
            bottom: -100px;
            right: -100px;
            animation: drift2 15s ease-in-out infinite alternate;
        }

        .bg-glow-3 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(244, 169, 0, 0.08) 0%, transparent 70%);
            top: 40%;
            left: 50%;
            animation: drift3 18s ease-in-out infinite alternate;
        }

        @keyframes drift1 {
            to {
                transform: translate(60px, 80px);
            }
        }

        @keyframes drift2 {
            to {
                transform: translate(-40px, -60px);
            }
        }

        @keyframes drift3 {
            to {
                transform: translate(30px, -50px);
            }
        }

        /* ── Layout ── */
        .page-wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Nav ── */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 48px;
            border-bottom: 1px solid var(--border);
            backdrop-filter: blur(12px);
            background: rgba(13, 13, 20, 0.6);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 12px;
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

        .nav-logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.3px;
        }

        .nav-logo-sub {
            font-size: 10px;
            color: var(--muted);
            font-family: 'Noto Sans Khmer', sans-serif;
            font-weight: 300;
            display: block;
            margin-top: -2px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            letter-spacing: 0.2px;
        }

        .btn-ghost {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            background: var(--glass);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue) 0%, #1a4db3 100%);
            color: #fff;
            box-shadow: 0 4px 20px rgba(0, 48, 135, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px rgba(0, 48, 135, 0.5);
        }

        .btn-large {
            padding: 14px 32px;
            font-size: 15px;
            font-weight: 600;
            border-radius: 12px;
        }

        .btn-outline {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.35);
        }

        /* ── Hero ── */
        .hero {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 100px 24px 80px;
            position: relative;
        }

        .hero-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(244, 169, 0, 0.1);
            border: 1px solid rgba(244, 169, 0, 0.25);
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 500;
            color: var(--gold);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 32px;
            animation: fadeUp 0.6s ease both;
        }

        .hero-eyebrow .pulse {
            width: 6px;
            height: 6px;
            background: var(--gold);
            border-radius: 50%;
            animation: pulse 2s ease infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(0.7);
            }
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(48px, 8vw, 88px);
            font-weight: 900;
            line-height: 1.05;
            color: #fff;
            margin-bottom: 8px;
            animation: fadeUp 0.6s ease 0.1s both;
        }

        .hero-title .accent {
            background: linear-gradient(135deg, var(--gold) 0%, #f97316 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-title-kh {
            font-family: 'Noto Sans Khmer', sans-serif;
            font-size: clamp(18px, 3vw, 28px);
            font-weight: 300;
            color: var(--muted);
            margin-bottom: 28px;
            animation: fadeUp 0.6s ease 0.2s both;
        }

        .hero-desc {
            font-size: 18px;
            color: #9CA3AF;
            max-width: 800px;
            line-height: 1.7;
            margin-bottom: 48px;
            font-weight: 300;
            animation: fadeUp 0.6s ease 0.3s both;
        }

        .hero-actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            justify-content: center;
            animation: fadeUp 0.6s ease 0.4s both;
        }

        /* ── Flag stripe decoration ── */
        .flag-bar {
            display: flex;
            width: 80px;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            gap: 2px;
            margin: 0 auto 40px;
            animation: fadeUp 0.6s ease 0.15s both;
        }

        .flag-bar span:nth-child(1),
        .flag-bar span:nth-child(3) {
            background: var(--red);
            flex: 1;
        }

        .flag-bar span:nth-child(2) {
            background: #4a90d9;
            flex: 2;
        }

        /* ── Stats strip ── */
        .stats-strip {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-top: 72px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            animation: fadeUp 0.6s ease 0.5s both;
        }

        .stat-item {
            flex: 1;
            max-width: 200px;
            padding: 28px 24px;
            text-align: center;
            border-right: 1px solid var(--border);
        }

        .stat-item:last-child {
            border-right: none;
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-number .unit {
            font-size: 18px;
            color: var(--gold);
        }

        .stat-label {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        /* ── Features ── */
        .features {
            padding: 100px 48px;
            max-width: 1100px;
            margin: 0 auto;
            width: 100%;
        }

        .section-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gold);
            font-weight: 600;
            margin-bottom: 16px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 700;
            color: #fff;
            margin-bottom: 56px;
            max-width: 500px;
            line-height: 1.2;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 32px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .feature-card:hover::before {
            opacity: 1;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 20px;
        }

        .feature-icon.blue {
            background: rgba(0, 48, 135, 0.3);
            box-shadow: 0 0 20px rgba(0, 48, 135, 0.2);
        }

        .feature-icon.red {
            background: rgba(204, 0, 1, 0.2);
            box-shadow: 0 0 20px rgba(204, 0, 1, 0.15);
        }

        .feature-icon.gold {
            background: rgba(244, 169, 0, 0.15);
            box-shadow: 0 0 20px rgba(244, 169, 0, 0.1);
        }

        .feature-icon.green {
            background: rgba(22, 163, 74, 0.2);
            box-shadow: 0 0 20px rgba(22, 163, 74, 0.1);
        }

        .feature-title {
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            margin-bottom: 10px;
        }

        .feature-desc {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.65;
            font-weight: 300;
        }

        /* ── KHQR highlight band ── */
        .khqr-band {
            margin: 0 48px 100px;
            max-width: 1100px;
            margin-left: auto;
            margin-right: auto;
            background: linear-gradient(135deg, rgba(0, 48, 135, 0.3) 0%, rgba(0, 48, 135, 0.1) 100%);
            border: 1px solid rgba(0, 48, 135, 0.4);
            border-radius: 20px;
            padding: 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 32px;
            flex-wrap: wrap;
            position: relative;
            overflow: hidden;
        }

        .khqr-band::before {
            content: 'KHQR';
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            font-family: 'Playfair Display', serif;
            font-size: 120px;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.03);
            pointer-events: none;
            letter-spacing: -4px;
        }

        .khqr-band-left {
            flex: 1;
            min-width: 260px;
        }

        .khqr-band-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0, 48, 135, 0.4);
            border: 1px solid rgba(100, 149, 237, 0.3);
            border-radius: 999px;
            padding: 4px 12px;
            font-size: 11px;
            color: #93C5FD;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .khqr-band-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
            line-height: 1.2;
        }

        .khqr-band-desc {
            font-size: 14px;
            color: #9CA3AF;
            line-height: 1.65;
            font-weight: 300;
        }

        .khqr-currencies {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }

        .currency-pill {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px 24px;
            min-width: 110px;
            transition: all 0.25s;
        }

        .currency-pill:hover {
            background: rgba(255, 255, 255, 0.09);
            transform: translateY(-3px);
        }

        .currency-pill .flag {
            font-size: 28px;
        }

        .currency-pill .code {
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
        }

        .currency-pill .name {
            font-size: 11px;
            color: var(--muted);
        }

        /* ── Footer ── */
        footer {
            border-top: 1px solid var(--border);
            padding: 32px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .footer-left {
            font-size: 13px;
            color: var(--muted);
        }

        .footer-left strong {
            color: #fff;
        }

        .footer-right {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--muted);
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

        /* ── Animations ── */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            nav {
                padding: 18px 24px;
            }

            .hero {
                padding: 60px 24px 60px;
            }

            .features {
                padding: 60px 24px;
            }

            .khqr-band {
                margin: 0 24px 60px;
                padding: 32px 24px;
            }

            footer {
                padding: 24px;
                flex-direction: column;
                text-align: center;
            }

            .stats-strip {
                flex-wrap: wrap;
            }

            .stat-item {
                max-width: none;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }

            .stat-item:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>

<body>

    <div class="bg-glow bg-glow-1"></div>
    <div class="bg-glow bg-glow-2"></div>
    <div class="bg-glow bg-glow-3"></div>

    <div class="page-wrap">

        {{-- ── NAV ── --}}
        <nav>
            <div class="nav-logo">
                <div class="nav-logo-icon">🏪</div>
                <div>
                    <div class="nav-logo-text">Mini Mart</div>
                    <span class="nav-logo-sub">ហាងលក់គ្រឿងទំនិញ</span>
                </div>
            </div>
            <div class="nav-right">
                @auth
                    <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">
                        Open POS →
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Log In</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                    @endif
                @endauth
            </div>
        </nav>

        {{-- ── HERO ── --}}
        <section class="hero">
            <div class="hero-eyebrow">
                <span class="pulse"></span>
                NBC Bakong KHQR Integrated
            </div>

            <h1 class="hero-title">
                Smart POS for
                <span class="accent">Cambodia</span>
            </h1>

            {{-- <div class="flag-bar">
                <span></span><span></span><span></span>
            </div> --}}

            <p class="hero-title-kh mt-4">ប្រព័ន្ធលក់ទំនិញទំនើប</p>

            <p class="hero-desc">
                A modern point-of-sale system built for Cambodian businesses —
                with dual-currency KHQR payments, real-time stock tracking, and instant Telegram notifications.
            </p>

            <div class="hero-actions">
                @auth
                    <a href="{{ route('admin.pos.index') }}" class="btn btn-primary btn-large">
                        🛒 Open POS Terminal
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-large">
                        Get Started Free
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline btn-large">
                            Create Account
                        </a>
                    @endif
                @endauth
            </div>

            <div class="stats-strip">
                <div class="stat-item">
                    <div class="stat-number">2<span class="unit">x</span></div>
                    <div class="stat-label">QR Currencies</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><span class="unit">$</span>+៛</div>
                    <div class="stat-label">USD & KHR</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">⚡</div>
                    <div class="stat-label">Real-Time Verify</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">📲</div>
                    <div class="stat-label">Telegram Alerts</div>
                </div>
            </div>
        </section>

        {{-- ── FEATURES ── --}}
        <section class="features">
            <div class="section-label">What's included</div>
            <h2 class="section-title">Everything your store needs</h2>

            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon blue">🛒</div>
                    <div class="feature-title">Fast POS Terminal</div>
                    <div class="feature-desc">Search by name or barcode, manage cart quantities, and process sales in
                        seconds with a clean, distraction-free interface.</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon gold">📱</div>
                    <div class="feature-title">Dual KHQR Payment</div>
                    <div class="feature-desc">Generate both USD and KHR QR codes simultaneously. Customers scan with any
                        Cambodian bank — ABA, ACLEDA, Wing, and more.</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon green">📦</div>
                    <div class="feature-title">Stock Management</div>
                    <div class="feature-desc">Track inventory in real time. Every sale automatically deducts stock and
                        logs a history entry for full audit trails.</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon red">🔔</div>
                    <div class="feature-title">Telegram Notifications</div>
                    <div class="feature-desc">Get instant sale alerts on Telegram with invoice number, amount, payment
                        method, and Bakong transaction reference.</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon blue">🧾</div>
                    <div class="feature-title">Professional Receipts</div>
                    <div class="feature-desc">Print-ready receipts in Khmer and English with itemized totals, KHQR
                        transaction hash, and your store branding.</div>
                </div>
                <div class="feature-card">
                    <div class="feature-icon gold">🏦</div>
                    <div class="feature-title">NBC Bakong Verified</div>
                    <div class="feature-desc">Payments are verified directly against the National Bank of Cambodia's
                        Bakong API — no middlemen, instant confirmation.</div>
                </div>
            </div>
        </section>

        {{-- ── KHQR BAND ── --}}
        <div style="padding: 0 48px; max-width: 1196px; margin: 0 auto; width: 100%;">
            <div class="khqr-band">
                <div class="khqr-band-left">
                    <div class="khqr-band-eyebrow">🏦 Powered by NBC Bakong</div>
                    <div class="khqr-band-title">Accept payments in<br>USD & KHR simultaneously</div>
                    <div class="khqr-band-desc">
                        Show two QR codes side-by-side at checkout. Your customer chooses their preferred currency —
                        the system auto-detects which QR was paid and completes the sale instantly.
                    </div>
                </div>
                <div class="khqr-currencies">
                    <div class="currency-pill">
                        <span class="flag">
                            $
                        </span>
                        <span class="code">USD</span>
                        <span class="name">US Dollar</span>
                    </div>
                    <div class="currency-pill">
                        <span class="flag">
                            ៛
                        </span>
                        <span class="code">KHR</span>
                        <span class="name">Khmer Riel</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── FOOTER ── --}}
        <footer>
            <div class="footer-left">
                <strong>Mini Mart POS</strong> — Built for Cambodia
                <br>
                <span style="font-size:12px;">ប្រព័ន្ធគ្រប់គ្រងការលក់ • Takeo</span>
            </div>
            <div class="footer-right">
                Payments via
                <span class="nbc-badge">KHQR</span>
                National Bank of Cambodia
            </div>
        </footer>

    </div>

</body>

</html>
