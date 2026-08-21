<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - ASR Farm</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background-color: var(--color-bg-dark); display: flex; justify-content: center; align-items: center; min-height: 100vh;">
    <div class="card" style="width: 100%; max-width: 400px; padding: 2rem;">
        <h2 style="text-align: center; margin-bottom: 2rem;">Admin Login</h2>
        
        @if($errors->any())
            <div style="background-color: #ffebee; color: #c62828; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px;" value="{{ old('email') }}">
            </div>
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.8rem; border: 1px solid #ccc; border-radius: 4px;">
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Login</button>
        </form>
    </div>
</body>
</html>
