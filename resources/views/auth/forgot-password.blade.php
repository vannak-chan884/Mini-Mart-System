<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Mini Mart POS</title>
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
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(244, 169, 0, 0.12) 0%, transparent 70%);
            top: -120px;
            left: 50%;
            transform: translateX(-50%);
            animation: drift1 14s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(0, 48, 135, 0.2) 0%, transparent 70%);
            bottom: -80px;
            left: -60px;
            animation: drift2 16s ease-in-out infinite alternate;
        }

        .bg-glow-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(204, 0, 1, 0.08) 0%, transparent 70%);
            bottom: 20%;
            right: -40px;
            animation: drift3 20s ease-in-out infinite alternate;
        }

        @keyframes drift1 {
            to {
                transform: translateX(-50%) translateY(50px);
            }
        }

        @keyframes drift2 {
            to {
                transform: translate(30px, -40px);
            }
        }

        @keyframes drift3 {
            to {
                transform: translate(-20px, 30px);
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

        .nav-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            text-decoration: none;
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .nav-link:hover {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.2);
            background: var(--glass);
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
            max-width: 420px;
            animation: fadeUp 0.5s ease both;
        }

        /* Icon circle */
        .icon-circle {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, rgba(244, 169, 0, 0.15), rgba(244, 169, 0, 0.05));
            border: 1px solid rgba(244, 169, 0, 0.25);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin: 0 auto 24px;
            box-shadow: 0 0 30px rgba(244, 169, 0, 0.1);
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-6px);
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
            background: rgba(244, 169, 0, 0.1);
            border: 1px solid rgba(244, 169, 0, 0.2);
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 600;
            color: var(--gold);
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 20px;
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
            background: linear-gradient(135deg, var(--gold), #f97316);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-desc {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.7;
            font-weight: 300;
            max-width: 340px;
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        }

        /* Status alert — success */
        .status-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.3);
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            color: #86EFAC;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .status-alert .check {
            font-size: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* Steps hint */
        .steps {
            display: flex;
            gap: 0;
            margin-bottom: 28px;
        }

        .step {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
        }

        .step::after {
            content: '';
            position: absolute;
            top: 14px;
            left: 50%;
            right: -50%;
            height: 1px;
            background: var(--border);
        }

        .step:last-child::after {
            display: none;
        }

        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--glass);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--muted);
            position: relative;
            z-index: 1;
        }

        .step.active .step-num {
            background: linear-gradient(135deg, var(--blue), #1a4db3);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 0 12px rgba(0, 48, 135, 0.4);
        }

        .step-label {
            font-size: 10px;
            color: var(--muted);
            text-align: center;
            letter-spacing: 0.3px;
        }

        .step.active .step-label {
            color: #fff;
        }

        /* Form group */
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

        input[type="email"] {
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

        input[type="email"]::placeholder {
            color: rgba(156, 163, 175, 0.45);
        }

        input[type="email"]:focus {
            border-color: rgba(0, 80, 200, 0.6);
            background: rgba(0, 48, 135, 0.1);
            box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.15);
        }

        input[type="email"].has-error {
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

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--blue) 0%, #1a4db3 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(0, 48, 135, 0.4);
            letter-spacing: 0.3px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 48, 135, 0.55);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .back-row {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }

        .back-row a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .back-row a:hover {
            opacity: 0.75;
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
        <a href="{{ route('login') }}" class="nav-link">← Back to Sign In</a>
    </nav>

    {{-- MAIN --}}
    <div class="main">
        <div class="card">

            {{-- Floating icon --}}
            <div class="icon-circle">🔑</div>

            {{-- Header --}}
            <div class="card-header">
                <div class="card-eyebrow">Password Recovery</div>
                <h1 class="card-title">Reset Your <span class="accent">Password</span></h1>
                <p class="card-desc">
                    Enter your email address and we'll send you a secure link to choose a new password.
                </p>
                <div class="flag-bar">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Form box --}}
            <div class="form-box">

                {{-- Steps indicator --}}
                <div class="steps">
                    <div class="step active">
                        <div class="step-num">1</div>
                        <div class="step-label">Enter email</div>
                    </div>
                    <div class="step">
                        <div class="step-num">2</div>
                        <div class="step-label">Check inbox</div>
                    </div>
                    <div class="step">
                        <div class="step-num">3</div>
                        <div class="step-label">New password</div>
                    </div>
                </div>

                {{-- Success status --}}
                @if (session('status'))
                    <div class="status-alert">
                        <span class="check">✅</span>
                        <span>{{ session('status') }} Please check your inbox and follow the link to reset your
                            password.</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" required autofocus
                            class="{{ $errors->has('email') ? 'has-error' : '' }}">
                        @error('email')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">
                        📧 Send Reset Link
                    </button>

                    <div class="back-row">
                        Remembered it?
                        <a href="{{ route('login') }}">Sign in instead</a>
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

</body>

</html>
