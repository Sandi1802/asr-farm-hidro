<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - ASR Farm</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body style="background-color: #f0f2f5;">
    <div style="display: flex; min-height: 100vh;">
        <!-- Sidebar Admin -->
        <div style="width: 250px; background-color: var(--color-bg-dark); color: white; padding: 2rem 1rem;">
            <h2 style="color: var(--color-text-light); margin-bottom: 2rem; font-size: 1.5rem; text-align: center;">ASR Admin</h2>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <a href="/admin" style="color: white; padding: 0.5rem; border-radius: 4px; background: {{ request()->is('admin') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">Dashboard</a>
                <a href="/admin/products" style="color: white; padding: 0.5rem; border-radius: 4px; background: {{ request()->is('admin/products') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">Kelola Produk</a>
                <a href="/admin/blog" style="color: white; padding: 0.5rem; border-radius: 4px; background: {{ request()->is('admin/blog') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">Kelola Blog</a>
                <a href="/admin/testimonials" style="color: white; padding: 0.5rem; border-radius: 4px; background: {{ request()->is('admin/testimonials') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">Testimoni</a>
                <a href="/admin/settings" style="color: white; padding: 0.5rem; border-radius: 4px; background: {{ request()->is('admin/settings') ? 'rgba(255,255,255,0.1)' : 'transparent' }};">Pengaturan Teks</a>
                <a href="/" target="_blank" style="color: var(--color-accent); padding: 0.5rem; border-radius: 4px;">Lihat Website &rarr;</a>
                <a href="/admin/logout" style="color: #ffaaaa; padding: 0.5rem; border-radius: 4px; margin-top: 2rem;">Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div style="flex: 1; padding: 2rem; overflow-y: auto;">
            @yield('content')
        </div>
    </div>
</body>
</html>
