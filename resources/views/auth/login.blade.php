<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Organization and People Development</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-100: rgba(191, 219, 254, 0.7);
            --brand-600: #2563eb;
            --brand-700: #1d4ed8;
            --brand-950: #0b1737;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-500: #64748b;
            --slate-700: #334155;
            --slate-900: #0f172a;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: var(--slate-900);
            background:
                linear-gradient(135deg, rgba(11, 23, 55, 0.76), rgba(29, 78, 216, 0.38)),
                url('https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1600&q=80') center/cover no-repeat fixed;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            backdrop-filter: blur(14px);
            background:
                radial-gradient(circle at top left, rgba(96, 165, 250, 0.20), transparent 22%),
                radial-gradient(circle at bottom right, rgba(37, 99, 235, 0.16), transparent 24%);
            pointer-events: none;
        }

        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,0.10), transparent 10%),
                radial-gradient(circle at 78% 28%, rgba(255,255,255,0.08), transparent 12%),
                radial-gradient(circle at 64% 78%, rgba(255,255,255,0.08), transparent 11%);
            animation: shimmerDrift 18s linear infinite;
            pointer-events: none;
        }

        .wrap {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            align-items: center;
            padding: 18px;
        }

        .shell {
            width: min(1120px, 100%);
            margin: 0 auto;
            display: grid;
            gap: 18px;
        }

        .showcase,
        .login-card {
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.10);
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.16);
            backdrop-filter: blur(22px);
            overflow: hidden;
        }

        .showcase {
            position: relative;
        }

        .showcase {
            display: none;
            color: #fff;
            padding: 28px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .showcase h1 {
            margin: 18px 0 10px;
            max-width: 10ch;
            font-size: clamp(2.4rem, 6vw, 4.4rem);
            line-height: 0.95;
            letter-spacing: -0.04em;
        }

        .showcase p {
            margin: 0;
            max-width: 520px;
            color: rgba(255, 255, 255, 0.76);
            font-size: 15px;
            line-height: 1.6;
        }

        .showcase-grid {
            display: grid;
            gap: 12px;
            margin-top: 28px;
        }

        .motion-orb,
        .motion-ring,
        .motion-grid {
            position: absolute;
            pointer-events: none;
        }

        .motion-orb {
            top: 8%;
            right: 10%;
            width: 150px;
            height: 150px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(255,255,255,0.26), rgba(255,255,255,0.02) 70%);
            filter: blur(3px);
            animation: floatOrb 7s ease-in-out infinite;
        }

        .motion-ring {
            bottom: 12%;
            right: 14%;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,0.18);
            animation: pulseRing 6s ease-in-out infinite;
        }

        .motion-grid {
            left: 8%;
            bottom: 12%;
            width: 160px;
            height: 120px;
            background-image:
                linear-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.08) 1px, transparent 1px);
            background-size: 22px 22px;
            mask-image: linear-gradient(180deg, rgba(0,0,0,1), rgba(0,0,0,0));
            animation: driftGrid 10s ease-in-out infinite;
        }

        .showcase-chip {
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
            padding: 14px 16px;
        }

        .showcase-chip strong {
            display: block;
            font-size: 13px;
        }

        .showcase-chip span {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.66);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.88);
            border-color: rgba(226, 232, 240, 0.7);
        }

        .login-inner {
            padding: 24px 20px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
        }

        .brand-mark {
            display: grid;
            width: 44px;
            height: 44px;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand-600), var(--brand-700));
            color: #fff;
            font-size: 15px;
            font-weight: 800;
        }

        .brand-copy strong {
            display: block;
            font-size: 15px;
            color: var(--slate-900);
        }

        .brand-copy span {
            display: block;
            margin-top: 3px;
            font-size: 12px;
            color: var(--slate-500);
        }

        .heading h2 {
            margin: 0;
            font-size: clamp(1.6rem, 4vw, 2.2rem);
            line-height: 1.05;
            letter-spacing: -0.03em;
        }

        .heading p {
            margin: 10px 0 0;
            color: var(--slate-500);
            font-size: 14px;
            line-height: 1.55;
        }

        .alert {
            margin-top: 18px;
            border-radius: 18px;
            padding: 14px 16px;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 13px;
            font-weight: 700;
        }

        form {
            margin-top: 22px;
        }

        .field + .field {
            margin-top: 16px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--brand-700);
        }

        input {
            width: 100%;
            min-height: 52px;
            border-radius: 16px;
            border: 1px solid var(--slate-200);
            background: rgba(255, 255, 255, 0.92);
            padding: 0 16px;
            color: var(--slate-900);
            font: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.16s ease, box-shadow 0.16s ease, transform 0.16s ease;
        }

        input:focus {
            border-color: #93c5fd;
            box-shadow: 0 0 0 4px var(--brand-100);
            transform: translateY(-1px);
        }

        .submit {
            width: 100%;
            min-height: 54px;
            margin-top: 20px;
            border: 0;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--brand-600), var(--brand-700));
            color: #fff;
            font: inherit;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 18px 30px rgba(37, 99, 235, 0.24);
            transition: transform 0.16s ease, box-shadow 0.16s ease;
        }

        .submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 20px 34px rgba(37, 99, 235, 0.28);
        }

        .footer-note {
            margin-top: 18px;
            text-align: center;
            font-size: 12px;
            color: var(--slate-500);
        }

        @media (min-width: 960px) {
            .shell {
                grid-template-columns: minmax(0, 1.08fr) minmax(420px, 0.92fr);
                align-items: stretch;
            }

            .showcase {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                min-height: 680px;
            }

            .login-card {
                align-self: center;
            }

            .login-inner {
                padding: 34px 30px;
            }
        }

        @keyframes floatOrb {
            0%, 100% { transform: translate3d(0, 0, 0) scale(1); }
            50% { transform: translate3d(-12px, 14px, 0) scale(1.06); }
        }

        @keyframes pulseRing {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.08); opacity: 0.45; }
        }

        @keyframes driftGrid {
            0%, 100% { transform: translate3d(0, 0, 0); }
            50% { transform: translate3d(8px, -10px, 0); }
        }

        @keyframes shimmerDrift {
            0% { transform: translate3d(0, 0, 0); }
            50% { transform: translate3d(10px, -8px, 0); }
            100% { transform: translate3d(0, 0, 0); }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="shell">
            <section class="showcase" aria-hidden="true">
                <div class="motion-orb"></div>
                <div class="motion-ring"></div>
                <div class="motion-grid"></div>
                <div>
                    <span class="eyebrow">Internal Access</span>
                    <h1>Training Operations, refined.</h1>
                    <p>Manage employees, training registration, attendance, reporting, and department-level monitoring in one streamlined internal workspace.</p>
                </div>

                <div class="showcase-grid">
                    <div class="showcase-chip">
                        <strong>Organization and People Development</strong>
                        <span>Centralized internal training management</span>
                    </div>
                    <div class="showcase-chip">
                        <strong>Department-based access</strong>
                        <span>Right visibility for OPD and department users</span>
                    </div>
                    <div class="showcase-chip">
                        <strong>QR registration and attendance</strong>
                        <span>Fast participant flow with recorded timestamps</span>
                    </div>
                </div>
            </section>

            <section class="login-card">
                <div class="login-inner">
                    <div class="brand">
                        <div class="brand-mark">OP</div>
                        <div class="brand-copy">
                            <strong>Organization and People Development</strong>
                            <span>Internal training platform</span>
                        </div>
                    </div>

                    <div class="heading">
                        <h2>Sign in to continue</h2>
                        <p>Use your internal account to access dashboard insights, training administration, and department-based monitoring.</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.attempt') }}">
                        @csrf
                        <div class="field">
                            <label for="email">Email Address</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Enter your email address" required autofocus>
                        </div>
                        <div class="field">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" placeholder="Enter your password" required>
                        </div>
                        <button type="submit" class="submit">Sign In</button>
                    </form>

                    <div class="footer-note">Secure internal access for Organization and People Development.</div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
