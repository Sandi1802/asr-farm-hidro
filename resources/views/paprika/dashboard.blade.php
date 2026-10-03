@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Dashboard Paprika</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Ringkasan dan statistik tanaman paprika.</p>
        </div>
        <div>
            <a href="{{ route('paprika.v2.index') }}" class="btn-primary" style="display:inline-block; padding: 0.75rem 1.5rem; border-radius: 8px; background: var(--asr-green); color: white; text-decoration: none; font-weight:600;">
                Kelola GH & Baris
            </a>
        </div>
    </div>
</div>

<div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 1.5rem;">
    <!-- Card Total GH -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(34, 197, 94, 0.15); color: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                <i class="ph ph-house-line"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem; font-weight: 500;">Total GH</p>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($totalGh, 0, ',', '.') }}</h3>
            </div>
        </div>
    </a>

    <!-- Card Total Tanaman (Red) -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                <i class="ph ph-plant"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem; font-weight: 500;">Total Pot Paprika</p>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($totalPlants, 0, ',', '.') }}</h3>
            </div>
        </div>
    </a>

    <!-- Card Ditanam (Orange) -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(249, 115, 22, 0.15); color: #f97316; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                <i class="ph ph-seedling"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem; font-weight: 500;">Sedang Ditanam</p>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($totalPlanted, 0, ',', '.') }}</h3>
            </div>
        </div>
    </a>

    <!-- Card Kosong (Gray) -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(107, 114, 128, 0.15); color: #6b7280; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                <i class="ph ph-circle-dashed"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem; font-weight: 500;">Kosong</p>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($totalKosong, 0, ',', '.') }}</h3>
            </div>
        </div>
    </a>

    <!-- Card Panen (Green) -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(34, 197, 94, 0.15); color: #22c55e; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                <i class="ph ph-basket"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem; font-weight: 500;">Total Panen</p>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($totalPanen, 0, ',', '.') }}</h3>
            </div>
        </div>
    </a>

    <!-- Card Gagal (Dark Red) -->
    <a href="/paprika/v2" style="text-decoration:none;">
        <div class="stat-card" style="background: var(--card-bg); border-radius: 12px; padding: 1.5rem; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 1.2rem; transition: transform 0.2s, box-shadow 0.2s;">
            <div style="width: 56px; height: 56px; border-radius: 14px; background: rgba(185, 28, 28, 0.15); color: #b91c1c; display: flex; align-items: center; justify-content: center; font-size: 1.75rem;">
                <i class="ph ph-x-circle"></i>
            </div>
            <div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.25rem; font-weight: 500;">Rusak / Gagal</p>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($totalGagal, 0, ',', '.') }}</h3>
            </div>
        </div>
    </a>
</div>

<style>
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    @media (max-width: 900px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 600px) {
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
