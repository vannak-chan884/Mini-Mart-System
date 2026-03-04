<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — Mini Mart POS</title>
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
            background: radial-gradient(circle, rgba(22, 163, 74, 0.14) 0%, transparent 70%);
            top: -150px;
            right: -80px;
            animation: drift1 13s ease-in-out infinite alternate;
        }

        .bg-glow-2 {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, rgba(0, 48, 135, 0.2) 0%, transparent 70%);
            bottom: -100px;
            left: -60px;
            animation: drift2 17s ease-in-out infinite alternate;
        }

        .bg-glow-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(244, 169, 0, 0.07) 0%, transparent 70%);
            top: 50%;
            left: 50%;
            animation: drift3 19s ease-in-out infinite alternate;
        }

        @keyframes drift1 {
            to {
                transform: translate(-40px, 70px);
            }
        }

        @keyframes drift2 {
            to {
                transform: translate(40px, -50px);
            }
        }

        @keyframes drift3 {
            to {
                transform: translate(30px, -40px);
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

        .step-badge {
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
            letter-spacing: 0.4px;
        }

        /* Main */
        .main {
            position: relative;
            z-index: 1;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px 24px;
        }

        .card {
            width: 100%;
            max-width: 440px;
            animation: fadeUp 0.5s ease both;
        }

        /* Key icon */
        .key-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 28px;
        }

        .key-icon {
            position: relative;
            width: 76px;
            height: 76px;
            background: linear-gradient(135deg, rgba(22, 163, 74, 0.2), rgba(22, 163, 74, 0.06));
            border: 1px solid rgba(22, 163, 74, 0.3);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
            box-shadow: 0 0 40px rgba(22, 163, 74, 0.15);
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-7px) rotate(-5deg);
            }
        }

        /* Corner dots */
        .key-icon::before,
        .key-icon::after {
            content: '';
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: rgba(22, 163, 74, 0.5);
        }

        .key-icon::before {
            top: -3px;
            right: -3px;
        }

        .key-icon::after {
            bottom: -3px;
            left: -3px;
        }

        /* Steps */
        .steps {
            display: flex;
            gap: 0;
            margin-bottom: 32px;
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

        .step.done .step-num {
            background: rgba(22, 163, 74, 0.2);
            border-color: rgba(22, 163, 74, 0.4);
            color: #86EFAC;
        }

        .step.done::after {
            background: rgba(22, 163, 74, 0.25);
        }

        .step.active .step-num {
            background: linear-gradient(135deg, #15803D, #16A34A);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 0 12px rgba(22, 163, 74, 0.4);
        }

        .step-label {
            font-size: 10px;
            color: var(--muted);
            text-align: center;
            letter-spacing: 0.3px;
        }

        .step.done .step-label {
            color: #86EFAC;
        }

        .step.active .step-label {
            color: #fff;
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
            margin-bottom: 8px;
        }

        .card-title .accent {
            background: linear-gradient(135deg, #4ADE80, #16A34A);
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
            background: linear-gradient(90deg, transparent, rgba(22, 163, 74, 0.2), transparent);
        }

        /* Form */
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

        input[type="email"]:read-only {
            background: rgba(255, 255, 255, 0.02);
            color: var(--muted);
            cursor: default;
        }

        input::placeholder {
            color: rgba(156, 163, 175, 0.45);
        }

        input:focus {
            border-color: rgba(22, 163, 74, 0.5);
            background: rgba(22, 163, 74, 0.05);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
        }

        input[type="email"]:read-only:focus {
            border-color: var(--border);
            background: rgba(255, 255, 255, 0.02);
            box-shadow: none;
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

        /* Email readonly hint */
        .field-hint {
            margin-top: 5px;
            font-size: 11px;
            color: rgba(156, 163, 175, 0.5);
            display: flex;
            align-items: center;
            gap: 4px;
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

        /* Strength bar */
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

        /* Divider */
        .form-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0 20px;
        }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #15803D 0%, #16A34A 100%);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 20px rgba(22, 163, 74, 0.3);
            letter-spacing: 0.3px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(22, 163, 74, 0.45);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

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
        <div class="step-badge">✅ Step 3 of 3</div>
    </nav>

    {{-- MAIN --}}
    <div class="main">
        <div class="card">

            {{-- Key icon --}}
            <div class="key-wrap">
                <div class="key-icon">🗝️</div>
            </div>

            {{-- Header --}}
            <div class="card-header">
                <div class="card-eyebrow">
                    <span class="dot"></span>
                    Final Step
                </div>
                <h1 class="card-title">Set New <span class="accent">Password</span></h1>
                <p class="card-subtitle">កំណត់លេខសម្ងាត់ថ្មី</p>
                <div class="flag-bar">
                    <span></span><span></span><span></span>
                </div>
            </div>

            {{-- Form box --}}
            <div class="form-box">

                {{-- Steps indicator --}}
                <div class="steps">
                    <div class="step done">
                        <div class="step-num">✓</div>
                        <div class="step-label">Email sent</div>
                    </div>
                    <div class="step done">
                        <div class="step-num">✓</div>
                        <div class="step-label">Link opened</div>
                    </div>
                    <div class="step active">
                        <div class="step-num">3</div>
                        <div class="step-label">New password</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('password.store') }}">
                    @csrf
                    {{-- <input type="hidden" name="token" value="{{ $request->route('token') }}"> --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    {{-- Email (read-only) --}}
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}"
                            required autofocus autocomplete="username" readonly
                            class="{{ $errors->has('email') ? 'has-error' : '' }}">
                        <div class="field-hint">🔒 Verified email — cannot be changed</div>
                        @error('email')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-divider"></div>

                    {{-- New password --}}
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <div class="password-wrap">
                            <input id="password" type="password" name="password" placeholder="Choose a strong password"
                                required autocomplete="new-password" oninput="checkStrength(this.value)"
                                class="{{ $errors->has('password') ? 'has-error' : '' }}">
                            <button type="button" class="eye-toggle" onclick="togglePass('password', this)">👁</button>
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

                    {{-- Confirm password --}}
                    <div class="form-group">
                        <label for="password_confirmation">Confirm New Password</label>
                        <div class="password-wrap">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                placeholder="Repeat your new password" required autocomplete="new-password"
                                oninput="checkMatch()"
                                class="{{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                            <button type="button" class="eye-toggle"
                                onclick="togglePass('password_confirmation', this)">👁</button>
                        </div>
                        <div class="strength-label" id="matchLabel"></div>
                        @error('password_confirmation')
                            <div class="input-error">⚠ {{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">
                        <span>✅</span> Reset Password
                    </button>

                    <div class="login-row">
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
            const segs = ['seg1', 'seg2', 'seg3', 'seg4'].map(id => document.getElementById(id));
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
            label.style.color = colors[cls] || '';
        }

        function checkMatch() {
            const pw = document.getElementById('password');
            const pc = document.getElementById('password_confirmation');
            const lbl = document.getElementById('matchLabel');
            if (!pc.value) {
                lbl.textContent = '';
                pc.className = '';
                return;
            }
            if (pw.value === pc.value) {
                pc.className = 'is-valid';
                lbl.textContent = '✓ Passwords match';
                lbl.style.color = '#22C55E';
            } else {
                pc.className = 'has-error';
                lbl.textContent = '✗ Passwords do not match';
                lbl.style.color = '#F87171';
            }
        }
    </script>

</body>

</html>
