<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion — Heritage Modernist Admin</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=block" rel="stylesheet">

    <style>
        /* Prevent Material Symbols FOUT */
        html:not(.mso-loaded) .material-symbols-outlined {
            visibility: hidden !important;
            opacity: 0 !important;
            display: inline-block;
            width: 1em;
        }

        :root {
            --green:       #1B6B3A;
            --green-dark:  #134d2b;
            --green-mid:   #2E8B57;
            --green-soft:  #e8f5ed;
            --green-xsoft: #f0faf4;
            --gold:        #D4920A;
            --gold-soft:   #fff8e6;
            --bg:          #f0f4f1;
            --card:        #ffffff;
            --border:      #dde8e0;
            --text:        #1a1a1a;
            --text2:       #4a5568;
            --text3:       #9ba8a3;
            --red:         #e53e3e;
            --red-soft:    #fff5f5;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: stretch;
        }

        a { text-decoration: none; color: inherit; }

        /* ── LEFT PANEL ── */
        .login-left {
            width: 42%;
            background: linear-gradient(155deg, #0d3320 0%, #1B6B3A 45%, #2E8B57 100%);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
            overflow: hidden;
        }

        /* Decorative circles */
        .login-left::before {
            content: '';
            position: absolute;
            width: 450px; height: 450px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            top: -100px; right: -140px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,255,255,0.04);
            bottom: -80px; left: -80px;
        }

        .login-left-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
        }
        .brand-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .brand-icon .material-symbols-outlined {
            color: #fff;
            font-size: 24px;
            font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
        }
        .brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            line-height: 1.1;
        }
        .brand-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .login-left-content {
            position: relative;
            z-index: 2;
        }
        .login-left-content h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 36px;
            font-weight: 800;
            color: #fff;
            line-height: 1.2;
            margin-bottom: 16px;
        }
        .login-left-content h1 span {
            color: #D4920A;
        }
        .login-left-content p {
            font-size: 15px;
            color: rgba(255,255,255,0.7);
            line-height: 1.7;
            max-width: 340px;
        }

        /* Feature list */
        .login-features {
            list-style: none;
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .login-features li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13.5px;
            color: rgba(255,255,255,0.85);
            font-weight: 500;
        }
        .login-feature-icon {
            width: 32px; height: 32px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .login-feature-icon .material-symbols-outlined {
            font-size: 17px;
            color: rgba(255,255,255,0.9);
            font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
        }

        .login-left-footer {
            position: relative;
            z-index: 2;
            font-size: 12px;
            color: rgba(255,255,255,0.4);
        }

        /* ── RIGHT PANEL ── */
        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
        }

        .login-box {
            width: 100%;
            max-width: 420px;
        }

        .login-box-header {
            margin-bottom: 36px;
        }
        .login-box-header h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--text);
            margin-bottom: 6px;
        }
        .login-box-header p {
            font-size: 14px;
            color: var(--text2);
        }

        /* ── FORM ── */
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text2);
            margin-bottom: 7px;
        }
        .form-input-wrap {
            position: relative;
        }
        .form-input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 19px;
            color: var(--text3);
            pointer-events: none;
        }
        .form-input {
            width: 100%;
            background: var(--bg);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px 12px 42px;
            font-size: 14px;
            color: var(--text);
            font-family: inherit;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .form-input::placeholder { color: var(--text3); }
        .form-input:focus {
            border-color: var(--green);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(27,107,58,0.1);
        }
        .form-input.is-invalid {
            border-color: var(--red);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(229,62,62,0.08);
        }

        /* Password toggle */
        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--text3);
            display: flex; align-items: center;
            transition: color 0.15s;
        }
        .pw-toggle:hover { color: var(--green); }
        .pw-toggle .material-symbols-outlined { font-size: 19px; }

        /* Error message */
        .form-error {
            font-size: 12px;
            color: var(--red);
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .form-error .material-symbols-outlined {
            font-size: 14px;
            font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
        }

        /* Remember + Forgot */
        .form-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .form-check {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .form-check input[type="checkbox"] {
            width: 16px; height: 16px;
            border-radius: 4px;
            accent-color: var(--green);
            cursor: pointer;
        }
        .form-check-label {
            font-size: 13px;
            color: var(--text2);
            font-weight: 500;
            user-select: none;
        }
        .form-forgot {
            font-size: 13px;
            color: var(--green);
            font-weight: 600;
            transition: color 0.15s;
        }
        .form-forgot:hover { color: var(--green-dark); }

        /* Submit button */
        .login-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            background: var(--green);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            position: relative;
            overflow: hidden;
        }
        .login-btn .material-symbols-outlined {
            font-size: 19px;
            font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
        }
        .login-btn:hover {
            background: var(--green-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(27,107,58,0.3);
        }
        .login-btn:active { transform: translateY(0); }

        /* Ripple on button */
        .login-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0);
            transition: background 0.15s;
        }
        .login-btn:active::after { background: rgba(255,255,255,0.08); }

        /* Alert banner */
        .login-alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 24px;
            animation: fadeInDown 0.3s ease;
        }
        .login-alert.error   { background: var(--red-soft); color: var(--red); border: 1px solid #fed7d7; }
        .login-alert.success { background: var(--green-soft); color: var(--green); border: 1px solid #b7dfc6; }
        .login-alert .material-symbols-outlined {
            font-size: 18px;
            flex-shrink: 0;
            font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
        }

        /* Divider */
        .login-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0;
        }
        .login-divider::before,
        .login-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .login-divider span {
            font-size: 12px;
            color: var(--text3);
            font-weight: 500;
            white-space: nowrap;
        }

        /* Security badge */
        .security-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
            font-size: 12px;
            color: var(--text3);
        }
        .security-badge .material-symbols-outlined {
            font-size: 15px;
            color: var(--green);
            font-variation-settings: 'FILL' 1,'wght' 400,'GRAD' 0,'opsz' 24;
        }

        /* Form loading */
        .login-btn.loading { color: transparent !important; pointer-events: none; }
        .login-btn.loading::before {
            content: '';
            position: absolute;
            inset: 0; margin: auto;
            width: 20px; height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 860px) {
            .login-left { display: none; }
            .login-right { padding: 32px 20px; }
        }

        /* ── ANIMATIONS ── */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .login-box { animation: fadeInUp 0.4s ease; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

    <!-- ══ LEFT PANEL ══ -->
    <div class="login-left">
        <div class="login-left-brand">
            <div class="brand-icon">
                <span class="material-symbols-outlined">storefront</span>
            </div>
            <div>
                <div class="brand-name">Heritage Modernist</div>
                <div class="brand-sub">Marketplace Admin</div>
            </div>
        </div>

        <div class="login-left-content">
            <h1>Gérez votre<br><span>Écosystème</span><br>Marketplace.</h1>
            <p>
                Plateforme d'administration complète pour superviser vendeurs,
                produits, commandes et la communauté du marché Heritage Modernist.
            </p>

            <ul class="login-features">
                <li>
                    <div class="login-feature-icon">
                        <span class="material-symbols-outlined">dashboard</span>
                    </div>
                    Tableau de bord en temps réel
                </li>
                <li>
                    <div class="login-feature-icon">
                        <span class="material-symbols-outlined">storefront</span>
                    </div>
                    Gestion des vendeurs et boutiques
                </li>
                <li>
                    <div class="login-feature-icon">
                        <span class="material-symbols-outlined">inventory_2</span>
                    </div>
                    Modération automatisée des produits
                </li>
                <li>
                    <div class="login-feature-icon">
                        <span class="material-symbols-outlined">analytics</span>
                    </div>
                    Rapports et statistiques avancés
                </li>
            </ul>
        </div>

        <div class="login-left-footer">
            © {{ date('Y') }} Heritage Modernist Marketplace. Tous droits réservés.
        </div>
    </div>

    <!-- ══ RIGHT PANEL ══ -->
    <div class="login-right">
        <div class="login-box">

            <div class="login-box-header">
                <h2>Connexion Admin</h2>
                <p>Accédez à votre espace d'administration sécurisé</p>
            </div>

            {{-- ── Erreur générale ── --}}
            @if(session('error'))
                <div class="login-alert error">
                    <span class="material-symbols-outlined">error</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="login-alert error">
                    <span class="material-symbols-outlined">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="login-alert success">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- ── LOGIN FORM ── --}}
            <form method="POST" action="{{ route('admin.login.submit') }}" id="loginForm">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label class="form-label" for="email">Adresse e-mail</label>
                    <div class="form-input-wrap">
                        <span class="material-symbols-outlined form-input-icon">mail</span>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                            placeholder="admin@heritage.bj"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                    </div>
                    @error('email')
                        <div class="form-error">
                            <span class="material-symbols-outlined">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label class="form-label" for="password">Mot de passe</label>
                    <div class="form-input-wrap">
                        <span class="material-symbols-outlined form-input-icon">lock</span>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                            placeholder="••••••••"
                            autocomplete="current-password"
                            required
                        >
                        <button type="button" class="pw-toggle" id="pwToggle" title="Afficher le mot de passe">
                            <span class="material-symbols-outlined" id="pwIcon">visibility</span>
                        </button>
                    </div>
                    @error('password')
                        <div class="form-error">
                            <span class="material-symbols-outlined">error</span>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Remember + Forgot --}}
                <div class="form-row">
                    <label class="form-check">
                        <input type="checkbox" name="remember" id="remember">
                        <span class="form-check-label">Se souvenir de moi</span>
                    </label>
                    <a href="#" class="form-forgot">Mot de passe oublié ?</a>
                </div>

                {{-- Submit --}}
                <button type="submit" class="login-btn" id="loginBtn">
                    <span class="material-symbols-outlined">login</span>
                    Se connecter
                </button>
            </form>

            {{-- Security Badge --}}
            <div class="security-badge">
                <span class="material-symbols-outlined">verified_user</span>
                Connexion sécurisée · Espace réservé aux administrateurs
            </div>

        </div>
    </div>

    <script>
        /* ─── Password toggle ─── */
        const pwToggle = document.getElementById('pwToggle');
        const pwInput  = document.getElementById('password');
        const pwIcon   = document.getElementById('pwIcon');
        pwToggle.addEventListener('click', () => {
            const isHidden = pwInput.type === 'password';
            pwInput.type = isHidden ? 'text' : 'password';
            pwIcon.textContent = isHidden ? 'visibility_off' : 'visibility';
        });

        /* ─── Loading state on submit ─── */
        document.getElementById('loginForm').addEventListener('submit', () => {
            document.getElementById('loginBtn').classList.add('loading');
        });

        /* ─── Material Symbols FOUT fix ─── */
        document.fonts.ready.then(() => document.documentElement.classList.add('mso-loaded'));
        setTimeout(() => document.documentElement.classList.add('mso-loaded'), 1500);
    </script>

</body>
</html>
