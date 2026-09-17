<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#bc000a" />
    <title>Masuk — Ayam Geprek Rejo</title>
    <meta name="description" content="Login ke sistem manajemen Ayam Geprek Rejo — Lezat, Gurih, Halal">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            margin: 0;
            min-height: 100dvh;
            background-color: #bc000a;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            overflow-x: hidden;
        }

        /* ── Gelombang merah di bagian atas ─────────────────── */
        .hero-section {
            width: 100%;
            max-width: 420px;
            background: #bc000a;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3rem 1.5rem 5rem;
            position: relative;
            z-index: 1;
        }

        /* Logo langsung tanpa bingkai */
        .logo-img {
            width: 160px;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 6px 18px rgba(0,0,0,0.35));
            margin-bottom: 0.75rem;
        }

        /* Brand name */
        .brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.75rem;
            font-weight: 800;
            color: #fabd00;
            text-align: center;
            letter-spacing: -0.01em;
            line-height: 1.2;
            margin: 0;
        }
        .brand-tagline {
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            color: rgba(255,255,255,0.75);
            margin-top: 0.375rem;
            letter-spacing: 0.04em;
        }

        /* ── Card putih yang numpuk di atas merah ─────────── */
        .card-wrapper {
            width: 100%;
            max-width: 420px;
            flex: 1;
            background: white;
            border-radius: 2rem 2rem 0 0;
            margin-top: -3rem;
            padding: 2rem 1.75rem 2.5rem;
            position: relative;
            z-index: 2;
            box-shadow: 0 -4px 40px rgba(0,0,0,0.12);
            min-height: calc(100dvh - 280px);
        }

        /* Garis dekoratif di atas card */
        .card-wrapper::before {
            content: '';
            display: block;
            width: 40px;
            height: 4px;
            background: #e0e3e8;
            border-radius: 9999px;
            margin: 0 auto 1.75rem;
        }

        /* ── Form elements ─────────────────────────────────── */
        .field-label {
            font-family: 'Inter', sans-serif;
            display: block;
            font-size: 0.6875rem;
            font-weight: 700;
            color: #1a1a1a;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.5rem;
        }
        .input-field {
            font-family: 'Inter', sans-serif;
            width: 100%;
            background: #f1f3f5;
            border: 1.5px solid #e0e3e8;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #181c20;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .input-field::placeholder { color: #9ca3af; }
        .input-field:hover { border-color: #936e69; }
        .input-field:focus {
            border-color: #bc000a;
            box-shadow: 0 0 0 3px rgba(188,0,10,0.1);
            background: #fff;
        }
        .input-field.error { border-color: #ba1a1a; }

        .input-wrap { position: relative; }
        .input-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #936e69;
            width: 1rem;
            height: 1rem;
        }
        .input-field.has-icon { padding-left: 2.5rem; }

        /* Toggle password */
        .toggle-pw {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #936e69;
            padding: 0;
            display: flex;
            align-items: center;
        }
        .toggle-pw:hover { color: #bc000a; }

        /* ── Login button ─────────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: 0.9rem 1rem;
            background: #fabd00;
            color: #1a1000;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1rem;
            font-weight: 800;
            border: none;
            border-radius: 0.75rem;
            cursor: pointer;
            letter-spacing: 0.02em;
            box-shadow: 0 4px 16px rgba(250,189,0,0.45);
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
        }
        .btn-login:hover {
            background: #f5b400;
            box-shadow: 0 6px 20px rgba(250,189,0,0.55);
        }
        .btn-login:active { transform: scale(0.98); }

        /* ── Remember me ───────────────────────────────────── */
        .remember-label {
            display: flex;
            align-items: center;
            gap: 0.625rem;
            cursor: pointer;
        }
        .remember-label input[type="checkbox"] {
            width: 1.125rem;
            height: 1.125rem;
            border: 1.5px solid #d1d5db;
            border-radius: 0.375rem;
            accent-color: #bc000a;
            cursor: pointer;
        }
        .remember-label span {
            font-family: 'Inter', sans-serif;
            font-size: 0.8125rem;
            color: #374151;
        }

        /* ── Alert boxes ──────────────────────────────────── */
        .alert-success {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            margin-bottom: 1.25rem;
        }
        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            font-size: 0.875rem;
            font-family: 'Inter', sans-serif;
            margin-bottom: 1.25rem;
        }

        /* ── Footer ───────────────────────────────────────── */
        .footer-text {
            font-family: 'Inter', sans-serif;
            font-size: 0.75rem;
            color: #9ca3af;
            text-align: center;
            margin-top: 2rem;
        }

        /* Full page merah di bawah card */
        .page-bg-red {
            position: fixed;
            inset: 0;
            background: #bc000a;
            z-index: 0;
        }

        /* Decorative pattern di hero */
        .hero-dots {
            position: absolute;
            inset: 0;
            opacity: 0.06;
            background-image: radial-gradient(circle, #fff 1px, transparent 1px);
            background-size: 24px 24px;
            pointer-events: none;
        }
    </style>
</head>

<body>
    {{-- Background merah penuh --}}
    <div class="page-bg-red"></div>

    {{-- Hero section: logo + nama --}}
    <div class="hero-section" style="position:relative; z-index:2;">
        <div class="hero-dots"></div>

        {{-- Logo langsung (transparan, tanpa bulatan) --}}
        <img src="{{ asset('images/logo.png') }}" alt="Ayam Geprek Rejo" class="logo-img">

        {{-- Brand --}}
        <h1 class="brand-name">Ayam Geprek Rejo</h1>
        <p class="brand-tagline">Lezat, Gurih, Halal</p>
    </div>

    {{-- Card putih login --}}
    <div class="card-wrapper">

        {{-- Session status --}}
        @if (session('status'))
            <div class="alert-success">
                <svg style="width:1rem;height:1rem;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- Error --}}
        @if ($errors->any())
            <div class="alert-error">
                <svg style="width:1rem;height:1rem;flex-shrink:0;margin-top:2px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    @foreach ($errors->all() as $error)
                        <p style="margin:0 0 2px">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" style="display:flex;flex-direction:column;gap:1.25rem;">
            @csrf

            {{-- Account (Email) --}}
            <div>
                <label for="email" class="field-label">Account</label>
                <div class="input-wrap">
                    <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <input
                        id="email" name="email" type="email"
                        value="{{ old('email') }}"
                        required autofocus autocomplete="email"
                        placeholder="atminrejo@gmail.com"
                        class="input-field has-icon {{ $errors->has('email') ? 'error' : '' }}"
                    >
                </div>
            </div>

            {{-- Password --}}
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem;">
                    <label for="password" class="field-label" style="margin-bottom:0;">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-family:Inter,sans-serif;font-size:0.6875rem;font-weight:600;color:#bc000a;text-decoration:none;">
                            Lupa password?
                        </a>
                    @endif
                </div>
                <div class="input-wrap">
                    <svg class="input-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <input
                        id="password" name="password" type="password"
                        required autocomplete="current-password"
                        placeholder="••••••••"
                        class="input-field has-icon {{ $errors->has('password') ? 'error' : '' }}"
                        style="padding-right:2.75rem;"
                    >
                    <button type="button" class="toggle-pw"
                        onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password'; this.querySelector('.eye-on').classList.toggle('hidden'); this.querySelector('.eye-off').classList.toggle('hidden');">
                        <svg class="eye-on" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg class="eye-off hidden" style="width:1rem;height:1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Remember me --}}
            <label class="remember-label" for="remember">
                <input id="remember" name="remember" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember me</span>
            </label>

            {{-- Submit --}}
            <button type="submit" class="btn-login">Login</button>
        </form>

        <p class="footer-text">© {{ date('Y') }} Ayam Geprek Rejo — Lezat, Gurih, Halal</p>
    </div>

    @fluxScripts
</body>
</html>
