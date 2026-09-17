@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Dashboard Paprika (Fitur Sedang Dalam Pengerjaan)</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Ringkasan dan statistik tanaman paprika.</p>
        </div>
    </div>
</div>

<div class="dashboard-stats" style="margin-top: 1.5rem;">
    <!-- Card Total GH -->
    <a href="/paprika/greenhouses" style="text-decoration:none;">
        <div class="stat-big-card sbc-paprika-green">
            <div>
                <div class="sbc-value">3</div>
                <div class="sbc-label">Total GH</div>
            </div>
            <i class="ph ph-house-line sbc-icon"></i>
        </div>
    </a>

    <!-- Card Total Tanaman -->
    <a href="/paprika/greenhouses" style="text-decoration:none;">
        <div class="stat-big-card sbc-paprika-red">
            <div>
                <div class="sbc-value">3000</div>
                <div class="sbc-label">Total Tanaman Paprika</div>
            </div>
            <i class="ph ph-pepper sbc-icon"></i>
        </div>
    </a>

    <!-- Card Ditanam -->
    <a href="/paprika/greenhouses" style="text-decoration:none;">
        <div class="stat-big-card sbc-paprika-orange">
            <div>
                <div class="sbc-value">0</div>
                <div class="sbc-label">Ditanam</div>
            </div>
            <i class="ph ph-seedling sbc-icon"></i>
        </div>
    </a>

    <!-- Card Proses -->
    <a href="/paprika/greenhouses" style="text-decoration:none;">
        <div class="stat-big-card sbc-paprika-yellow">
            <div>
                <div class="sbc-value">0</div>
                <div class="sbc-label">Proses</div>
            </div>
            <i class="ph ph-arrows-clockwise sbc-icon"></i>
        </div>
    </a>

    <!-- Card Siap Panen -->
    <a href="/paprika/greenhouses" style="text-decoration:none;">
        <div class="stat-big-card sbc-paprika-green">
            <div>
                <div class="sbc-value">0</div>
                <div class="sbc-label">Siap Panen</div>
            </div>
            <i class="ph ph-basket sbc-icon"></i>
        </div>
    </a>

    <!-- Card Gagal -->
    <a href="/paprika/greenhouses" style="text-decoration:none;">
        <div class="stat-big-card sbc-paprika-red">
            <div>
                <div class="sbc-value">0</div>
                <div class="sbc-label">Gagal Panen</div>
            </div>
            <i class="ph ph-warning-circle sbc-icon"></i>
        </div>
    </a>
</div>
@endsection
