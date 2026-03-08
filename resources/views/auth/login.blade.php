<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Mini Mart POS</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer:wght@300;400;600&family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --ink: #0D0D14;
            --deep: #111827;
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

        /* Noise */
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

        /* Glow blobs */
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
            background: radial-gradient(circle, rgba(0, 48, 135, 0.3) 0%, transparent 70%);
            top: -150px;
            left: -100px;
            animation: drift1 12s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(204, 0, 1, 0.1) 0%, transparent 70%);
            bottom: -100px;
            right: -50px;
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

        /* Main layout */
        .main {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 24px;
        }

        /* Card */
        .card {
            width: 100%;
            max-width: 440px;
            animation: fadeUp 0.5s ease both;
        }

        /* Header */
        .card-header {
            text-align: center;
            margin-bottom: 40px;
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

        .card-eyebrow .dot {
            width: 5px;
            height: 5px;
            background: var(--gold);
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

        /* Flag stripe */
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
        }

        /* Status alert */
        .status-alert {
            background: rgba(22, 163, 74, 0.1);
            border: 1px solid rgba(22, 163, 74, 0.3);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #86EFAC;
            margin-bottom: 24px;
        }

        /* Form groups */
        .form-group {
            margin-bottom: 22px;
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

        input[type="email"],
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

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: rgba(156, 163, 175, 0.5);
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: rgba(0, 80, 200, 0.6);
            background: rgba(0, 48, 135, 0.1);
            box-shadow: 0 0 0 3px rgba(0, 48, 135, 0.15);
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

        /* Password wrapper */
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
            padding: 0;
            font-size: 18px;
            line-height: 1;
            transition: color 0.2s;
        }

        .eye-toggle:hover {
            color: #fff;
        }

        /* Remember + forgot */
        .form-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember-label {
            display: flex;
            align-items: center;
            gap: 9px;
            cursor: pointer;
            text-transform: none;
            letter-spacing: 0;
            font-size: 13px;
            font-weight: 400;
            color: var(--muted);
        }

        .remember-label input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--blue);
            cursor: pointer;
        }

        .remember-label:hover {
            color: #fff;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: var(--gold);
        }

        /* Submit button */
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

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 24px 0 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider-text {
            font-size: 12px;
            color: var(--muted);
        }

        /* Register link */
        .register-row {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: var(--muted);
        }

        .register-row a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.2s;
        }

        .register-row a:hover {
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
                padding: 28px 24px;
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
        @if (Route::has('register'))
            <a href="{{ route('register') }}"
                style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border-radius:10px;font-size:14px;font-weight:500;font-family:'DM Sans',sans-serif;text-decoration:none;background:transparent;color:var(--muted);border:1px solid var(--border);transition:all 0.2s;"
                onmouseover="this.style.color='#fff';this.style.borderColor='rgba(255,255,255,0.2)'"
                onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
                Create account →
            </a>
        @endif
    </nav>

    {{-- MAIN --}}
    <div class="main">
        <div class="card">

            {{-- Header --}}
            <div class="card-header">
                <div class="card-eyebrow">
                    <span class="dot"></span>
                    POS System
                </div>
                <h1 class="card-title">Welcome <span class="accent">Back</span></h1>
                <p class="card-subtitle">ចូលទៅកាន់ប្រព័ន្ធគ្រប់គ្រង</p>
                <div class="flag-bar">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Form Box --}}
            <div class="form-box">

                {{-- Session status --}}
                @if (session('status'))
                    <div class="status-alert">✓ {{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Email --}}
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="you@example.com" required autofocus autocomplete="username"
                            class="{{ $errors->has('email') ? 'has-error' : '' }}">
                        @error('email')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrap">
                            <input id="password" type="password" name="password" placeholder="••••••••" required
                                autocomplete="current-password"
                                class="{{ $errors->has('password') ? 'has-error' : '' }}">
                            <button type="button" class="eye-toggle" onclick="togglePassword()"
                                id="eyeBtn">👁</button>
                        </div>
                        @error('password')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="form-footer-row">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" id="remember_me"
                                {{ old('remember') ? 'checked' : '' }}>
                            Remember me
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        @endif
                    </div>

                    {{-- Submit --}}
                    <button type="submit" class="btn-submit">
                        Sign In to POS →
                    </button>

                    @if (Route::has('register'))
                        <div class="divider">
                            <div class="divider-line"></div>
                            <span class="divider-text">New here?</span>
                            <div class="divider-line"></div>
                        </div>
                        <div class="register-row">
                            Don't have an account?
                            <a href="{{ route('register') }}">Create one free</a>
                        </div>
                    @endif

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
            const btn = document.getElementById('eyeBtn');
            if (input.type === 'password') {
                input.type = 'text';
                btn.textContent = '🙈';
            } else {
                input.type = 'password';
                btn.textContent = '👁';
            }
        }
    </script>

    {{-- Loading overlay --}}
    <div id="globalLoader"
        style="display:none; position:fixed; inset:0; z-index:9999;
           background:rgba(0,0,0,0.45); backdrop-filter:blur(3px);
           align-items:center; justify-content:center; flex-direction:column; gap:16px;">
        <div
            style="background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.12);
                border-radius:20px; padding:28px 36px;
                display:flex; flex-direction:column; align-items:center; gap:14px;
                backdrop-filter:blur(16px); box-shadow:0 8px 40px rgba(0,0,0,0.4);">
            <div style="position:relative; width:48px; height:48px;">
                <svg style="animation:spin 0.9s linear infinite; width:48px; height:48px;" viewBox="0 0 48 48"
                    fill="none">
                    <circle cx="24" cy="24" r="20" stroke="rgba(255,255,255,0.12)" stroke-width="4" />
                    <path d="M24 4 A20 20 0 0 1 44 24" stroke="url(#lg2)" stroke-width="4" stroke-linecap="round" />
                    <defs>
                        <linearGradient id="lg2" x1="24" y1="4" x2="44" y2="24"
                            gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#3B82F6" />
                            <stop offset="100%" stop-color="#003087" />
                        </linearGradient>
                    </defs>
                </svg>
                <span
                    style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:16px;">🏪</span>
            </div>
            <div style="text-align:center;">
                <div id="loaderText"
                    style="font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;color:rgba(255,255,255,0.9);">
                    Signing in…
                </div>
                <div
                    style="font-family:'DM Sans',sans-serif;font-size:11px;color:rgba(255,255,255,0.4);margin-top:3px;">
                    Mini Mart POS</div>
            </div>
            <div style="width:160px;height:3px;background:rgba(255,255,255,0.08);border-radius:999px;overflow:hidden;">
                <div id="loaderBar"
                    style="height:100%;width:0%;border-radius:999px;background:linear-gradient(90deg,#003087,#3B82F6);transition:width 0.4s ease;">
                </div>
            </div>
        </div>
    </div>
    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <script>
        (function() {
            const loader = document.getElementById('globalLoader');
            const loaderText = document.getElementById('loaderText');
            const loaderBar = document.getElementById('loaderBar');

            function showLoader(msg) {
                loader.style.display = 'flex';
                loaderText.textContent = msg || 'Loading…';
                loaderBar.style.transition = 'none';
                loaderBar.style.width = '0%';
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    loaderBar.style.transition = 'width 10s cubic-bezier(0.1,0.4,0.2,1)';
                    loaderBar.style.width = '85%';
                }));
            }

            function hideLoader() {
                try {
                    sessionStorage.removeItem('loaderMsg');
                } catch (e) {}
                loaderBar.style.transition = 'width 0.3s ease';
                loaderBar.style.width = '100%';
                setTimeout(() => {
                    loader.style.display = 'none';
                    loaderBar.style.width = '0%';
                }, 300);
            }

            // If we arrived here from another page (e.g. logout redirect), clear the loader
            try {
                const msg = sessionStorage.getItem('loaderMsg');
                if (msg) {
                    showLoader(msg);
                    setTimeout(hideLoader, 400);
                }
            } catch (e) {}

            // Show loader on login form submit
            document.addEventListener('submit', function(e) {
                showLoader('Signing in…');
                try {
                    sessionStorage.setItem('loaderMsg', 'Signing in…');
                } catch (e) {}
            }, true);
        })();
    </script>

</body>

</html>
