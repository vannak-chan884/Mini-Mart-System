<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email — Mini Mart POS</title>
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
            --error: #F87171;
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
            width: 520px;
            height: 520px;
            background: radial-gradient(circle, rgba(234, 179, 8, 0.12) 0%, transparent 70%);
            top: -160px;
            left: 50%;
            transform: translateX(-50%);
            animation: drift1 14s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(0, 48, 135, 0.18) 0%, transparent 70%);
            bottom: -80px;
            left: -60px;
            animation: drift2 17s ease-in-out infinite alternate;
        }

        .bg-glow-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(204, 0, 1, 0.07) 0%, transparent 70%);
            bottom: 25%;
            right: -40px;
            animation: drift3 20s ease-in-out infinite alternate;
        }

        @keyframes drift1 {
            to {
                transform: translateX(-50%) translateY(55px);
            }
        }

        @keyframes drift2 {
            to {
                transform: translate(35px, -45px);
            }
        }

        @keyframes drift3 {
            to {
                transform: translate(-25px, 35px);
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

        .logout-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            color: var(--error);
            border-color: rgba(248, 113, 113, 0.3);
            background: rgba(248, 113, 113, 0.05);
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

        .card {
            width: 100%;
            max-width: 460px;
            animation: fadeUp 0.5s ease both;
        }

        /* Envelope animation */
        .envelope-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 32px;
        }

        .envelope-outer {
            position: relative;
            width: 90px;
            height: 90px;
        }

        .envelope-icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, rgba(234, 179, 8, 0.18), rgba(234, 179, 8, 0.06));
            border: 1px solid rgba(234, 179, 8, 0.3);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            box-shadow: 0 0 50px rgba(234, 179, 8, 0.15);
            animation: envelope-float 3s ease-in-out infinite;
        }

        @keyframes envelope-float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        /* Orbiting dots */
        .orbit {
            position: absolute;
            inset: -14px;
            border-radius: 34px;
            border: 1px dashed rgba(234, 179, 8, 0.2);
            animation: spin 10s linear infinite;
        }

        .orbit::before,
        .orbit::after {
            content: '';
            position: absolute;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: rgba(234, 179, 8, 0.5);
        }

        .orbit::before {
            top: -3.5px;
            left: 50%;
            transform: translateX(-50%);
        }

        .orbit::after {
            bottom: -3.5px;
            left: 50%;
            transform: translateX(-50%);
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Card header */
        .card-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .card-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(234, 179, 8, 0.1);
            border: 1px solid rgba(234, 179, 8, 0.22);
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 600;
            color: #FDE68A;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .card-eyebrow .dot {
            width: 5px;
            height: 5px;
            background: #FDE68A;
            border-radius: 50%;
            animation: blink 2s ease infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.2;
            }
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            font-weight: 900;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .card-title .accent {
            background: linear-gradient(135deg, #FDE68A, #F59E0B);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--muted);
            font-family: 'Noto Sans Khmer', sans-serif;
            font-weight: 300;
        }

        .flag-bar {
            display: flex;
            width: 60px;
            height: 3px;
            border-radius: 2px;
            overflow: hidden;
            gap: 2px;
            margin: 16px auto 0;
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

        /* Form box */
        .form-box {
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
        }

        .form-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(234, 179, 8, 0.2), transparent);
        }

        /* Success alert */
        .success-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
            animation: fadeUp 0.3s ease;
        }

        .success-alert .icon {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .success-alert p {
            font-size: 13px;
            color: #86EFAC;
            line-height: 1.6;
            font-weight: 400;
        }

        /* Info box */
        .info-box {
            background: rgba(234, 179, 8, 0.06);
            border: 1px solid rgba(234, 179, 8, 0.18);
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 28px;
        }

        .info-box-title {
            font-size: 13px;
            font-weight: 600;
            color: #FDE68A;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .info-box p {
            font-size: 13px;
            color: var(--muted);
            line-height: 1.7;
            font-weight: 300;
        }

        /* Steps checklist */
        .checklist {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 28px;
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 13px;
            color: var(--muted);
            transition: all 0.2s;
        }

        .check-item.done {
            border-color: rgba(22, 163, 74, 0.25);
            background: rgba(22, 163, 74, 0.05);
            color: #86EFAC;
        }

        .check-item .num {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--glass);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: var(--muted);
            flex-shrink: 0;
        }

        .check-item.done .num {
            background: rgba(22, 163, 74, 0.2);
            border-color: rgba(22, 163, 74, 0.4);
            color: #86EFAC;
        }

        .check-item.active .num {
            background: linear-gradient(135deg, #92400E, #B45309);
            border-color: transparent;
            color: #FDE68A;
        }

        .check-item.active {
            border-color: rgba(234, 179, 8, 0.3);
            background: rgba(234, 179, 8, 0.05);
            color: #FDE68A;
        }

        /* Resend button */
        .btn-resend {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #92400E 0%, #B45309 100%);
            color: #FDE68A;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(180, 83, 9, 0.3);
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-resend:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(180, 83, 9, 0.45);
        }

        .btn-resend:active {
            transform: translateY(0);
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider-text {
            font-size: 12px;
            color: rgba(156, 163, 175, 0.5);
        }

        /* Help note */
        .help-note {
            text-align: center;
            font-size: 12px;
            color: rgba(156, 163, 175, 0.55);
            line-height: 1.6;
        }

        .help-note strong {
            color: var(--muted);
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

            .form-box {
                padding: 28px 20px;
            }

            footer {
                padding: 20px 24px;
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
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
                <div class="nav-logo-name">Mini Mart</div>
                <span class="nav-logo-sub">ហាងលក់គ្រឿងទំនិញ</span>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                ↩ Log Out
            </button>
        </form>
    </nav>

    {{-- MAIN --}}
    <div class="main">
        <div class="card">

            {{-- Envelope --}}
            <div class="envelope-wrap">
                <div class="envelope-outer">
                    <div class="orbit"></div>
                    <div class="envelope-icon">📧</div>
                </div>
            </div>

            {{-- Header --}}
            <div class="card-header">
                <div class="card-eyebrow">
                    <span class="dot"></span>
                    Email Verification
                </div>
                <h1 class="card-title">Check Your <span class="accent">Inbox</span></h1>
                <p class="card-subtitle">សូមផ្ទៀងផ្ទាត់អ៊ីមែលរបស់អ្នក</p>
                <div class="flag-bar">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Form box --}}
            <div class="form-box">

                {{-- Success: resend confirmation --}}
                @if (session('status') == 'verification-link-sent')
                    <div class="success-alert">
                        <span class="icon">✅</span>
                        <p>A new verification link has been sent to your email address. Please check your inbox.</p>
                    </div>
                @endif

                {{-- Info --}}
                <div class="info-box">
                    <div class="info-box-title">📬 Verification email sent</div>
                    <p>
                        Thanks for signing up! We sent a verification link to your registered email address.
                        Click the link in that email to activate your account and start using Mini Mart POS.
                    </p>
                </div>

                {{-- Steps checklist --}}
                <div class="checklist">
                    <div class="check-item done">
                        <div class="num">✓</div>
                        Account created successfully
                    </div>
                    <div class="check-item active">
                        <div class="num">2</div>
                        Verify your email address
                    </div>
                    <div class="check-item">
                        <div class="num">3</div>
                        Access Mini Mart POS
                    </div>
                </div>

                {{-- Resend button --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn-resend">
                        📨 Resend Verification Email
                    </button>
                </form>

                <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">need help?</span>
                    <div class="divider-line"></div>
                </div>

                <div class="help-note">
                    Didn't receive the email? Check your <strong>spam or junk folder</strong>.<br>
                    Still nothing? Click the button above to resend.
                </div>

            </div>

        </div>
    </div>

    {{-- Footer --}}
    <footer>
        <div class="footer-left">
            <strong>Mini Mart POS</strong> — Cambodia 🇰🇭
        </div>
        <div class="footer-right">
            Payments via <span class="nbc-badge">KHQR</span> National Bank of Cambodia
        </div>
    </footer>

</body>

</html>
