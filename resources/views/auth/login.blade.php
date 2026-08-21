<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ASR FARM</title>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background-image: radial-gradient(circle at 10% 20%, rgba(22, 163, 74, 0.05) 0%, transparent 20%), 
                              radial-gradient(circle at 90% 80%, rgba(22, 163, 74, 0.08) 0%, transparent 20%);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            animation: slideUp 0.5s ease-out forwards;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        .brand-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-section img {
            height: 54px;
            margin-bottom: 1rem;
        }

        .brand-section h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 0.25rem;
        }

        .brand-section p {
            font-size: 0.9rem;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.15rem;
            transition: color 0.2s;
        }

        .input-wrapper input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.6rem;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.95rem;
            color: #1e293b;
            background: #ffffff;
            transition: all 0.2s;
            font-family: inherit;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
        }

        .input-wrapper input:focus + i,
        .input-wrapper input:not(:placeholder-shown) ~ i {
            color: #16a34a;
        }

        .remember-flex {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            font-size: 0.85rem;
        }

        .remember-flex label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            color: #64748b;
            font-weight: 500;
        }

        .remember-flex input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #16a34a;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: #16a34a;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            background: #15803d;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #ef4444;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .demo-credentials {
            margin-top: 1.5rem;
            text-align: center;
        }

        .demo-credentials .title {
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }
        
        .demo-badge {
            display: inline-block;
            padding: 0.3rem 0.6rem;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            font-size: 0.75rem;
            color: #475569;
            margin: 0.2rem;
            cursor: help;
        }
        .demo-badge span {
            font-weight: 700;
            color: #16a34a;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="brand-section">
                <img src="{{ asset('images/logo-asr.png') }}" alt="ASR Logo">
                <h1>ASR FARM</h1>
                <p>Smart Management System</p>
            </div>

            @if($errors->any())
                <div class="alert-error">
                    <i class="ph-fill ph-warning-circle" style="font-size: 1.15rem;"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('auth.login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="login">Email atau Username</label>
                    <div class="input-wrapper">
                        <i class="ph ph-user"></i>
                        <input type="text" id="login" name="login" value="{{ old('login') }}"
                               required placeholder="Masukkan akses Anda" autocomplete="username">
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
                        Ingat Sesi Saya
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    Masuk <i class="ph-bold ph-arrow-right"></i>
                </button>
            </form>
        </div>
    </div>

</body>
</html>
