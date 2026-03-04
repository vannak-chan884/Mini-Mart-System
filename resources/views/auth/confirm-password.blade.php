<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Password — Mini Mart POS</title>
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
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 48, 135, 0.22) 0%, transparent 70%);
            top: -160px;
            left: -80px;
            animation: drift1 13s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(244, 169, 0, 0.09) 0%, transparent 70%);
            bottom: -80px;
            right: -60px;
            animation: drift2 17s ease-in-out infinite alternate;
        }

        .bg-glow-3 {
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(204, 0, 1, 0.07) 0%, transparent 70%);
            top: 45%;
            left: 55%;
            animation: drift3 21s ease-in-out infinite alternate;
        }

        @keyframes drift1 {
            to {
                transform: translate(50px, 70px);
            }
        }

        @keyframes drift2 {
            to {
                transform: translate(-30px, -50px);
            }
        }

        @keyframes drift3 {
            to {
                transform: translate(25px, -35px);
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

        /* Secure badge in nav */
        .secure-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.25);
            border-radius: 999px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            color: #86EFAC;
            letter-spacing: 0.5px;
        }

        .secure-badge .lock {
            font-size: 13px;
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
            max-width: 400px;
            animation: fadeUp 0.5s ease both;
        }

        /* Shield icon */
        .shield-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 28px;
        }

        .shield-icon {
            position: relative;
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(0, 48, 135, 0.3), rgba(0, 48, 135, 0.1));
            border: 1px solid rgba(0, 48, 135, 0.4);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            box-shadow: 0 0 40px rgba(0, 48, 135, 0.25);
            animation: pulse-glow 3s ease-in-out infinite;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 40px rgba(0, 48, 135, 0.25);
            }

            50% {
                box-shadow: 0 0 60px rgba(0, 48, 135, 0.45);
            }
        }

        /* Orbiting ring */
        .shield-ring {
            position: absolute;
            inset: -10px;
            border-radius: 30px;
            border: 1px dashed rgba(0, 80, 200, 0.2);
            animation: spin-slow 12s linear infinite;
        }

        @keyframes spin-slow {
            to {
                transform: rotate(360deg);
            }
        }

        /* Header */
        .card-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .card-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.2);
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 600;
            color: #86EFAC;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .card-eyebrow .dot {
            width: 5px;
            height: 5px;
            background: #86EFAC;
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
            margin-bottom: 12px;
        }

        .card-title .accent {
            background: linear-gradient(135deg, #93C5FD, #3B82F6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-desc {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.7;
            font-weight: 300;
            max-width: 320px;
            margin: 0 auto;
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
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.2), transparent);
        }

        /* Security notice */
        .security-notice {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(59, 130, 246, 0.07);
            border: 1px solid rgba(59, 130, 246, 0.2);
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 24px;
        }

        .security-notice .icon {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .security-notice p {
            font-size: 13px;
            color: #93C5FD;
            line-height: 1.6;
            font-weight: 300;
        }

        .security-notice p strong {
            font-weight: 600;
            color: #BFDBFE;
        }

        /* Form */
        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #D1D5DB;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 8px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 46px;
        }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 13px 16px;
            font-size: 15px;
            font-family: 'DM Sans', sans-serif;
            color: #fff;
            outline: none;
            transition: all 0.2s ease;
        }

        input::placeholder {
            color: rgba(156, 163, 175, 0.45);
        }

        input:focus {
            border-color: rgba(59, 130, 246, 0.5);
            background: rgba(59, 130, 246, 0.06);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        input.has-error {
            border-color: rgba(248, 113, 113, 0.5);
            background: rgba(248, 113, 113, 0.05);
        }

        .input-error {
            margin-top: 6px;
            font-size: 12px;
            color: var(--error);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .eye-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            cursor: pointer;
            font-size: 17px;
            transition: color 0.2s;
        }

        .eye-toggle:hover {
            color: #fff;
        }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.35);
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Trust indicators */
        .trust-row {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: rgba(156, 163, 175, 0.6);
        }

        .trust-item .icon {
            font-size: 12px;
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

            .trust-row {
                flex-wrap: wrap;
                gap: 12px;
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
        <div class="secure-badge">
            <span class="lock">🔒</span>
            Secure Area
        </div>
    </nav>

    {{-- MAIN --}}
    <div class="main">
        <div class="card">

            {{-- Shield icon --}}
            <div class="shield-wrap">
                <div class="shield-icon">
                    <div class="shield-ring"></div>
                    🛡️
                </div>
            </div>

            {{-- Header --}}
            <div class="card-header">
                <div class="card-eyebrow">
                    <span class="dot"></span>
                    Identity Check
                </div>
                <h1 class="card-title">Confirm Your <span class="accent">Identity</span></h1>
                <p class="card-desc">
                    You're entering a protected area. Please verify your password to continue.
                </p>
                <div class="flag-bar">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Form box --}}
            <div class="form-box">

                {{-- Security notice --}}
                <div class="security-notice">
                    <span class="icon">🔐</span>
                    <p><strong>Secure area detected.</strong> This action requires re-authentication to protect your
                        account and store data.</p>
                </div>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="form-group">
                        <label for="password">Your Password</label>
                        <div class="password-wrap">
                            <input id="password" type="password" name="password"
                                placeholder="Enter your current password" required autocomplete="current-password"
                                class="{{ $errors->has('password') ? 'has-error' : '' }}">
                            <button type="button" class="eye-toggle" onclick="togglePassword()">👁</button>
                        </div>
                        @error('password')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>🛡️</span> Confirm & Continue
                    </button>

                    <div class="trust-row">
                        <div class="trust-item"><span class="icon">🔒</span> Encrypted</div>
                        <div class="trust-item"><span class="icon">✅</span> NBC Verified</div>
                        <div class="trust-item"><span class="icon">🇰🇭</span> Cambodia</div>
                    </div>

                </form>
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

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const btn = event.currentTarget;
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }
    </script>

</body>

</html>
