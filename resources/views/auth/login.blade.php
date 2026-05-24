<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Posgrado</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(circle at 20% 80%, rgba(99,102,241,0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(139,92,246,0.1) 0%, transparent 50%);
            pointer-events: none;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 0 20px;
            position: relative;
            z-index: 1;
        }
        .login-card {
            background: rgba(30,41,59,0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.05);
        }
        .logo-section { text-align: center; margin-bottom: 32px; }
        .logo-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 40px -10px rgba(99,102,241,0.5);
        }
        .logo-icon svg { width: 36px; height: 36px; fill: white; }
        .logo-title { color: #f8fafc; font-size: 28px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.5px; }
        .logo-subtitle { color: #94a3b8; font-size: 15px; font-weight: 400; }
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            margin: 28px 0;
        }
        .floating-group { position: relative; margin-bottom: 20px; }
        .floating-input {
            width: 100%;
            padding: 20px 16px 8px 48px;
            background: rgba(15,23,42,0.6);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            color: #f1f5f9;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
            height: 56px;
        }
        .floating-input::placeholder { color: transparent; }
        .floating-input:focus {
            border-color: #6366f1;
            background: rgba(15,23,42,0.8);
            box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
        }
        .floating-label {
            position: absolute;
            left: 48px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-size: 15px;
            font-weight: 400;
            pointer-events: none;
            transition: all 0.2s ease;
            padding: 0 4px;
        }
        .floating-input:focus ~ .floating-label,
        .floating-input:not(:placeholder-shown) ~ .floating-label {
            top: 12px;
            transform: translateY(0);
            font-size: 11px;
            color: #6366f1;
            font-weight: 500;
        }
        .floating-input:not(:focus):not(:placeholder-shown) ~ .floating-label { color: #94a3b8; }
        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
            z-index: 2;
        }
        .floating-input:focus ~ .input-icon { color: #6366f1; }
        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
        }
        .password-toggle:hover { color: #94a3b8; }
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            border-radius: 14px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px -10px rgba(59,130,246,0.4);
            margin-top: 8px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 15px 35px -10px rgba(59,130,246,0.5); }
        .btn-login:active { transform: translateY(0); }
        .btn-login svg { width: 20px; height: 20px; }
        .alert-error {
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.2);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .remember-forgot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            margin-top: 4px;
        }
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .remember-me input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: #3b82f6;
            cursor: pointer;
        }
        .remember-me span { color: #94a3b8; font-size: 13px; }
        .forgot-link {
            color: #3b82f6;
            font-size: 13px;
            text-decoration: none;
            transition: color 0.2s;
        }
        .forgot-link:hover { color: #60a5fa; }
        .register-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .register-link p { color: #94a3b8; font-size: 14px; }
        .register-link a {
            color: #3b82f6;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .register-link a:hover { color: #60a5fa; }
        @media (max-width: 480px) {
            .login-card { padding: 36px 24px; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 3L1 9L5 11.18V17.18L12 21L19 17.18V11.18L21 10.09V17H23V9L12 3ZM18.82 9L12 12.72L5.18 9L12 5.28L18.82 9ZM17 15.99L12 18.72L7 15.99V12.27L12 15L17 12.27V15.99Z"/>
                    </svg>
                </div>
                <h1 class="logo-title">Sistema de Posgrado</h1>
                <p class="logo-subtitle">Gestión Académica Institucional</p>
            </div>

            <div class="divider"></div>

            @if ($errors->any())
                <div class="alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="floating-group">
                    <span class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                        </svg>
                    </span>
                    <input type="email" id="email" name="email" class="floating-input" placeholder="Correo Electrónico"
                           value="{{ old('email') }}" required autofocus>
                    <label class="floating-label" for="email">Correo Electrónico</label>
                </div>

                <div class="floating-group">
                    <span class="input-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" class="floating-input" placeholder="Contraseña" required>
                    <label class="floating-label" for="password">Contraseña</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <div class="remember-forgot">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Recordarme</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    Iniciar Sesión
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </button>
            </form>
            {{-- Sin registro público. Contacta al administrador. --}}

        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>
</body>
</html>
