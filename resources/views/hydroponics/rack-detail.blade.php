@extends('layouts.app')
@section('title', 'Detail Rak – ' . $rack->name)
@section('content')

@php
    // plantTypeMap is passed from controller: ['Pakcoy' => 20, 'Selada' => 25, ...]
    $plantTypeMapPHP = isset($plantTypeMap) ? $plantTypeMap->toArray() : [];
    $defaultGrowthDays = 30;

    $allHoles = $rack->rows->flatMap->holes;
    $countKosong  = $allHoles->where('status', 'kosong')->count();
    $countPanen   = $allHoles->where('status', 'panen')->count();
    $countRusak   = $allHoles->where('status', 'rusak')->count();
    $totalDitanam = $allHoles->where('status', 'ditanam')->count();

    // Dynamic "siap panen" — per-plant growth_days threshold
    $plantsReadyToHarvest = $allHoles->where('status', 'ditanam')
        ->filter(function($h) use ($plantTypeMapPHP, $defaultGrowthDays) {
            if (empty($h->plant_name) || !$h->planted_at) return false;
            $days = $plantTypeMapPHP[$h->plant_name] ?? $defaultGrowthDays;
            return \Carbon\Carbon::parse($h->planted_at)->addDays($days)->lte(now());
        })
        ->groupBy('plant_name')
        ->map(fn($g) => $g->count());

    $countReady        = $plantsReadyToHarvest->sum();
    $countPlantedYoung = max(0, $totalDitanam - $countReady);

    // All ditanam plants grouped
    $plantsPlanted = $allHoles->where('status', 'ditanam')
        ->filter(fn($h) => !empty($h->plant_name))
        ->groupBy('plant_name')
        ->map(fn($g) => $g->count());

    $plantsHarvested = $allHoles->where('status', 'panen')
        ->filter(fn($h) => !empty($h->plant_name))
        ->groupBy('plant_name')
        ->map(fn($g) => $g->count());

    $plantsDamaged = $allHoles->where('status', 'rusak')
        ->filter(fn($h) => !empty($h->plant_name))
        ->groupBy('plant_name')
        ->map(fn($g) => $g->count());
@endphp


<style>
/* ── Hole Grid ─────────────────────────────────────────── */
.hole-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 0.875rem;
    user-select: none;
    -webkit-user-select: none;
}
.hole-item {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 700;
    cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.2s, box-shadow 0.2s, opacity 0.2s;
    position: relative;
    color: white;
}
.hole-item:hover { transform: scale(1.15); box-shadow: 0 3px 10px rgba(0,0,0,0.25); z-index: 5; }

.hole-kosong       { background: #cbd5e1; border-color: #94a3b8; color: #475569; }
.hole-ditanam      { background: #16a34a; border-color: #15803d; }
.hole-siap-panen   { background: #ea580c; border-color: #c2410c; box-shadow: 0 0 6px rgba(234, 88, 12, 0.4); }
.hole-panen        { background: #2563eb; border-color: #1d4ed8; }
.hole-rusak        { background: #dc2626; border-color: #b91c1c; }

/* Plant Highlight & Dimmed state */
.hole-item.highlight-plant {
    box-shadow: 0 0 0 3px #fbbf24, 0 0 16px rgba(245, 158, 11, 0.9) !important;
    transform: scale(1.22) !important;
    z-index: 20 !important;
    border-color: #f59e0b !important;
}
.hole-item.dimmed-plant {
    opacity: 0.2 !important;
}

/* Multi-select mode */
.select-mode .hole-item { cursor: crosshair; }
.hole-item.selected {
    outline: 3px solid #f59e0b !important;
    outline-offset: 2px;
    transform: scale(1.1);
    box-shadow: 0 0 0 4px rgba(245,158,11,0.3);
}

/* Drag selection box */
#dragSelectionBox {
    position: fixed;
    border: 2px dashed #f59e0b;
    background: rgba(245,158,11,0.08);
    pointer-events: none;
    z-index: 500;
    display: none;
    border-radius: 4px;
}

/* Floating action bar */
#bulkActionBar {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    background: #111827;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 50px;
    display: none;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    z-index: 600;
    white-space: nowrap;
    min-width: 540px;
}

/* Plant filter badges */
.plant-badge-btn {
    transition: all 0.2s ease;
}
.plant-badge-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
}
.plant-badge-btn.active {
    outline: 2px solid #f59e0b !important;
    box-shadow: 0 0 10px rgba(245, 158, 11, 0.4) !important;
    transform: scale(1.05) !important;
}
</style>

{{-- BACK & BREADCRUMB --}}
<div style="margin-bottom: 1rem;">
    <a href="{{ route('hydroponics.greenhouses.show', $rack->greenhouse_id) }}" style="color: #6b7280; text-decoration:none; font-size: 0.875rem;">
        <i class="ph ph-arrow-left"></i> {{ $rack->greenhouse->name ?? 'Green House' }}
    </a>
    <span style="color: #d1d5db; margin: 0 0.5rem;">/</span>
    <span style="color: #111827; font-weight: 600; font-size: 0.875rem;">{{ $rack->name }}</span>
</div>

{{-- RAK HEADER --}}
<div style="background: white; border-radius: 14px; border: 1px solid #e5e7eb; padding: 1.5rem; margin-bottom: 1.25rem; display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
    <div style="flex:1; min-width: 200px;">
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">{{ $rack->name }}</h1>
        <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">{{ $rack->greenhouse->name ?? '' }} · Monitoring Lubang & Sensor</p>
    </div>

    {{-- PPM & pH cards --}}
    <div style="display: flex; gap: 1rem; flex-shrink: 0; align-items: stretch;">
        <div style="background: #f0fdf4; border: 2px solid #bbf7d0; border-radius: 12px; padding: 0.875rem 1.25rem; text-align: center; min-width: 100px;">
            <div style="font-size: 0.7rem; font-weight: 700; color: #16a34a; text-transform: uppercase; letter-spacing: 0.5px;">PPM</div>
            <div id="ppmDisplay" style="font-size: 1.75rem; font-weight: 800; color: #15803d; line-height: 1.2;">{{ $rack->ppm_level ?? '—' }}</div>
        </div>
        <div style="background: #eff6ff; border: 2px solid #bfdbfe; border-radius: 12px; padding: 0.875rem 1.25rem; text-align: center; min-width: 100px;">
            <div style="font-size: 0.7rem; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px;">pH</div>
            <div id="phDisplay" style="font-size: 1.75rem; font-weight: 800; color: #1d4ed8; line-height: 1.2;">{{ $rack->ph_level ?? '—' }}</div>
        </div>
        <div style="display: flex; flex-direction: column; justify-content: center; gap: 0.5rem;">
            <button onclick="document.getElementById('sensorModal').style.display='flex'" style="padding: 0.5rem 1rem; background: #16a34a; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.85rem; white-space: nowrap;">
                <i class="ph ph-pencil-simple"></i> Update Sensor
            </button>
            <div style="font-size: 0.7rem; color: #9ca3af; text-align: center;">
                {{ $rack->ppm_ph_updated_at ? $rack->ppm_ph_updated_at->diffForHumans() : 'Belum diupdate' }}
            </div>
        </div>
        <div style="width: 1px; background: #e5e7eb; margin: 0 0.5rem;"></div>
        <div style="display: flex; flex-direction: column; justify-content: center;">
            <a href="{{ route('hydroponics.racks.print-qr', $rack->id) }}" target="_blank" style="display: flex; align-items: center; gap: 0.4rem; padding: 0.7rem 1.2rem; background: #111827; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 0.9rem; text-decoration: none; white-space: nowrap;">
                <i class="ph ph-qr-code" style="font-size: 1.2rem;"></i> Cetak QR
            </a>
        </div>
    </div>
</div>

{{-- SUCCESS ALERT --}}
@if(session('success'))
<div style="padding: 1rem 1.25rem; background: #dcfce7; color: #15803d; border-radius: 10px; border-left: 4px solid #16a34a; font-weight: 500; margin-bottom: 1.25rem;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- RINCIAN JENIS TANAMAN PER STATUS (DITANAM, SIAP PANEN, PANEN, RUSAK) --}}
<div style="background: white; border-radius: 14px; border: 1px solid #e5e7eb; padding: 1.25rem 1.5rem; margin-bottom: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.06); display: flex; flex-direction: column; gap: 1rem;">
    
    {{-- Sedang Ditanam --}}
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <span style="font-size: 0.85rem; font-weight: 700; color: #15803d; min-width: 180px; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph ph-plant" style="color: #16a34a; font-size: 1.1rem;"></i> Sedang Ditanam:
        </span>
        <div id="plantedListContainer" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            @forelse($plantsPlanted as $plantName => $count)
            <button type="button" class="plant-badge-btn badge-ditanam" data-plant="{{ $plantName }}" data-status="ditanam" onclick="highlightPlant('{{ addslashes($plantName) }}', 'ditanam', this)"
                style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;"
                title="Klik untuk highlight lokasi tanaman {{ $plantName }} yang sedang ditanam">
                🌱 {{ $plantName }}
                <span class="plant-count" style="background: #16a34a; color: white; border-radius: 50px; padding: 0.05rem 0.5rem; font-size: 0.7rem; font-weight: 800;">{{ $count }}</span>
            </button>
            @empty
            <span style="font-size: 0.825rem; color: #9ca3af; font-style: italic;">Belum ada bibit aktif.</span>
            @endforelse
        </div>
    </div>

    {{-- Siap Dipanen (>= 30 Hari) --}}
    <div id="readyRowContainer" style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; border-top: 1px dashed #fed7aa; padding-top: 0.75rem; padding-bottom: 0.25rem;">
        <span style="font-size: 0.85rem; font-weight: 700; color: #c2410c; min-width: 180px; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph ph-trophy" style="color: #ea580c; font-size: 1.1rem;"></i> Siap Dipanen (≥30 Hari):
        </span>
        <div id="readyListContainer" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            @forelse($plantsReadyToHarvest as $plantName => $count)
            <button type="button" class="plant-badge-btn badge-ready" data-plant="{{ $plantName }}" data-status="ready" onclick="highlightPlant('{{ addslashes($plantName) }}', 'ready', this)"
                style="background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;"
                title="Klik untuk highlight lokasi tanaman {{ $plantName }} yang siap dipanen (≥30 hari)">
                🎉 {{ $plantName }}
                <span class="plant-count" style="background: #ea580c; color: white; border-radius: 50px; padding: 0.05rem 0.5rem; font-size: 0.7rem; font-weight: 800;">{{ $count }}</span>
            </button>
            @empty
            <span style="font-size: 0.825rem; color: #9ca3af; font-style: italic;">Belum ada tanaman yang mencapai masa panen (≥30 hari).</span>
            @endforelse
        </div>
    </div>

    {{-- Hasil Panen --}}
    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; border-top: 1px dashed #e2e8f0; padding-top: 0.75rem;">
        <span style="font-size: 0.85rem; font-weight: 700; color: #1d4ed8; min-width: 180px; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph ph-basket" style="color: #2563eb; font-size: 1.1rem;"></i> Sudah Dipanen:
        </span>
        <div id="harvestedListContainer" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            @forelse($plantsHarvested as $plantName => $count)
            <button type="button" class="plant-badge-btn badge-panen" data-plant="{{ $plantName }}" data-status="panen" onclick="highlightPlant('{{ addslashes($plantName) }}', 'panen', this)"
                style="background: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;"
                title="Klik untuk highlight lokasi panen {{ $plantName }}">
                🧺 {{ $plantName }}
                <span class="plant-count" style="background: #2563eb; color: white; border-radius: 50px; padding: 0.05rem 0.5rem; font-size: 0.7rem; font-weight: 800;">{{ $count }}</span>
            </button>
            @empty
            <span style="font-size: 0.825rem; color: #9ca3af; font-style: italic;">Belum ada riwayat panen di rak ini.</span>
            @endforelse
        </div>
    </div>

    {{-- Tanaman Rusak (jika ada) --}}
    <div id="damagedRowContainer" style="display: {{ $plantsDamaged->count() > 0 ? 'flex' : 'none' }}; align-items: center; gap: 0.75rem; flex-wrap: wrap; border-top: 1px dashed #e2e8f0; padding-top: 0.75rem;">
        <span style="font-size: 0.85rem; font-weight: 700; color: #b91c1c; min-width: 180px; display: flex; align-items: center; gap: 0.4rem;">
            <i class="ph ph-warning" style="color: #dc2626; font-size: 1.1rem;"></i> Tanaman Rusak:
        </span>
        <div id="damagedListContainer" style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            @foreach($plantsDamaged as $plantName => $count)
            <button type="button" class="plant-badge-btn badge-rusak" data-plant="{{ $plantName }}" data-status="rusak" onclick="highlightPlant('{{ addslashes($plantName) }}', 'rusak', this)"
                style="background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;"
                title="Klik untuk highlight lokasi tanaman {{ $plantName }} (Rusak)">
                ⚠️ {{ $plantName }}
                <span class="plant-count" style="background: #dc2626; color: white; border-radius: 50px; padding: 0.05rem 0.5rem; font-size: 0.7rem; font-weight: 800;">{{ $count }}</span>
            </button>
            @endforeach
        </div>
    </div>
    
    <div style="display: flex; justify-content: flex-end;">
        <button id="resetHighlightBtn" onclick="resetHighlight()" style="display: none; padding: 0.35rem 0.875rem; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 50px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
            ✕ Hapus Penanda / Reset Highlight
        </button>
    </div>
</div>

{{-- TOOLBAR (STATISTIK REAL-TIME & KONTROL MASSAL) --}}
<div style="background: white; border-radius: 14px; border: 1px solid #e5e7eb; padding: 1rem 1.5rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">

    {{-- Real-time Legend & Stats --}}
    <div style="display: flex; gap: 0.75rem; align-items: center; font-size: 0.85rem; flex-wrap: wrap;">
        <div style="display:flex; align-items:center; gap:0.4rem; background: #f8fafc; padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid #e2e8f0;">
            <div style="width:12px; height:12px; border-radius:50%; background:#cbd5e1;"></div>
            <span style="color: #64748b; font-weight: 500;">Kosong:</span>
            <span id="legendKosong" style="font-weight: 800; color: #334155;">{{ $countKosong }}</span>
        </div>

        <div style="display:flex; align-items:center; gap:0.4rem; background: #f0fdf4; padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid #bbf7d0;">
            <div style="width:12px; height:12px; border-radius:50%; background:#16a34a;"></div>
            <span style="color: #15803d; font-weight: 500;">Ditanam:</span>
            <span id="legendDitanam" style="font-weight: 800; color: #16a34a;">{{ $totalDitanam }}</span>
        </div>

        <div style="display:flex; align-items:center; gap:0.4rem; background: #fff7ed; padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid #fed7aa;">
            <div style="width:12px; height:12px; border-radius:50%; background:#ea580c;"></div>
            <span style="color: #c2410c; font-weight: 500;">Siap Panen:</span>
            <span id="legendReady" style="font-weight: 800; color: #ea580c;">{{ $countReady }}</span>
        </div>

        <div style="display:flex; align-items:center; gap:0.4rem; background: #eff6ff; padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid #bfdbfe;">
            <div style="width:12px; height:12px; border-radius:50%; background:#2563eb;"></div>
            <span style="color: #1d4ed8; font-weight: 500;">Panen:</span>
            <span id="legendPanen" style="font-weight: 800; color: #2563eb;">{{ $countPanen }}</span>
        </div>

        <div style="display:flex; align-items:center; gap:0.4rem; background: #fef2f2; padding: 0.4rem 0.75rem; border-radius: 8px; border: 1px solid #fecaca;">
            <div style="width:12px; height:12px; border-radius:50%; background:#dc2626;"></div>
            <span style="color: #b91c1c; font-weight: 500;">Rusak:</span>
            <span id="legendRusak" style="font-weight: 800; color: #dc2626;">{{ $countRusak }}</span>
        </div>
    </div>

    <div style="margin-left: auto; display: flex; gap: 0.75rem; align-items: center;">
        {{-- BULK PLANT TOGGLE --}}
        <button id="toggleSelectBtn" onclick="toggleSelectMode()" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1.125rem; background: #f59e0b; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.875rem; transition: all 0.2s;">
            <i class="ph ph-selection"></i> Mode Tanam Massal
        </button>
        <button onclick="selectAll()" style="padding: 0.625rem 0.875rem; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.8rem; cursor: pointer;">
            Pilih Semua
        </button>
        <button onclick="clearSelection()" style="padding: 0.625rem 0.875rem; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.8rem; cursor: pointer;">
            Bersihkan
        </button>
    </div>
</div>

{{-- ROWS & HOLES --}}
<div id="holeContainer" style="display: flex; flex-direction: column; gap: 1rem;">
    @foreach($rack->rows as $rowIndex => $row)
    <div style="background: white; border-radius: 12px; border: 1px solid #e5e7eb; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
            <h3 style="font-weight: 700; color: #374151; font-size: 0.95rem; display: flex; align-items: center; gap: 0.5rem;">
                <i class="ph ph-rows" style="color: #9ca3af;"></i> {{ $row->name }}
            </h3>
            <div style="font-size: 0.78rem; color: #9ca3af;" class="row-stats" data-row-id="{{ $row->id }}">
                <span class="row-planted">{{ $row->holes->where('status', 'ditanam')->count() }}</span>/{{ $row->holes->count() }} terisi
            </div>
        </div>
        <div class="hole-grid" data-row="{{ $row->id }}">
            @foreach($row->holes as $hole)
            @php
                $growthDaysForHole = $plantTypeMapPHP[$hole->plant_name] ?? $defaultGrowthDays;
                $isReady = $hole->status == 'ditanam' && $hole->planted_at
                    && \Carbon\Carbon::parse($hole->planted_at)->addDays($growthDaysForHole)->lte(now());
                $statusClass = $isReady ? 'hole-siap-panen' : 'hole-'.$hole->status;
                // Build tooltip
                $daysOld = $hole->planted_at ? \Carbon\Carbon::parse($hole->planted_at)->diffInDays(now()) : null;
                $estimatedHarvest = $hole->planted_at ? \Carbon\Carbon::parse($hole->planted_at)->addDays($growthDaysForHole)->format('d M Y') : null;
                $tooltipLabel = $hole->name;
                if ($hole->plant_name) $tooltipLabel .= ' – ' . $hole->plant_name;
                if ($isReady) {
                    $tooltipLabel .= ' | ⚠️ SIAP PANEN (umur ' . $daysOld . ' hari)';
                } elseif ($hole->status == 'ditanam' && $daysOld !== null) {
                    $tooltipLabel .= ' | Umur: ' . $daysOld . ' hari | Estimasi Panen: ' . $estimatedHarvest;
                } else {
                    $tooltipLabel .= ' (' . ucfirst($hole->status) . ')';
                }
            @endphp
            <div class="hole-item {{ $statusClass }}"
                 data-id="{{ $hole->id }}"
                 data-status="{{ $hole->status }}"
                 data-plant="{{ $hole->plant_name }}"
                 data-name="{{ $hole->name }}"
                 data-ready="{{ $isReady ? 'true' : 'false' }}"
                 data-planted-at="{{ $hole->planted_at ? \Carbon\Carbon::parse($hole->planted_at)->format('Y-m-d') : '' }}"
                 data-days-old="{{ $daysOld ?? '' }}"
                 data-growth-days="{{ $growthDaysForHole }}"
                 data-harvest-est="{{ $estimatedHarvest ?? '' }}"
                 title="{{ $tooltipLabel }}"
                 onclick="handleHoleClick(this, event)">
                {{ str_replace('L', '', $hole->name) }}
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

</div>

{{-- DRAG SELECTION BOX --}}
<div id="dragSelectionBox"></div>

{{-- FLOATING BULK ACTION BAR --}}
<div id="bulkActionBar">
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <div style="width: 32px; height: 32px; background: #f59e0b; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <i class="ph ph-check-square" style="color: white;"></i>
        </div>
        <div>
            <span id="selectedCount" style="font-weight: 700; font-size: 1.1rem;">0</span>
            <span style="color: #9ca3af; font-size: 0.875rem;"> lubang dipilih</span>
        </div>
    </div>

    <div style="width: 1px; height: 32px; background: #374151;"></div>

    <select id="bulkStatus" style="padding: 0.5rem 0.75rem; background: #1f2937; color: white; border: 1px solid #374151; border-radius: 8px; font-size: 0.875rem; cursor: pointer;">
        <option value="ditanam">🌱 Tanam Baru</option>
        <option value="siap_panen">🎉 Set Siap Panen (≥30 Hari)</option>
        <option value="panen">🧺 Panen</option>
        <option value="rusak">⚠️ Rusak</option>
        <option value="kosong">⬜ Kosongkan</option>
    </select>

    <select id="bulkPlantName" style="padding: 0.5rem 0.75rem; background: #1f2937; color: white; border: 1px solid #374151; border-radius: 8px; font-size: 0.875rem; min-width: 200px;">
        <option value="">— Pilih Tanaman —</option>
        @foreach($plantTypes as $pt)
        <option value="{{ $pt->name }}" data-days="{{ $pt->growth_days }}">{{ $pt->name }} ({{ $pt->growth_days }} hari)</option>
        @endforeach
    </select>

    <button onclick="executeBulk()" style="padding: 0.625rem 1.5rem; background: #f59e0b; color: #111827; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 0.9rem;">
        <i class="ph ph-check"></i> Terapkan
    </button>
    <button onclick="clearSelection()" style="padding: 0.625rem 0.875rem; background: #374151; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 0.875rem;">
        ✕
    </button>
</div>

{{-- MODAL: Update Sensor --}}
<div id="sensorModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:2rem; width:100%; max-width:420px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-size:1.25rem; font-weight:700; color:#111827;">Update PPM & pH</h3>
            <button onclick="document.getElementById('sensorModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#6b7280;">×</button>
        </div>
        <form action="{{ route('hydroponics.racks.updatePpmPh', $rack->id) }}" method="POST">
            @csrf
            <div style="display:grid; gap:1rem; margin-bottom:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.375rem; font-weight:600; font-size:0.875rem; color:#374151;">PPM Level (800–2000 ideal)</label>
                    <input type="number" step="1" name="ppm_level" value="{{ $rack->ppm_level }}" required
                        style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:1rem; font-weight:600;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.375rem; font-weight:600; font-size:0.875rem; color:#374151;">pH Level (5.5–6.5 ideal)</label>
                    <input type="number" step="0.1" name="ph_level" value="{{ $rack->ph_level }}" required
                        style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:1rem; font-weight:600;">
                </div>
            </div>
            <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('sensorModal').style.display='none'"
                    style="padding:0.75rem 1.25rem; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#16a34a; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Single Hole Update --}}
<div id="holeModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; padding:2rem; width:100%; max-width:420px; box-shadow: 0 20px 60px rgba(0,0,0,0.3);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="font-size:1.1rem; font-weight:700; color:#111827;">Aktivitas Lubang <span id="holeNameLabel" style="color:#16a34a;"></span></h3>
            <button onclick="document.getElementById('holeModal').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#6b7280;">×</button>
        </div>
        <form id="holeForm" method="POST">
            @csrf
            <div style="display:grid; gap:1rem; margin-bottom:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.375rem; font-weight:600; font-size:0.875rem; color:#374151;">Status</label>
                    <select name="status" id="holeStatus" style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.9rem;">
                        <option value="kosong">⬜ Kosong</option>
                        <option value="ditanam">🌱 Ditanam Baru</option>
                        <option value="siap_panen">🎉 Set Siap Panen (≥30 Hari)</option>
                        <option value="panen">🧺 Panen</option>
                        <option value="rusak">⚠️ Rusak</option>
                    </select>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.375rem; font-weight:600; font-size:0.875rem; color:#374151;">Nama Tanaman</label>
                    <select name="plant_name" id="holePlant"
                        style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.9rem;"
                        onchange="updateHarvestEstimate()">
                        <option value="">— Pilih Jenis Tanaman —</option>
                        @foreach($plantTypes as $pt)
                        <option value="{{ $pt->name }}" data-days="{{ $pt->growth_days }}">{{ $pt->name }} ({{ $pt->growth_days }} hari)</option>
                        @endforeach
                    </select>
                    <div id="harvestEstimateInfo" style="margin-top:0.4rem;font-size:0.78rem;color:#16a34a;font-weight:500;"></div>
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.375rem; font-weight:600; font-size:0.875rem; color:#374151;">Tanggal Tanam</label>
                    <input type="date" name="planted_at" id="holePlantedAt"
                        style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.9rem;"
                        onchange="updateHarvestEstimate()">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.375rem; font-weight:600; font-size:0.875rem; color:#374151;">Catatan</label>
                    <textarea name="description" rows="2" placeholder="Kondisi, keterangan tambahan..."
                        style="width:100%; padding:0.75rem; border:1px solid #d1d5db; border-radius:8px; font-size:0.9rem; resize:none;"></textarea>
                </div>
            </div>
            <div style="display:flex; gap:0.75rem; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('holeModal').style.display='none'"
                    style="padding:0.75rem 1.25rem; background:#f3f4f6; color:#374151; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.75rem 1.5rem; background:#16a34a; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
let selectModeActive = false;
let selectedHoles = new Set();
let isDragging = false;
let dragStartX = 0, dragStartY = 0;
const dragBox = document.getElementById('dragSelectionBox');
const bulkBar = document.getElementById('bulkActionBar');

// ─── HIGHLIGHT PLANT ───────────────────────────────────────────
function highlightPlant(plantName, targetStatus, btn) {
    const isAlreadyActive = btn && btn.classList.contains('active');
    
    document.querySelectorAll('.plant-badge-btn').forEach(b => b.classList.remove('active'));
    
    if (isAlreadyActive || !plantName) {
        resetHighlight();
        return;
    }
    
    if (btn) btn.classList.add('active');
    document.getElementById('resetHighlightBtn').style.display = 'inline-flex';

    document.querySelectorAll('.hole-item').forEach(el => {
        let matchesStatus = true;
        if (targetStatus === 'ready') {
            matchesStatus = el.dataset.status === 'ditanam' && el.dataset.ready === 'true';
        } else if (targetStatus) {
            matchesStatus = el.dataset.status === targetStatus;
        }

        const matchesPlant = el.dataset.plant && el.dataset.plant.trim().toLowerCase() === plantName.trim().toLowerCase();

        if (matchesStatus && matchesPlant) {
            el.classList.add('highlight-plant');
            el.classList.remove('dimmed-plant');
        } else {
            el.classList.remove('highlight-plant');
            el.classList.add('dimmed-plant');
        }
    });
}

function resetHighlight() {
    document.querySelectorAll('.plant-badge-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.hole-item').forEach(el => {
        el.classList.remove('highlight-plant', 'dimmed-plant');
    });
    document.getElementById('resetHighlightBtn').style.display = 'none';
}

// ─── UPDATE REAL-TIME STATS ────────────────────────────────────
function updateRealtimeStats() {
    let countKosong = 0, countDitanam = 0, countReady = 0, countPanen = 0, countRusak = 0;
    const plantedMap = {}, readyMap = {}, harvestedMap = {}, damagedMap = {};

    document.querySelectorAll('.hole-item').forEach(el => {
        const st = el.dataset.status;
        const p  = el.dataset.plant ? el.dataset.plant.trim() : '';
        const isReady = el.dataset.ready === 'true';

        if (st === 'kosong') {
            countKosong++;
        } else if (st === 'ditanam') {
            countDitanam++;
            if (p) plantedMap[p] = (plantedMap[p] || 0) + 1;
            if (isReady) {
                countReady++;
                if (p) readyMap[p] = (readyMap[p] || 0) + 1;
            }
        } else if (st === 'panen') {
            countPanen++;
            if (p) harvestedMap[p] = (harvestedMap[p] || 0) + 1;
        } else if (st === 'rusak') {
            countRusak++;
            if (p) damagedMap[p] = (damagedMap[p] || 0) + 1;
        }
    });

    document.getElementById('legendKosong').textContent = countKosong;
    document.getElementById('legendDitanam').textContent = countDitanam;
    if (document.getElementById('legendReady')) document.getElementById('legendReady').textContent = countReady;
    document.getElementById('legendPanen').textContent = countPanen;
    document.getElementById('legendRusak').textContent = countRusak;

    // Update row stats
    document.querySelectorAll('.row-stats').forEach(rEl => {
        const rId = rEl.dataset.rowId;
        const pCount = document.querySelectorAll(`.hole-grid[data-row="${rId}"] .hole-ditanam, .hole-grid[data-row="${rId}"] .hole-siap-panen`).length;
        const pSpan = rEl.querySelector('.row-planted');
        if (pSpan) pSpan.textContent = pCount;
    });

    // Render badge groups
    renderBadgeGroup('plantedListContainer', plantedMap, 'ditanam', '#dcfce7', '#15803d', '#bbf7d0', '#16a34a', '🌱');
    renderBadgeGroup('readyListContainer', readyMap, 'ready', '#fff7ed', '#c2410c', '#fed7aa', '#ea580c', '🎉');
    renderBadgeGroup('harvestedListContainer', harvestedMap, 'panen', '#dbeafe', '#1e40af', '#bfdbfe', '#2563eb', '🧺');
    renderBadgeGroup('damagedListContainer', damagedMap, 'rusak', '#fee2e2', '#991b1b', '#fecaca', '#dc2626', '⚠️');

    // Keep ready row container visible
    const readyContainer = document.getElementById('readyRowContainer');
    if (readyContainer) {
        readyContainer.style.display = 'flex';
    }

    // Show/hide damaged row container
    const damContainer = document.getElementById('damagedRowContainer');
    if (damContainer) {
        damContainer.style.display = Object.keys(damagedMap).length > 0 ? 'flex' : 'none';
    }
}

function renderBadgeGroup(containerId, mapData, statusKey, bg, color, border, countBg, icon) {
    const container = document.getElementById(containerId);
    if (!container) return;
    const keys = Object.keys(mapData);
    if (keys.length === 0) {
        container.innerHTML = `<span style="font-size: 0.825rem; color: #9ca3af; font-style: italic;">Belum ada data ${statusKey}.</span>`;
    } else {
        let html = '';
        keys.forEach(pName => {
            const escaped = pName.replace(/'/g, "\\'");
            html += `
            <button type="button" class="plant-badge-btn badge-${statusKey}" data-plant="${pName}" data-status="${statusKey}" onclick="highlightPlant('${escaped}', '${statusKey}', this)"
                style="background: ${bg}; color: ${color}; border: 1px solid ${border}; padding: 0.35rem 0.75rem; border-radius: 50px; font-size: 0.8rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.4rem;"
                title="Klik untuk highlight lokasi ${statusKey} ${pName}">
                ${icon} ${pName}
                <span class="plant-count" style="background: ${countBg}; color: white; border-radius: 50px; padding: 0.05rem 0.5rem; font-size: 0.7rem; font-weight: 800;">${mapData[pName]}</span>
            </button>`;
        });
        container.innerHTML = html;
    }
}

// ─── TOGGLE SELECT MODE ───────────────────────────────────────
function toggleSelectMode() {
    selectModeActive = !selectModeActive;
    const btn = document.getElementById('toggleSelectBtn');
    const container = document.getElementById('holeContainer');
    if (selectModeActive) {
        btn.style.background = '#dc2626';
        btn.innerHTML = '<i class="ph ph-x"></i> Keluar Mode Massal';
        container.classList.add('select-mode');
    } else {
        btn.style.background = '#f59e0b';
        btn.innerHTML = '<i class="ph ph-selection"></i> Mode Tanam Massal';
        container.classList.remove('select-mode');
        clearSelection();
    }
}

// ─── HANDLE HOLE CLICK ────────────────────────────────────────
function handleHoleClick(el, event) {
    if (selectModeActive) {
        event.preventDefault();
        toggleHole(el);
    } else {
        const id       = el.dataset.id;
        const isReady  = el.dataset.ready === 'true';
        const plantVal = el.dataset.plant || '';
        const daysOld  = el.dataset.daysOld;
        const harvestEst = el.dataset.harvestEst;

        document.getElementById('holeNameLabel').textContent = el.dataset.name;
        document.getElementById('holeStatus').value = isReady ? 'siap_panen' : el.dataset.status;

        // Set select to matching plant option
        const holePlant = document.getElementById('holePlant');
        holePlant.value = plantVal;

        document.getElementById('holePlantedAt').value = el.dataset.plantedAt || '';
        document.getElementById('holeForm').action = '/hydroponics/holes/' + id;

        // Show age tracker info in modal
        const infoEl = document.getElementById('harvestEstimateInfo');
        if (el.dataset.status === 'ditanam' && daysOld !== '') {
            if (isReady) {
                infoEl.innerHTML = `<span style="color:#ea580c;">⚠️ Sudah ${daysOld} hari — SIAP PANEN!</span>`;
            } else {
                infoEl.innerHTML = `🌱 Umur: <strong>${daysOld} hari</strong> &nbsp;|&nbsp; Estimasi Panen: <strong>${harvestEst || '—'}</strong>`;
            }
        } else {
            infoEl.innerHTML = '';
            updateHarvestEstimate();
        }

        document.getElementById('holeModal').style.display = 'flex';
    }
}

function updateHarvestEstimate() {
    const plantSel   = document.getElementById('holePlant');
    const plantedAt  = document.getElementById('holePlantedAt').value;
    const infoEl     = document.getElementById('harvestEstimateInfo');
    const selectedOpt = plantSel.options[plantSel.selectedIndex];
    const growthDays  = selectedOpt ? parseInt(selectedOpt.dataset.days) : NaN;

    if (!isNaN(growthDays) && plantedAt) {
        const planted = new Date(plantedAt);
        const harvest = new Date(planted);
        harvest.setDate(harvest.getDate() + growthDays);
        const today   = new Date();
        const daysOld = Math.floor((today - planted) / 86400000);
        const opts    = { day:'numeric', month:'long', year:'numeric' };
        const hStr    = harvest.toLocaleDateString('id-ID', opts);

        if (harvest <= today) {
            infoEl.innerHTML = `<span style="color:#ea580c;">⚠️ Umur ${daysOld} hari — Sudah melewati waktu panen!</span>`;
        } else {
            const daysLeft = Math.ceil((harvest - today) / 86400000);
            infoEl.innerHTML = `🌱 Umur: <strong>${daysOld} hari</strong> &nbsp;|&nbsp; Estimasi Panen: <strong>${hStr}</strong> (${daysLeft} hari lagi)`;
        }
    } else if (!isNaN(growthDays)) {
        const today = new Date();
        const harvest = new Date(today);
        harvest.setDate(harvest.getDate() + growthDays);
        const opts = { day:'numeric', month:'long', year:'numeric' };
        infoEl.innerHTML = `📅 Jika ditanam hari ini → Panen <strong>${harvest.toLocaleDateString('id-ID', opts)}</strong>`;
    } else {
        infoEl.innerHTML = '';
    }
}

function toggleHole(el) {
    const id = el.dataset.id;
    if (selectedHoles.has(id)) {
        selectedHoles.delete(id);
        el.classList.remove('selected');
    } else {
        selectedHoles.add(id);
        el.classList.add('selected');
    }
    updateBulkBar();
}

function updateBulkBar() {
    const cnt = selectedHoles.size;
    document.getElementById('selectedCount').textContent = cnt;
    bulkBar.style.display = cnt > 0 ? 'flex' : 'none';
}

function selectAll() {
    if (!selectModeActive) {
        selectModeActive = true;
        const btn = document.getElementById('toggleSelectBtn');
        btn.style.background = '#dc2626';
        btn.innerHTML = '<i class="ph ph-x"></i> Keluar Mode Massal';
        document.getElementById('holeContainer').classList.add('select-mode');
    }
    document.querySelectorAll('.hole-item').forEach(el => {
        selectedHoles.add(el.dataset.id);
        el.classList.add('selected');
    });
    updateBulkBar();
}

function clearSelection() {
    selectedHoles.clear();
    document.querySelectorAll('.hole-item.selected').forEach(el => el.classList.remove('selected'));
    updateBulkBar();
}

// ─── DRAG TO SELECT ───────────────────────────────────────────
document.addEventListener('mousedown', function(e) {
    if (!selectModeActive) return;
    if (e.target.closest('#bulkActionBar') || e.target.closest('.modal')) return;

    if (e.target.classList.contains('hole-item')) return;

    isDragging = true;
    dragStartX = e.clientX;
    dragStartY = e.clientY;
    dragBox.style.left = e.clientX + 'px';
    dragBox.style.top = e.clientY + 'px';
    dragBox.style.width = '0';
    dragBox.style.height = '0';
    dragBox.style.display = 'block';
});

document.addEventListener('mousemove', function(e) {
    if (!isDragging) return;
    const x = Math.min(e.clientX, dragStartX);
    const y = Math.min(e.clientY, dragStartY);
    const w = Math.abs(e.clientX - dragStartX);
    const h = Math.abs(e.clientY - dragStartY);
    dragBox.style.left = x + 'px';
    dragBox.style.top = y + 'px';
    dragBox.style.width = w + 'px';
    dragBox.style.height = h + 'px';
});

document.addEventListener('mouseup', function(e) {
    if (!isDragging) return;
    isDragging = false;
    dragBox.style.display = 'none';

    const selRect = {
        left:   Math.min(e.clientX, dragStartX),
        top:    Math.min(e.clientY, dragStartY),
        right:  Math.max(e.clientX, dragStartX),
        bottom: Math.max(e.clientY, dragStartY),
    };

    if (Math.abs(e.clientX - dragStartX) < 5 && Math.abs(e.clientY - dragStartY) < 5) return;

    document.querySelectorAll('.hole-item').forEach(el => {
        const r = el.getBoundingClientRect();
        const cx = r.left + r.width / 2;
        const cy = r.top + r.height / 2;
        if (cx >= selRect.left && cx <= selRect.right && cy >= selRect.top && cy <= selRect.bottom) {
            selectedHoles.add(el.dataset.id);
            el.classList.add('selected');
        }
    });
    updateBulkBar();
});

// ─── EXECUTE BULK ─────────────────────────────────────────────
function executeBulk() {
    if (selectedHoles.size === 0) return;

    const status    = document.getElementById('bulkStatus').value;
    const plantName = document.getElementById('bulkPlantName').value;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const btn = document.querySelector('#bulkActionBar button[onclick="executeBulk()"]');
    btn.disabled = true;
    btn.innerHTML = '<i class="ph ph-spinner"></i> Menyimpan...';

    fetch('/hydroponics/holes/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({
            hole_ids: Array.from(selectedHoles),
            status: status,
            plant_name: plantName,
            description: status === 'siap_panen' ? 'Set siap panen massal' : 'Penanaman massal via drag-select',
        }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            selectedHoles.forEach(id => {
                const el = document.querySelector(`.hole-item[data-id="${id}"]`);
                if (el) {
                    if (status === 'siap_panen') {
                        el.className = 'hole-item hole-siap-panen';
                        el.dataset.status = 'ditanam';
                        el.dataset.ready = 'true';
                    } else {
                        const statusClass = {kosong: 'hole-kosong', ditanam: 'hole-ditanam', panen: 'hole-panen', rusak: 'hole-rusak'};
                        el.className = 'hole-item ' + (statusClass[status] || 'hole-kosong');
                        el.dataset.status = status;
                        el.dataset.ready = 'false';
                    }

                    if (plantName) {
                        el.dataset.plant = plantName;
                    } else if (status === 'kosong') {
                        el.dataset.plant = '';
                    }

                    const stText = status === 'siap_panen' ? 'Siap Panen (≥30 Hari)' : status;
                    el.title = el.dataset.name + (el.dataset.plant ? ' – ' + el.dataset.plant : '') + ' (' + stText + ')';
                }
            });

            updateRealtimeStats();
            showToast(`✅ ${data.count} lubang berhasil diperbarui!`, '#16a34a');
            clearSelection();
        } else {
            showToast('❌ Gagal menyimpan. Coba lagi.', '#dc2626');
        }
    })
    .catch(() => showToast('❌ Gagal terhubung ke server.', '#dc2626'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ph ph-check"></i> Terapkan';
    });
}

// ─── TOAST NOTIFICATION ───────────────────────────────────────
function showToast(msg, color) {
    const toast = document.createElement('div');
    toast.style.cssText = `position:fixed; top:1.5rem; right:1.5rem; background:${color}; color:white;
        padding:1rem 1.5rem; border-radius:12px; font-weight:600; font-size:0.9rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.2); z-index:9999; transition: all 0.3s ease;`;
    toast.textContent = msg;
    document.body.appendChild(toast);
    setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 300); }, 3000);
}
</script>

@endsection
