<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Mobile Konven')</title>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981; /* Emerald/Green for Konven */
            --primary-dark: #059669;
            --bg-color: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --danger: #ef4444;
            --warning: #f59e0b;
            --info: #3b82f6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-color); color: var(--text-main); line-height: 1.5; padding-bottom: 5rem; -webkit-tap-highlight-color: transparent; }
        
        /* App Bar */
        .app-bar {
            background: #111827;
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .app-bar h1 {
            font-size: 1.1rem;
            font-weight: 700;
            margin-left: 0.75rem;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .back-btn { color: white; text-decoration: none; display: flex; align-items: center; font-size: 1.25rem; }

        .container { padding: 1rem; }

        /* Card */
        .card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
            border: 1px solid var(--border);
            display: block;
            text-decoration: none;
            color: inherit;
        }
        .card-clickable:active {
            background: #f9fafb;
        }

        .list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .list-item-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
        }
        .list-item-subtitle {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        /* Buttons */
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            padding: 0.875rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:active { background: var(--primary-dark); }
        .btn-danger { background: var(--danger); color: white; }
        .btn-warning { background: var(--warning); color: white; }
        .btn-info { background: var(--info); color: white; }
        .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-main); }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-kosong { background: #e5e7eb; color: #374151; }
        .badge-ditanam { background: #d1fae5; color: #065f46; }
        .badge-rusak { background: #fee2e2; color: #991b1b; }

        /* Form */
        .form-group { margin-bottom: 1rem; }
        .form-label { display: block; font-weight: 500; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            background: #f9fafb;
        }
        .form-control:focus { outline: none; border-color: var(--primary); background: white; }

        /* Bottom Nav */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            display: flex;
            justify-content: space-around;
            padding: 0.75rem 0;
            border-top: 1px solid var(--border);
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
            z-index: 40;
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 500;
            gap: 0.25rem;
        }
        .nav-item.active { color: var(--primary); }
        .nav-item i { font-size: 1.5rem; }

        /* Alert */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #f87171; }
        
        @yield('styles')
    </style>
</head>
<body>

    <div class="app-bar">
        @hasSection('back_url')
            <a href="@yield('back_url')" class="back-btn"><i class="ph ph-arrow-left"></i></a>
        @endif
        <h1>@yield('title')</h1>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

    <div class="bottom-nav">
        <a href="{{ route('m.konven.index') }}" class="nav-item {{ request()->routeIs('m.konven.*') ? 'active' : '' }}">
            <i class="ph ph-plant"></i>
            <span>Konven</span>
        </a>
        <a href="/dashboard" class="nav-item">
            <i class="ph ph-squares-four"></i>
            <span>Dashboard</span>
        </a>
    </div>

    @yield('scripts')
</body>
</html>
