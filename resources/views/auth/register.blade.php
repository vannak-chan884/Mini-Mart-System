<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account — Mini Mart POS</title>
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
            filter: blur(120px);
            pointer-events: none;
            z-index: 0;
        }

        .bg-glow-1 {
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(204, 0, 1, 0.15) 0%, transparent 70%);
            top: -150px;
            right: -100px;
            animation: drift1 12s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(0, 48, 135, 0.25) 0%, transparent 70%);
            bottom: -100px;
            left: -80px;
            animation: drift2 15s ease-in-out infinite alternate;
        }

        .bg-glow-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(244, 169, 0, 0.07) 0%, transparent 70%);
            top: 40%;
            left: 55%;
            animation: drift3 18s ease-in-out infinite alternate;
        }

        @keyframes drift1 {
            to {
                transform: translate(-40px, 60px);
            }
        }

        @keyframes drift2 {
            to {
                transform: translate(40px, -50px);
            }
        }

        @keyframes drift3 {
            to {
                transform: translate(-20px, 40px);
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
            padding: 48px 24px;
        }

        .card {
            width: 100%;
            max-width: 460px;
            animation: fadeUp 0.5s ease both;
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
            background: rgba(204, 0, 1, 0.1);
            border: 1px solid rgba(204, 0, 1, 0.25);
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 11px;
            font-weight: 600;
            color: #FCA5A5;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .card-eyebrow .dot {
            width: 5px;
            height: 5px;
            background: #FCA5A5;
            border-radius: 50%;
            animation: pulse 2s ease infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .card-title {
            font-family: 'Playfair Display', serif;
            font-size: 36px;
            font-weight: 900;
            color: #fff;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .card-title .accent {
            background: linear-gradient(135deg, var(--gold), #f97316);
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        }

        /* Two-column row */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 20px;
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

        input[type="text"],
        input[type="email"],
        input[type="password"] {
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
            border-color: rgba(0, 80, 200, 0.6);
            background: rgba(0, 48, 135, 0.1);
            box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.15);
        }

        input.has-error {
            border-color: rgba(248, 113, 113, 0.5);
            background: rgba(248, 113, 113, 0.05);
        }

        input.is-valid {
            border-color: rgba(34, 197, 94, 0.4);
            background: rgba(34, 197, 94, 0.04);
        }

        .input-error {
            margin-top: 6px;
            font-size: 12px;
            color: var(--error);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Password wrap */
        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 46px;
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

        /* Password strength */
        .strength-bar {
            display: flex;
            gap: 4px;
            margin-top: 8px;
        }

        .strength-segment {
            flex: 1;
            height: 3px;
            border-radius: 2px;
            background: var(--border);
            transition: background 0.3s ease;
        }

        .strength-segment.weak {
            background: var(--error);
        }

        .strength-segment.medium {
            background: var(--gold);
        }

        .strength-segment.strong {
            background: #22C55E;
        }

        .strength-label {
            font-size: 11px;
            color: var(--muted);
            margin-top: 5px;
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
            margin-top: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(0, 48, 135, 0.55);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Login link */
        .login-row {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }

        .login-row a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .login-row a:hover {
            opacity: 0.75;
        }

        /* Terms note */
        .terms-note {
            text-align: center;
            font-size: 11px;
            color: rgba(156, 163, 175, 0.6);
            margin-top: 16px;
            line-height: 1.6;
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

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
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
        <a href="{{ route('login') }}" class="nav-link">← Sign In</a>
    </nav>

    {{-- MAIN --}}
    <div class="main">
        <div class="card">

            {{-- Header --}}
            <div class="card-header">
                <div class="card-eyebrow">
                    <span class="dot"></span>
                    New Account
                </div>
                <h1 class="card-title">Join <span class="accent">Mini Mart</span></h1>
                <p class="card-subtitle">បង្កើតគណនីថ្មី</p>
                <div class="flag-bar">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Form Box --}}
            <div class="form-box">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    {{-- Name --}}
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}"
                            placeholder="Your full name" required autofocus autocomplete="name"
                            class="{{ $errors->has('name') ? 'has-error' : '' }}">
                        @error('name')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" required autocomplete="username"
                            class="{{ $errors->has('email') ? 'has-error' : '' }}">
                        @error('email')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Passwords side by side on desktop --}}
                    <div class="form-row">
                        {{-- Password --}}
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-wrap">
                                <input id="password" type="password" name="password" placeholder="••••••••" required
                                    autocomplete="new-password" oninput="checkStrength(this.value)"
                                    class="{{ $errors->has('password') ? 'has-error' : '' }}">
                                <button type="button" class="eye-toggle"
                                    onclick="togglePass('password', this)">👁</button>
                            </div>
                            <div class="strength-bar">
                                <div class="strength-segment" id="seg1"></div>
                                <div class="strength-segment" id="seg2"></div>
                                <div class="strength-segment" id="seg3"></div>
                                <div class="strength-segment" id="seg4"></div>
                            </div>
                            <div class="strength-label" id="strengthLabel"></div>
                            @error('password')
                                <div class="input-error">⚠ {{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="form-group">
                            <label for="password_confirmation">Confirm</label>
                            <div class="password-wrap">
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                    placeholder="••••••••" required autocomplete="new-password" oninput="checkMatch()"
                                    class="{{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                                <button type="button" class="eye-toggle"
                                    onclick="togglePass('password_confirmation', this)">👁</button>
                            </div>
                            @error('password_confirmation')
                                <div class="input-error">⚠ {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-submit">
                        Create Account →
                    </button>

                    <div class="login-row">
                        Already have an account?
                        <a href="{{ route('login') }}">Sign in here</a>
                    </div>

                    <div class="terms-note">
                        By creating an account you agree to use this system<br>for authorized Mini Mart operations only.
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
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }

        function checkStrength(val) {
            const segs = [document.getElementById('seg1'), document.getElementById('seg2'),
                document.getElementById('seg3'), document.getElementById('seg4')
            ];
            const label = document.getElementById('strengthLabel');
            segs.forEach(s => s.className = 'strength-segment');

            if (!val) {
                label.textContent = '';
                return;
            }

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            const cls = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
            const texts = ['', 'Weak', 'Weak', 'Medium', 'Strong'];
            const colors = {
                weak: '#F87171',
                medium: '#F4A900',
                strong: '#22C55E'
            };

            for (let i = 0; i < score; i++) segs[i].classList.add(cls);
            label.textContent = texts[score];
            label.style.color = colors[cls];
        }

        function checkMatch() {
            const pw = document.getElementById('password');
            const pc = document.getElementById('password_confirmation');
            if (pc.value && pw.value) {
                pc.className = pw.value === pc.value ? 'is-valid' : 'has-error';
            }
        }
    </script>

</body>

</html>
