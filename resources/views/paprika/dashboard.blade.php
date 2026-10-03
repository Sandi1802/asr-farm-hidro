@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Dashboard Paprika</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Ringkasan dan statistik tanaman paprika.</p>
        </div>
    </div>
</div>

<div class="dashboard-stats" style="margin-top: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
    <!-- Card Total GH -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-big-card" style="background:#e0f2fe; padding:1.5rem; border-radius:12px; position:relative; overflow:hidden;">
            <div>
                <div style="font-size:2rem; font-weight:800; color:#0369a1;">{{ number_format($totalGh, 0, ',', '.') }}</div>
                <div style="font-size:0.9rem; color:#0c4a6e; font-weight:600; margin-top:0.25rem;">Total GH</div>
            </div>
            <i class="ph ph-house-line" style="position:absolute; right:1rem; bottom:1rem; font-size:4rem; color:#0284c7; opacity:0.2;"></i>
        </div>
    </a>

    <!-- Card Total Tanaman -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-big-card" style="background:#f3f4f6; padding:1.5rem; border-radius:12px; position:relative; overflow:hidden;">
            <div>
                <div style="font-size:2rem; font-weight:800; color:#374151;">{{ number_format($totalPlants, 0, ',', '.') }}</div>
                <div style="font-size:0.9rem; color:#4b5563; font-weight:600; margin-top:0.25rem;">Total Pot Paprika</div>
            </div>
            <i class="ph ph-pepper" style="position:absolute; right:1rem; bottom:1rem; font-size:4rem; color:#6b7280; opacity:0.2;"></i>
        </div>
    </a>

    <!-- Card Ditanam -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-big-card" style="background:#dcfce7; padding:1.5rem; border-radius:12px; position:relative; overflow:hidden;">
            <div>
                <div style="font-size:2rem; font-weight:800; color:#166534;">{{ number_format($totalPlanted, 0, ',', '.') }}</div>
                <div style="font-size:0.9rem; color:#14532d; font-weight:600; margin-top:0.25rem;">Sedang Ditanam</div>
            </div>
            <i class="ph ph-seedling" style="position:absolute; right:1rem; bottom:1rem; font-size:4rem; color:#16a34a; opacity:0.2;"></i>
        </div>
    </a>

    <!-- Card Panen -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-big-card" style="background:#ffedd5; padding:1.5rem; border-radius:12px; position:relative; overflow:hidden;">
            <div>
                <div style="font-size:2rem; font-weight:800; color:#c2410c;">{{ number_format($totalPanen, 0, ',', '.') }}</div>
                <div style="font-size:0.9rem; color:#9a3412; font-weight:600; margin-top:0.25rem;">Total Panen</div>
            </div>
            <i class="ph ph-basket" style="position:absolute; right:1rem; bottom:1rem; font-size:4rem; color:#f97316; opacity:0.2;"></i>
        </div>
    </a>

    <!-- Card Rusak/Gagal -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-big-card" style="background:#fee2e2; padding:1.5rem; border-radius:12px; position:relative; overflow:hidden;">
            <div>
                <div style="font-size:2rem; font-weight:800; color:#b91c1c;">{{ number_format($totalGagal, 0, ',', '.') }}</div>
                <div style="font-size:0.9rem; color:#991b1b; font-weight:600; margin-top:0.25rem;">Rusak / Gagal</div>
            </div>
            <i class="ph ph-warning-circle" style="position:absolute; right:1rem; bottom:1rem; font-size:4rem; color:#dc2626; opacity:0.2;"></i>
        </div>
    </a>
</div>

<div class="content-body" style="margin-top: 2rem;">
    <div style="background:white; border-radius:12px; padding:2rem; text-align:center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <img src="https://cdn-icons-png.flaticon.com/512/2821/2821808.png" width="120" style="opacity:0.8; margin-bottom:1rem;" />
        <h3 style="font-size:1.2rem; color:var(--text-main); margin-bottom:0.5rem;">Selamat Datang di Paprika V2</h3>
        <p style="color:var(--text-muted); max-width:600px; margin:0 auto 1.5rem auto; line-height:1.6;">
            Sistem Paprika V2 memungkinkan Anda untuk mengelola seluruh siklus penanaman dengan terstruktur.<br>Mulai dari tingkat <strong>Greenhouse</strong> &rarr; <strong>Baris</strong> &rarr; hingga ke level <strong>Pot</strong>.
        </p>
        <a href="{{ route('paprika.v2.index') }}" class="btn-primary" style="display:inline-block; padding: 0.75rem 1.5rem; border-radius: 8px; background: var(--asr-green); color: white; text-decoration: none; font-weight:600;">
            Kelola Greenhouse Paprika Sekarang
        </a>
    </div>
</div>
@endsection
