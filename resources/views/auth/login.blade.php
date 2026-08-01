<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ASR FARM</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            font-family: 'Inter', sans-serif;
            color: #1f2937;
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1000px;
            min-height: 600px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            margin: 2rem;
            animation: fadeIn 0.6s ease-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Left Panel - Branding */
        .login-sidebar {
            flex: 1;
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            color: #ffffff;
        }

        .login-sidebar::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.03) 0%, transparent 60%);
            transform: rotate(30deg);
            pointer-events: none;
        }
        
        .login-sidebar::after {
            content: '';
            position: absolute;
            bottom: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            border: 40px solid rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            z-index: 1;
        }

        .brand-header img {
            height: 48px;
            filter: brightness(0) invert(1);
        }

        .brand-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0;
        }

        .brand-content {
            z-index: 1;
        }

        .brand-content h2 {
            font-size: 2.25rem;
            font-weight: 300;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .brand-content h2 strong {
            font-weight: 700;
            display: block;
        }

        .brand-content p {
            font-size: 1rem;
            opacity: 0.8;
            line-height: 1.6;
            max-width: 85%;
        }

        .brand-footer {
            z-index: 1;
            font-size: 0.85rem;
            opacity: 0.6;
            letter-spacing: 0.5px;
        }

        /* Right Panel - Form */
        .login-form-area {
            flex: 1;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }

        .login-form-area h3 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        .login-form-area p.text-muted {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 2.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 1.25rem;
            transition: color 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.95rem;
            color: #1f2937;
            background: #f9fafb;
            transition: all 0.2s;
            box-sizing: border-box;
            font-family: inherit;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #059669;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }

        .input-wrapper input:focus + i,
        .input-wrapper input:not(:placeholder-shown) ~ i {
            color: #059669;
        }

        .remember-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }

        .remember-flex label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: #4b5563;
        }

        .remember-flex input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #059669;
            cursor: pointer;
        }

        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            background: #059669;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        .alert-error {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 4px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-credentials {
            margin-top: 2rem;
            padding: 1.25rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .demo-credentials .title {
            color: #475569;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .demo-grid {
            display: grid;
            gap: 0.5rem;
        }

        .demo-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #334155;
        }

        .demo-item span.role {
            font-weight: 600;
            color: #059669;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }
            .login-sidebar {
                padding: 3rem 2rem;
            }
            .login-form-area {
                padding: 3rem 2rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Sidebar Branding -->
        <div class="login-sidebar">
            <div class="brand-header">
                <img src="{{ asset('images/logo-asr.png') }}" alt="ASR Logo">
                <h1>ASR FARM</h1>
            </div>

            <div class="brand-content">
                <h2>Smart <strong>Hydroponic</strong> Management</h2>
                <p>Platform profesional untuk memonitoring dan mengelola infrastruktur greenhouse secara terintegrasi dan real-time.</p>
            </div>

            <div class="brand-footer">
                &copy; {{ date('Y') }} ASR FARM Internal System. All rights reserved.
            </div>
        </div>

        <!-- Login Form -->
        <div class="login-form-area">
            <h3>Selamat Datang</h3>
            <p class="text-muted">Masuk ke portal administrasi Anda</p>

            @if($errors->any())
                <div class="alert-error">
                    <i class="ph ph-warning-circle" style="font-size: 1.25rem;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrapper">
                        <i class="ph ph-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required placeholder="superadmin@asrfarm.com" autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi</label>
                    <div class="input-wrapper">
                        <i class="ph ph-lock-key"></i>
                        <input type="password" id="password" name="password"
                               required placeholder="••••••••" autocomplete="current-password">
                    </div>
                </div>

                <div class="remember-flex">
                    <label for="remember">
                        <input type="checkbox" name="remember" id="remember">
                        Ingat sesi saya
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    Masuk Portal <i class="ph ph-arrow-right"></i>
                </button>
            </form>

            <div class="demo-credentials">
                <div class="title"><i class="ph ph-info"></i> Informasi Akses (Sandi: ASRFarm@2026)</div>
                <div class="demo-grid">
                    <div class="demo-item">
                        <span class="role">Super Admin</span>
                        <span>superadmin@asrfarm.com</span>
                    </div>
                    <div class="demo-item">
                        <span class="role">Admin</span>
                        <span>admin@asrfarm.com</span>
                    </div>
                    <div class="demo-item">
                        <span class="role">Viewer</span>
                        <span>viewer@asrfarm.com</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
