@extends('layouts.app')
@section('title', 'Dashboard ASR FARM')
@section('content')

@php
$categoryConfig = [
    'bibit'       => ['label' => 'Stok Bibit',       'icon' => 'ph-fill ph-plant',         'color' => '#16a34a', 'bg' => '#dcfce7'],
    'media_tanam' => ['label' => 'Media Tanam',       'icon' => 'ph-fill ph-cube',          'color' => '#0369a1', 'bg' => '#e0f2fe'],
    'nutrisi'     => ['label' => 'Nutrisi',           'icon' => 'ph-fill ph-flask',         'color' => '#d97706', 'bg' => '#fef3c7'],
    'obat'        => ['label' => 'Obat-obatan',       'icon' => 'ph-fill ph-first-aid-kit', 'color' => '#dc2626', 'bg' => '#fee2e2'],
    'peralatan'   => ['label' => 'Peralatan',         'icon' => 'ph-fill ph-wrench',        'color' => '#7c3aed', 'bg' => '#ede9fe'],
    'perlengkapan'=> ['label' => 'Perlengkapan',      'icon' => 'ph-fill ph-toolbox',       'color' => '#0891b2', 'bg' => '#cffafe'],
    'lainnya'     => ['label' => 'Lainnya',           'icon' => 'ph-fill ph-package',       'color' => '#64748b', 'bg' => '#f1f5f9'],
];
$calendarJson   = json_encode($calendarEvents ?? []);
$rotationJson   = json_encode($rotationData ?? []);
$plantStageJson = json_encode($plantStageData ?? []);
@endphp

<style>
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; }
.cal-day-header { text-align:center; font-size:0.75rem; font-weight:700; color:var(--text-muted, #9ca3af); padding:0.4rem 0; text-transform:uppercase; letter-spacing:0.5px; }
.cal-day { border-radius:10px; min-height:75px; padding:6px 6px; font-size:0.78rem; cursor:pointer; transition:all 0.2s ease; border: 1px solid var(--border-color, #e5e7eb); background: var(--card-bg, #ffffff); display: flex; flex-direction: column; align-items: stretch; position: relative; }
.cal-day:hover { border-color: var(--asr-green, #16a34a); box-shadow: 0 4px 12px rgba(22, 163, 74, 0.12); transform: translateY(-1px); }
.cal-day.today { background: rgba(22, 163, 74, 0.08) !important; border: 2px solid var(--asr-green, #16a34a) !important; }
[data-theme="dark"] .cal-day.today { background: rgba(22, 163, 74, 0.2) !important; border: 2px solid #22c55e !important; }
.cal-day.selected-day { border: 2px solid #2563eb !important; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2) !important; }
.cal-day.other-month { opacity: 0.35; background: rgba(0,0,0,0.02); border-color: transparent; cursor: default; }
.cal-day-num { font-weight: 700; color: var(--text-main, #1e293b); font-size: 0.82rem; line-height: 1; }
.cal-dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
.cal-legend-pill { background: var(--border-color, #f1f5f9); color: var(--text-main); padding: 0.3rem 0.75rem; border-radius: 50px; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; border: 1px solid rgba(0,0,0,0.05); }
.cal-nav-btn { border: 1px solid var(--border-color); background: var(--card-bg); color: var(--text-main); width: 32px; height: 32px; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; transition: all 0.15s ease; }
.cal-nav-btn:hover { background: var(--asr-green-light); color: var(--asr-green); border-color: var(--asr-green); }
.chart-card { background: var(--card-bg, white); border-radius: 14px; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1.25rem 1.5rem; min-width: 0; }
.chart-card-title { font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main); }

/* Fullscreen Chart Fixes */
.chart-card:-webkit-full-screen { display: flex; flex-direction: column; padding: 2rem; background: var(--card-bg, #ffffff); overflow: hidden; }
.chart-card:-webkit-full-screen > div[style*="height"] { flex: 1 !important; height: auto !important; min-height: 0 !important; }
.chart-card:-webkit-full-screen .chart-card-title { font-size: 1.5rem; margin-bottom: 2rem; }

.chart-card:fullscreen { display: flex; flex-direction: column; padding: 2rem; background: var(--card-bg, #ffffff); overflow: hidden; }
.chart-card:fullscreen > div[style*="height"] { flex: 1 !important; height: auto !important; min-height: 0 !important; }
.chart-card:fullscreen .chart-card-title { font-size: 1.5rem; margin-bottom: 2rem; }

/* Calendar Dropdown */
.cal-dropdown { position: absolute; top: 100%; left: 50%; transform: translateX(-50%); background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.1); z-index: 100; margin-top: 0.5rem; min-width: 220px; }
.cal-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
.cal-btn { background: transparent; border: 1px solid var(--border-color); padding: 0.5rem; border-radius: 8px; color: var(--text-main); font-weight: 600; cursor: pointer; transition: all 0.2s; width: 100% !important; text-align: center; }
.cal-btn:hover { background: var(--asr-green); color: white; border-color: var(--asr-green); }
.cal-btn.active { background: var(--asr-green); color: white; border-color: var(--asr-green); }
.cal-year-scroller { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; max-height: 200px; overflow-y: auto; padding-right: 0.25rem; }
.cal-year-scroller::-webkit-scrollbar { width: 4px; }
.cal-year-scroller::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 4px; }
</style>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:-0.75rem; flex-wrap: wrap; gap: 0.75rem;">
        <h2 style="font-size:1.15rem;font-weight:800;color:var(--text-main);display:flex;align-items:center;gap:0.5rem;margin:0; letter-spacing:-0.3px;">
            <i class="ph ph-squares-four" style="color:var(--asr-green);"></i> <span id="summaryGlobalTitle">Ringkasan Global</span>
        </h2>
        <div id="periodFilterTabs" style="display: flex; gap: 0.25rem; background: var(--bg-main); border-radius: 10px; padding: 4px; border: 1px solid var(--border-color);">
            <button onclick="switchPeriod('year', this)" class="period-tab" style="border:none; background:transparent; padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:var(--text-muted); cursor:pointer; transition:all 0.2s; font-family:inherit;">Tahun Ini</button>
            <button onclick="switchPeriod('month', this)" class="period-tab period-tab-active" style="border:none; background:var(--asr-green); padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:white; cursor:pointer; transition:all 0.2s; font-family:inherit;">Bulan Ini</button>
            <button onclick="switchPeriod('week', this)" class="period-tab" style="border:none; background:transparent; padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:var(--text-muted); cursor:pointer; transition:all 0.2s; font-family:inherit;">Minggu Ini</button>
            <button onclick="switchPeriod('today', this)" class="period-tab" style="border:none; background:transparent; padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:var(--text-muted); cursor:pointer; transition:all 0.2s; font-family:inherit;">Hari Ini</button>
        </div>
    </div>
    <div class="dashboard-stats">
        @php
        $totalDamage = \App\Models\DamageNote::where('status','!=','resolved')->count();
        $todayStr = \Carbon\Carbon::now()->format('Y-m-d');
        $todayActivities = isset($calendarEvents[$todayStr]) ? count($calendarEvents[$todayStr]) : 0;
        
        $pendingProcurements = \App\Models\Procurement::where('status_po', false)->count();
        $combinedStats = [
            // Baris 1: Kapasitas & Operasional (4 Kolom)
            ['label' => 'Total Fasilitas', 'value' => $totalGH . ' GH', 'icon' => 'ph-buildings', 'class' => 'sbc-dark-green', 'sub' => $totalRacks . ' Rak • ' . number_format($totalHoles,0,',','.') . ' Lubang'],
            ['label' => 'Keterisian Lahan', 'value' => $occupancyRate . '%', 'icon' => 'ph-chart-pie-slice', 'class' => 'sbc-mid-green', 'sub' => number_format($plantedHoles,0,',','.') . ' Lubang Terisi (' . $plantedTypesCount . ' Jenis)'],
            ['id' => 'card-lubang-kosong', 'label' => 'Lubang Kosong', 'value' => number_format($emptyHolesCount,0,',','.'),        'icon' => 'ph-circle-dashed', 'class' => 'sbc-slate-farm', 'sub' => 'Menunggu ditanam'],
            ['label' => 'Jadwal Hari Ini', 'value' => $todayActivities, 'icon' => 'ph-calendar-check', 'class' => 'sbc-gold', 'sub' => 'Kegiatan Operasional'],
            
            // Baris 2: Siklus Produksi
            ['id' => 'val-total-semai', 'label' => 'Total Semai',       'value' => number_format($produksiBulanIni['total_semai'],0,',','.').' Benih', 'icon' => 'ph-seedling',   'class' => 'sbc-mid-green', 'sub' => $produksiBulanIni['jenis_semai'].' Jenis Tanaman'],
            ['id' => 'val-total-tanam', 'label' => 'Total Masuk GH',    'value' => number_format($produksiBulanIni['total_tanam'],0,',','.').' Lubang', 'icon' => 'ph-plant',    'class' => 'sbc-teal-farm'],
            ['id' => 'card-siap-panen', 'label' => 'Siap Panen',    'value' => number_format($readyToHarvestCount,0,',','.'),    'icon' => 'ph-trophy',        'class' => 'sbc-gold',        'sub' => $readyTypesCount.' Jenis Tanaman', 'onClick' => 'showSiapPanenModal()'],
            
            // Baris 3: Laporan & Isu
            ['id' => 'card-sudah-panen', 'label' => 'Sudah Panen',   'value' => number_format($harvestedHoles,0,',','.'),         'icon' => 'ph-basket',        'class' => 'sbc-teal-farm', 'sub' => $harvestedTypesCount.' Jenis Tanaman'],
            ['id' => 'card-gagal-panen', 'label' => 'Gagal Panen',   'value' => number_format($damagedHoles,0,',','.'),           'icon' => 'ph-warning',       'class' => 'sbc-earth',       'sub' => $damagedTypesCount.' Jenis Rusak'],
            ['label' => 'Perbaikan Aset',   'value' => $totalDamage, 'icon' => 'ph-warning-octagon','class' => 'sbc-rust', 'link' => '/hydroponics/damage-notes', 'sub' => 'Kasus Menunggu'],
            ['label' => 'Pengajuan Kebutuhan', 'value' => $pendingProcurements, 'icon' => 'ph-clipboard-text', 'class' => 'sbc-olive', 'sub' => 'Kasus Pembelian Aktif', 'link' => '/hydroponics/inventory'],
        ];
        @endphp

        @foreach($combinedStats as $s)
        <a @if(isset($s['link'])) href="{{ $s['link'] }}" @elseif(isset($s['onClick'])) onclick="{{ $s['onClick'] }}" style="cursor:pointer;" @endif style="text-decoration:none; @if(!isset($s['link']) && !isset($s['onClick'])) cursor:default; @endif">
            <div class="stat-big-card {{ $s['class'] }}">
                <div>
                    <div class="sbc-value" {!! isset($s['id']) ? 'id="'.$s['id'].'"' : '' !!}>{{ $s['value'] }}</div>
                    <div class="sbc-label">{{ $s['label'] }}</div>
                    @if(isset($s['sub']))
                    <div style="font-size:0.75rem; color: rgba(255,255,255,0.85); font-weight:600; margin-top:0.35rem; letter-spacing:0.3px;" {!! isset($s['id']) ? 'id="'.$s['id'].'-sub"' : '' !!}>{{ $s['sub'] }}</div>
                    @endif
                </div>
                @if(isset($s['link']) || isset($s['onClick']))
                <div class="sbc-link">Selengkapnya <i class="ph ph-arrow-right"></i></div>
                @endif
                <i class="ph {{ $s['icon'] }} sbc-icon"></i>
            </div>
        </a>
        @endforeach
    </div>

    {{-- PRODUKSI BULAN INI MERGED WITH COMBINED STATS ABOVE --}}





    {{-- CALENDAR + DAILY SCHEDULE (2-col) AT TOP --}}
    <div class="responsive-grid-cal">

        {{-- HARVEST CALENDAR --}}
        <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph ph-calendar-check" style="color: var(--asr-green);"></i> Kalender Pertumbuhan
                </h2>
                <div style="display: flex; align-items: center; gap: 0.5rem; position: relative;">
                    <button onclick="document.getElementById('addEventModal').classList.add('open')" style="border:none;background:var(--asr-green-light);color:var(--asr-green-dark);border-radius:7px;padding:0.4rem 0.8rem;cursor:pointer;font-weight:700;font-size:0.8rem;display:flex;align-items:center;gap:0.3rem;"><i class="ph ph-plus"></i> Kegiatan</button>
                    <button onclick="changeMonth(-1)" class="cal-nav-btn"><i class="ph ph-caret-left"></i></button>
                    <div style="display:flex; gap:0.25rem; font-weight:700; font-size:1rem; color:var(--text-main); min-width:140px; justify-content:center; align-items:center;">
                        <span class="cal-month-selector" onclick="toggleMonthSelect()" id="calMonthText" style="cursor:pointer;"></span>
                        <span class="cal-year-selector" onclick="toggleYearSelect()" id="calYearText" style="cursor:pointer;"></span>
                    </div>
                    <button onclick="changeMonth(1)" class="cal-nav-btn"><i class="ph ph-caret-right"></i></button>

                    <!-- Month Dropdown -->
                    <div id="monthDropdown" class="cal-dropdown" style="display:none;">
                        <div class="cal-grid-3" id="monthGrid"></div>
                    </div>

                    <!-- Year Picker Popup - spinner style -->
                    <div id="yearDropdown" class="cal-dropdown" style="display:none; width:200px;">
                        <div style="text-align:center; padding:0.5rem 0;">
                            <div style="font-size:0.72rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.75rem;">Pilih Tahun</div>
                            <button type="button" onclick="nudgeYear(1)" style="width:100%;border:none;background:var(--bg-color);border-radius:8px;padding:0.5rem;cursor:pointer;font-size:1.3rem;color:var(--text-muted);transition:all 0.15s;" onmouseover="this.style.background='var(--asr-green)';this.style.color='white'" onmouseout="this.style.background='var(--bg-color)';this.style.color='var(--text-muted)'">
                                <i class="ph ph-caret-up"></i>
                            </button>
                            <input id="yearInput" type="number" min="2000" max="2099" onchange="selectYearFromInput(this.value)" style="width:100%;text-align:center;font-size:1.8rem;font-weight:800;border:none;background:transparent;color:var(--text-main);outline:none;padding:0.25rem 0;-moz-appearance:textfield;" />
                            <button type="button" onclick="nudgeYear(-1)" style="width:100%;border:none;background:var(--bg-color);border-radius:8px;padding:0.5rem;cursor:pointer;font-size:1.3rem;color:var(--text-muted);transition:all 0.15s;" onmouseover="this.style.background='var(--asr-green)';this.style.color='white'" onmouseout="this.style.background='var(--bg-color)';this.style.color='var(--text-muted)'">
                                <i class="ph ph-caret-down"></i>
                            </button>
                            <button type="button" onclick="confirmYear()" style="margin-top:0.75rem;width:100%;border:none;background:var(--asr-green);color:white;border-radius:8px;padding:0.5rem;cursor:pointer;font-weight:700;font-size:0.85rem;">OK</button>
                        </div>
                    </div>
                </div>
            </div>
            <div style="padding: 1.25rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                {{-- Unified Legend --}}
                <div class="cal-legend-bar">
                    <span class="cal-legend-item" onclick="toggleCalFilter('semai', this)" style="cursor:pointer; transition:all 0.2s;"><span class="cal-legend-swatch" style="background:#16a34a;"></span>Semai</span>
                    <span class="cal-legend-item" onclick="toggleCalFilter('tanam', this)" style="cursor:pointer; transition:all 0.2s;"><span class="cal-legend-swatch" style="background:#2563eb;"></span>Tanam</span>
                    <span class="cal-legend-item" onclick="toggleCalFilter('remaja', this)" style="cursor:pointer; transition:all 0.2s;"><span class="cal-legend-swatch" style="background:#d97706;"></span>Remaja</span>
                    <span class="cal-legend-item" onclick="toggleCalFilter('harvest', this)" style="cursor:pointer; transition:all 0.2s;"><span class="cal-legend-swatch" style="background:#e11d48;"></span>Panen</span>
                    <span class="cal-legend-item" onclick="toggleCalFilter('custom', this)" style="cursor:pointer; transition:all 0.2s;"><span class="cal-legend-swatch" style="background:#0891b2;"></span>Kegiatan</span>
                </div>
                <div class="cal-grid" style="margin-bottom:6px;">
                    @foreach(['Min','Sen','Sel','Rab','Kam','Jum','Sab'] as $d)
                    <div class="cal-day-header">{{ $d }}</div>
                    @endforeach
                </div>
                <div class="cal-grid" id="calBody" style="flex: 1;"></div>
            </div>
        </div>

        {{-- DAILY SCHEDULE --}}
        <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; height: 100%; min-height: 0; max-height: 520px;">
            <div style="padding: 1.1rem 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph ph-calendar-check" style="color: var(--asr-green);"></i> Jadwal: <span id="scheduleDateLabel">{{ now()->format('d M Y') }}</span>
                </h2>
            </div>
            <div id="dailyScheduleList" style="flex: 1; overflow-y: auto; padding: 0.5rem 0;">
                <div style="padding:3.5rem 1.5rem;text-align:center;color:var(--text-muted);display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">
                    <i class="ph ph-calendar-blank" style="font-size:2.5rem;margin-bottom:0.75rem;opacity:0.6;"></i>
                    <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.25rem;">Pilih Tanggal</div>
                    <div style="font-size:0.78rem;opacity:0.8;">Klik tanggal pada kalender untuk melihat jadwal kegiatan.</div>
                </div>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- GRAFIK BARIS 1: Tanaman Sering Ditanam + Sering Dipanen --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="responsive-grid-2">

        {{-- Chart: Tanaman Paling Sering Ditanam --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'print')">Cetak grafik</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'png')">Unduh gambar PNG</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'jpeg')">Unduh gambar JPEG</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'svg')">Unduh vektor SVG</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'csv')">Unduh CSV</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'xls')">Unduh XLS</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostPlanted', 'table')">Lihat tabel data</a>
                </div>
            </div>
            <div class="chart-card-title" style="padding-right: 30px;">
                <i class="ph ph-seed" style="color:#16a34a;font-size:1.3rem;"></i>
                Tanaman Paling Sering Ditanam
                <span style="margin-left:auto;font-size:0.72rem;background:#dcfce7;color:#15803d;padding:2px 8px;border-radius:20px;font-weight:600;">Top 8</span>
            </div>
            <div style="position: relative; height: 260px;">
                <canvas id="chartMostPlanted"></canvas>
            </div>
            @if(empty($mostPlantedLabels))
            <div style="text-align:center;color:#9ca3af;font-size:0.85rem;padding:1rem;">Belum ada data penanaman</div>
            @endif
        </div>

        {{-- Chart: Tanaman Paling Sering Dipanen --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'print')">Cetak grafik</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'png')">Unduh gambar PNG</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'jpeg')">Unduh gambar JPEG</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'svg')">Unduh vektor SVG</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'csv')">Unduh CSV</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'xls')">Unduh XLS</a>
                    <a class="chart-export-item" onclick="exportChart('chartMostHarvested', 'table')">Lihat tabel data</a>
                </div>
            </div>
            <div class="chart-card-title" style="padding-right: 30px;">
                <i class="ph ph-basket" style="color:#ea580c;font-size:1.3rem;"></i>
                Tanaman Paling Sering Dipanen
                <span style="margin-left:auto;font-size:0.72rem;background:#fff7ed;color:#c2410c;padding:2px 8px;border-radius:20px;font-weight:600;">Top 8</span>
            </div>
            <div style="position: relative; height: 260px;">
                <canvas id="chartMostHarvested"></canvas>
            </div>
            @if(empty($mostHarvestedLabels))
            <div style="text-align:center;color:#9ca3af;font-size:0.85rem;padding:1rem;">Belum ada data panen</div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- GRAFIK BARIS 2: Occupancy + Proporsi Status + Populasi GH --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <div class="responsive-grid-2">

        {{-- Chart: Perputaran / Occupancy Rate per Greenhouse --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'print')">Cetak grafik</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'png')">Unduh gambar PNG</a>
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'jpeg')">Unduh gambar JPEG</a>
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'svg')">Unduh vektor SVG</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'csv')">Unduh CSV</a>
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'xls')">Unduh XLS</a>
                    <a class="chart-export-item" onclick="exportChart('chartRotation', 'table')">Lihat tabel data</a>
                </div>
            </div>
            <div class="chart-card-title" style="padding-right: 30px;">
                <i class="ph ph-arrows-clockwise" style="color:#7c3aed;font-size:1.3rem;"></i>
                Perputaran & Tingkat Keterisian per Green House
            </div>
            <div style="position: relative; height: 240px;">
                <canvas id="chartRotation"></canvas>
            </div>
        </div>

        {{-- Chart: Distribusi Green House (Pie Chart) --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'print')">Cetak grafik</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'png')">Unduh gambar PNG</a>
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'jpeg')">Unduh gambar JPEG</a>
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'svg')">Unduh vektor SVG</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'csv')">Unduh CSV</a>
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'xls')">Unduh XLS</a>
                    <a class="chart-export-item" onclick="exportChart('chartGHDistribution', 'table')">Lihat tabel data</a>
                </div>
            </div>
            <div class="chart-card-title" style="padding-right: 30px;">
                <i class="ph ph-chart-pie-slice" style="color:#10b981;font-size:1.3rem;"></i>
                Distribusi Green House
            </div>
            <div style="position: relative; height: 210px; display: flex; justify-content: center;">
                <canvas id="chartGHDistribution"></canvas>
            </div>
            
            <!-- Custom Legend -->
            <div id="ghLegendContainer" style="margin-top: 1rem; padding: 0 0.5rem;"></div>

            <div style="text-align:center; font-size: 0.72rem; color: var(--text-muted); margin-top: 0.75rem;">
                * Klik bidang pada grafik untuk melihat daftar tanaman
            </div>
        </div>

    </div>

    <!-- Modal for GH Plants -->
    <div id="ghPlantsModal" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter: blur(2px);">
        <div style="background:var(--card-bg, #ffffff); width:450px; max-width:90%; margin: 80px auto; border-radius:12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; flex-direction: column;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <h3 id="ghPlantsModalTitle" style="margin:0; color:var(--text-main); font-size: 1.15rem; font-weight: 700;">Tanaman di GH</h3>
                <button onclick="document.getElementById('ghPlantsModal').style.display='none'" style="border:none; background:transparent; font-size:1.2rem; cursor:pointer; color: var(--text-muted);"><i class="ph ph-x"></i></button>
            </div>
            <div style="padding: 1.5rem;">
                <ul id="ghPlantsModalList" style="list-style: none; padding: 0; margin: 0; border: 1px solid var(--border-color); border-radius: 8px;">
                    <!-- Populated by JS -->
                </ul>
            </div>
        </div>
    </div>



    <!-- Modal for Siap Panen Details -->
    <div id="siapPanenModal" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter: blur(2px);">
        <div style="background:var(--card-bg, #ffffff); width:650px; max-width:95%; margin: 60px auto; border-radius:12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; flex-direction: column; max-height:85vh;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <h3 style="margin:0; color:var(--text-main); font-size: 1.15rem; font-weight: 700;"><i class="ph ph-trophy" style="color:#ca8a04; margin-right:8px;"></i> Rincian Tanaman Siap Panen</h3>
                <button onclick="document.getElementById('siapPanenModal').style.display='none'" style="border:none; background:transparent; font-size:1.2rem; cursor:pointer; color: var(--text-muted);"><i class="ph ph-x"></i></button>
            </div>
            <div style="padding: 1.5rem; overflow-y:auto;">
                @if(!isset($readyToHarvestItems) || $readyToHarvestItems->isEmpty())
                    <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                        <i class="ph ph-leaf" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                        Belum ada tanaman yang memasuki masa panen.
                    </div>
                @else
                    @foreach($readyToHarvestItems as $plantName => $holes)
                        @php
                            $totalHoles = $holes->count();
                            $locations = [];
                            foreach($holes as $hole) {
                                $gh = optional(optional(optional($hole->row)->rack)->greenhouse)->name ?? 'GH Unknown';
                                $rack = optional(optional($hole->row)->rack)->name ?? 'Rak Unknown';
                                $age = \Carbon\Carbon::parse($hole->planted_at)->diffInDays(now());
                                $locKey = $gh . ' - ' . $rack;
                                if(!isset($locations[$locKey])) {
                                    $locations[$locKey] = ['count' => 0, 'ages' => []];
                                }
                                $locations[$locKey]['count']++;
                                $locations[$locKey]['ages'][] = $age;
                            }
                        @endphp
                        <div style="margin-bottom:1.5rem; border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">
                            <div style="background:var(--bg-light); padding:0.75rem 1rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                                <strong style="color:var(--text-main); font-size:1.05rem;">{{ $plantName ?? 'Tanaman Tidak Diketahui' }}</strong>
                                <span style="background:rgba(202, 138, 4, 0.15); color:#ca8a04; padding:3px 10px; border-radius:20px; font-weight:700; font-size:0.85rem;">{{ $totalHoles }} Lubang</span>
                            </div>
                            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                                <thead>
                                    <tr style="background:var(--card-bg, #fff); border-bottom:1px solid var(--border-color); color:var(--text-muted);">
                                        <th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Lokasi (GH / Rak)</th>
                                        <th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Jumlah</th>
                                        <th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Usia Tanaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $loc => $data)
                                    @php 
                                        $minAge = min($data['ages']);
                                        $maxAge = max($data['ages']);
                                        $ageStr = $minAge == $maxAge ? $minAge . ' hari' : $minAge . ' - ' . $maxAge . ' hari';
                                    @endphp
                                    <tr style="border-bottom:1px solid var(--border-color);">
                                        <td style="padding:0.75rem 1rem; color:var(--text-main);">{{ $loc }}</td>
                                        <td style="padding:0.75rem 1rem; text-align:center; font-weight:600; color:var(--text-main);">{{ $data['count'] }}</td>
                                        <td style="padding:0.75rem 1rem; text-align:center; color:var(--text-muted);">{{ $ageStr }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- INVENTORY SECTION --}}
    {{-- INVENTORY SECTION --}}
    <style>
        .inv-card-premium {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            background: #ffffff;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .inv-card-premium:hover {
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .inv-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(to bottom right, #ffffff, #f8fafc);
        }
        .inv-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        .inv-list {
            max-height: 240px;
            overflow-y: auto;
            padding: 0 1.5rem;
        }
        .inv-list::-webkit-scrollbar {
            width: 5px;
        }
        .inv-list::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .inv-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .inv-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .inv-item:last-child {
            border-bottom: none;
        }
    </style>
    <div style="margin-top: 2rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
            <h2 style="font-size:1.2rem;font-weight:800;color:var(--text-main);display:flex;align-items:center;gap:0.6rem;margin:0;">
                <i class="ph-fill ph-stack" style="color:var(--asr-green); font-size:1.5rem;"></i> 
                <span>Stok Inventaris Real-time</span>
            </h2>
            <a href="{{ route('hydroponics.inventory') }}" style="font-size:0.85rem;color:white;background:var(--asr-green);padding:0.5rem 1.25rem;border-radius:8px;text-decoration:none;font-weight:600;display:flex;align-items:center;gap:0.5rem;transition:all 0.2s;box-shadow:0 4px 10px rgba(22,163,74,0.25);">
                Kelola Inventaris <i class="ph ph-arrow-right"></i>
            </a>
        </div>
        <div class="responsive-grid-inv" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:1.5rem;">
            @foreach(array_diff_key($categoryConfig, ['lainnya' => '']) as $typeKey => $cfg)
                @php $items = $inventoryItems->where('type', $typeKey); @endphp
                <div class="inv-card-premium">
                    <div class="inv-header">
                        <div style="display:flex; align-items:center; gap:1rem;">
                            <div class="inv-icon-box" style="background:{{ $cfg['color'] }}15; color:{{ $cfg['color'] }}; box-shadow: 0 4px 12px {{ $cfg['color'] }}15;">
                                <i class="{{ $cfg['icon'] }}"></i>
                            </div>
                            <div>
                                <h3 style="margin:0; font-weight:800; font-size:1.1rem; color:#1e293b; letter-spacing:-0.2px;">{{ $cfg['label'] }}</h3>
                                <span style="font-size:0.75rem; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">{{ $items->count() }} Jenis Item</span>
                            </div>
                        </div>
                    </div>
                    <div style="padding:0; flex-grow:1; background:#ffffff;">
                        <div class="inv-list">
                        @forelse($items as $inv)
                        <div class="inv-item">
                            <div style="display:flex; flex-direction:column; gap:0.2rem;">
                                <span style="font-size:0.95rem; color:#334155; font-weight:700;">{{ $inv->name }}</span>
                                <span style="font-size:0.75rem; color:#94a3b8; font-weight:500;">
                                    Batas min: 10 {{ $inv->unit }}
                                </span>
                            </div>
                            <div style="display:flex; align-items:center; justify-content:center; padding:0.4rem 0.8rem; border-radius:8px; background:{{ $inv->quantity < 10 ? '#fef2f2' : '#f8fafc' }}; border:1px solid {{ $inv->quantity < 10 ? '#fecaca' : '#e2e8f0' }}; min-width:80px; box-shadow:inset 0 1px 2px rgba(0,0,0,0.02);">
                                <span style="font-size:1rem; font-weight:800; color:{{ $inv->quantity < 10 ? '#ef4444' : '#0f172a' }};">
                                    {{ number_format($inv->quantity,0,',','.') }}
                                </span>
                                <span style="font-size:0.8rem; color:{{ $inv->quantity < 10 ? '#ef4444' : '#64748b' }}; font-weight:700; margin-left:4px;">{{ $inv->unit }}</span>
                            </div>
                        </div>
                        @empty
                        <div style="padding:3.5rem 0; text-align:center; display:flex; flex-direction:column; align-items:center; gap:0.75rem;">
                            <div style="width:64px; height:64px; border-radius:50%; background:#f1f5f9; display:flex; align-items:center; justify-content:center; box-shadow:inset 0 2px 4px rgba(0,0,0,0.05);">
                                <i class="ph ph-package" style="font-size:2rem; color:#cbd5e1;"></i>
                            </div>
                            <div>
                                <div style="color:#64748b; font-size:0.95rem; font-weight:700;">Belum ada item</div>
                                <div style="color:#94a3b8; font-size:0.8rem;">Stok kosong</div>
                            </div>
                        </div>
                        @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Add Event Modal --}}
<div class="modal-overlay" id="addEventModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:14px; padding:1.5rem; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="color:#16a34a; font-size:1.1rem; font-weight:700; margin:0;"><i class="ph ph-calendar-plus"></i> Tambah Kegiatan</h3>
            <button onclick="document.getElementById('addEventModal').classList.remove('open')" style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#6b7280;">×</button>
        </div>
        <form method="POST" action="{{ route('hydroponics.calendar.store') }}">
            @csrf
            <div style="margin-bottom: 0.9rem;">
                <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem;">Judul Kegiatan <span style="color:#dc2626;">*</span></label>
                <input type="text" name="title" required style="width:100%; padding:0.55rem 0.8rem; border:1.5px solid #d1d5db; border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:0.9rem;">
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem;">Tanggal <span style="color:#dc2626;">*</span></label>
                    <input type="date" name="event_date" required value="{{ now()->format('Y-m-d') }}" style="width:100%; padding:0.55rem 0.8rem; border:1.5px solid #d1d5db; border-radius:8px; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem;">Waktu</label>
                    <input type="time" name="event_time" style="width:100%; padding:0.55rem 0.8rem; border:1.5px solid #d1d5db; border-radius:8px; box-sizing:border-box;">
                </div>
            </div>
            <div style="margin-bottom: 0.9rem;">
                <label style="display:block; font-size:0.82rem; font-weight:600; margin-bottom:0.35rem;">Deskripsi</label>
                <textarea name="description" rows="3" style="width:100%; padding:0.55rem 0.8rem; border:1.5px solid #d1d5db; border-radius:8px; box-sizing:border-box; resize:vertical;"></textarea>
            </div>
            <button type="submit" style="width:100%; padding:0.65rem; background:linear-gradient(135deg, #16a34a, #15803d); color:white; border:none; border-radius:9px; font-weight:600; cursor:pointer;">
                Simpan Kegiatan
            </button>
        </form>
    </div>
</div>

{{-- View Event Modal --}}
<div class="modal-overlay" id="viewEventModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:14px; padding:1.5rem; width:100%; max-width:440px; box-shadow:0 20px 60px rgba(0,0,0,0.2); position:relative;">
        <button onclick="document.getElementById('viewEventModal').classList.remove('open')" style="position:absolute; top:1.25rem; right:1.25rem; background:none; border:none; font-size:1.5rem; cursor:pointer; color:#6b7280; line-height:1;">&times;</button>
        <div id="viewEventModalContent"></div>
    </div>
</div>
<style> .modal-overlay.open { display:flex !important; } </style>

<script>
// ══════════════════════════════════════════════════════════════
//  DATA FROM BLADE
// ══════════════════════════════════════════════════════════════
const calendarData  = {!! $calendarJson !!};
const rotationData  = {!! $rotationJson !!};
const plantStageData= {!! $plantStageJson !!};

let currentYear, currentMonth;
const monthsStr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

window.calFilters = {
    semai: true, tanam: true, remaja: true, harvest: true, custom: true
};
window.currentActiveDate = null;

function toggleCalFilter(type, el) {
    window.calFilters[type] = !window.calFilters[type];
    if (!window.calFilters[type]) {
        el.style.textDecoration = 'line-through';
        el.style.opacity = '0.5';
    } else {
        el.style.textDecoration = 'none';
        el.style.opacity = '1';
    }
    renderCalendar();
    if (window.currentActiveDate) {
        renderDailySchedule(window.currentActiveDate);
    }
}

// ══════════════════════════════════════════════════════════════
//  CALENDAR
// ══════════════════════════════════════════════════════════════
function initCalendar() {
    const t = new Date();
    currentYear  = t.getFullYear();
    currentMonth = t.getMonth();
    renderCalendar();
    const todayStr = currentYear + '-' + pad(currentMonth+1) + '-' + pad(t.getDate());
    renderDailySchedule(todayStr);
    buildMonthDropdown();
    buildYearDropdown();
}

function buildMonthDropdown() {
    let html = '';
    monthsStr.forEach((m, i) => {
        const btnClass = i === currentMonth ? 'cal-btn active' : 'cal-btn';
        html += `<button type="button" class="${btnClass}" onclick="selectMonth(${i})">${m.substring(0,3)}</button>`;
    });
    document.getElementById('monthGrid').innerHTML = html;
}
function buildYearDropdown() {
    // Set the input value to current year
    const inp = document.getElementById('yearInput');
    if (inp) inp.value = currentYear;
}
function nudgeYear(d) {
    const inp = document.getElementById('yearInput');
    const newY = parseInt(inp.value) + d;
    if (newY >= 1990 && newY <= 2099) inp.value = newY;
}
function selectYearFromInput(val) {
    const y = parseInt(val);
    if (y >= 1990 && y <= 2099) { currentYear = y; renderCalendar(); }
}
function confirmYear() {
    const inp = document.getElementById('yearInput');
    const y = parseInt(inp.value);
    if (y >= 1990 && y <= 2099) { currentYear = y; }
    document.getElementById('yearDropdown').style.display = 'none';
    renderCalendar();
}
function toggleMonthSelect() {
    document.getElementById('monthDropdown').style.display = document.getElementById('monthDropdown').style.display === 'none' ? 'block' : 'none';
    document.getElementById('yearDropdown').style.display  = 'none';
}
function toggleYearSelect() {
    const dd = document.getElementById('yearDropdown');
    const isOpen = dd.style.display !== 'none';
    dd.style.display = isOpen ? 'none' : 'block';
    document.getElementById('monthDropdown').style.display = 'none';
    if (!isOpen) {
        buildYearDropdown();
        // Briefly highlight the input
        setTimeout(() => { const inp=document.getElementById('yearInput'); if(inp){inp.select();} }, 50);
    }
}
function selectMonth(m) { currentMonth = m; document.getElementById('monthDropdown').style.display = 'none'; renderCalendar(); }
function selectYear(y)  { currentYear  = y; document.getElementById('yearDropdown').style.display  = 'none'; renderCalendar(); }
function changeMonth(d) {
    currentMonth += d;
    if (currentMonth > 11) { currentMonth = 0; currentYear++; }
    if (currentMonth < 0)  { currentMonth = 11; currentYear--; }
    renderCalendar();
}
function pad(n) { return String(n).padStart(2,'0'); }

document.addEventListener('click', function(e) {
    if (!e.target.closest('.cal-month-selector') && !e.target.closest('#monthDropdown'))
        document.getElementById('monthDropdown').style.display = 'none';
    if (!e.target.closest('.cal-year-selector') && !e.target.closest('#yearDropdown'))
        document.getElementById('yearDropdown').style.display  = 'none';
});

function showSiapPanenModal() {
    document.getElementById('siapPanenModal').style.display = 'block';
}

function renderCalendar() {
    const today    = new Date();
    const todayStr = today.getFullYear() + '-' + pad(today.getMonth()+1) + '-' + pad(today.getDate());
    const first    = new Date(currentYear, currentMonth, 1);
    const last     = new Date(currentYear, currentMonth + 1, 0);

    document.getElementById('calMonthText').textContent = monthsStr[currentMonth];
    document.getElementById('calYearText').textContent  = currentYear;
    
    // Update dashboard stats automatically
    if (typeof fetchProduksiStats === 'function') {
        fetchProduksiStats(currentMonth + 1, currentYear);
    }
    if (typeof updateSummaryCards === 'function') {
        updateSummaryCards(currentMonth + 1, currentYear);
    }

    document.querySelectorAll('#monthGrid .cal-btn').forEach((btn, i) => {
        btn.classList.toggle('active', i === currentMonth);
    });
    document.querySelectorAll('#yearScroller .cal-btn').forEach(btn => {
        btn.classList.toggle('active', parseInt(btn.textContent) === currentYear);
    });

    const typeColors = {
        semai: '#16a34a', tanam: '#2563eb', remaja: '#d97706',
        harvest: '#e11d48', planting: '#16a34a', custom: '#0891b2'
    };

    const firstDayIndex = first.getDay();
    const prevLast = new Date(currentYear, currentMonth, 0);
    const prevDaysCount = prevLast.getDate();

    let html = '';
    // Previous month padding days
    for (let i = firstDayIndex - 1; i >= 0; i--) {
        const num = prevDaysCount - i;
        html += `<div class="cal-day other-month"><div class="cal-day-num">${num}</div></div>`;
    }

    // Current month days
    for (let d = 1; d <= last.getDate(); d++) {
        const dateStr = currentYear + '-' + pad(currentMonth+1) + '-' + pad(d);
        const isToday = dateStr === todayStr;
        let evList  = calendarData[dateStr] || [];
        evList = evList.filter(ev => {
            let type = ev.type;
            if (type === 'planting') type = 'tanam';
            return window.calFilters[type] !== false;
        });
        const evCount = evList.length;

        let typeCounts = {};
        let totalCount = 0;
        evList.forEach(ev => {
            if (!typeCounts[ev.type]) typeCounts[ev.type] = 0;
            typeCounts[ev.type] += (ev.hole_count || 1);
            totalCount++;
        });

        let dots = '';
        let typeLabels = { semai: 'Semai', tanam: 'Tanam', remaja: 'Remaja', harvest: 'Panen', custom: 'Keg.' };
        
        Object.keys(typeCounts).slice(0, 3).forEach(type => {
            const color = typeColors[type] || '#9ca3af';
            const count = typeCounts[type];
            const label = typeLabels[type] || type;
            dots += `<div style="font-size: 0.65rem; background: ${color}15; border-left: 2px solid ${color}; color: ${color}; padding: 2px 4px; border-radius: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: flex; justify-content: space-between; margin-bottom: 2px; font-weight: 700; line-height:1;">
                <span>${label}</span>
                <span style="opacity:0.8;">${count > 1 ? count : ''}</span>
            </div>`;
        });
        
        let remainingTypes = Object.keys(typeCounts).length - 3;
        if (remainingTypes > 0) {
            dots += `<div style="font-size:0.6rem;color:var(--text-muted);font-weight:700;text-align:center;margin-top:2px;">+${remainingTypes} lainnya</div>`;
        }

        html += `<div class="cal-day${isToday?' today':''}${evCount>0?' has-event':''}"
                     data-date="${dateStr}"
                     onclick="renderDailySchedule('${dateStr}')">
                    <div class="cal-day-num" style="margin-bottom:4px; text-align:right;">${d}</div>
                    <div style="display:flex;flex-direction:column;gap:1px;width:100%;">${dots}</div>
                 </div>`;
    }

    // Next month padding days to complete row grid
    const totalCells = firstDayIndex + last.getDate();
    const nextDaysNeeded = (totalCells % 7 === 0) ? 0 : 7 - (totalCells % 7);
    for (let n = 1; n <= nextDaysNeeded; n++) {
        html += `<div class="cal-day other-month"><div class="cal-day-num">${n}</div></div>`;
    }

    document.getElementById('calBody').innerHTML = html;
}

function renderDailySchedule(dateStr) {
    const [y,m,d] = dateStr.split('-');
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    document.getElementById('scheduleDateLabel').textContent = `${parseInt(d)} ${months[parseInt(m)-1]} ${y}`;

    // Active day selection highlight
    document.querySelectorAll('.cal-day').forEach(el => el.classList.remove('selected-day'));
    const clickedDay = document.querySelector(`.cal-day[data-date="${dateStr}"]`);
    if (clickedDay) clickedDay.classList.add('selected-day');

    window.currentActiveDate = dateStr;
    let evList  = calendarData[dateStr] || [];
    evList = evList.filter(ev => {
        let type = ev.type;
        if (type === 'planting') type = 'tanam';
        return window.calFilters[type] !== false;
    });
    window.currentEvList = evList;
    const container = document.getElementById('dailyScheduleList');

    if (evList.length === 0) {
        container.innerHTML = `<div style="padding:3.5rem 1.5rem;text-align:center;color:var(--text-muted);display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;">
            <i class="ph ph-calendar-blank" style="font-size:2.5rem;margin-bottom:0.75rem;opacity:0.6;"></i>
            <div style="font-weight:600;font-size:0.9rem;margin-bottom:0.25rem;">Tidak Ada Kegiatan</div>
            <div style="font-size:0.78rem;opacity:0.8;">Tidak ada jadwal tercatat pada tanggal ini.</div>
        </div>`;
        return;
    }

    // Stage config
    const stageMap = {
        semai:   { icon:'ph-seedling',  color:'#16a34a', bg:'#dcfce7', label:'Fase Penyemaian',  emoji:'🌱' },
        tanam:   { icon:'ph-plant',     color:'#2563eb', bg:'#dbeafe', label:'Fase Penanaman',   emoji:'🪴' },
        remaja:  { icon:'ph-leaf',      color:'#9333ea', bg:'#f3e8ff', label:'Fase Remaja',       emoji:'🌿' },
        harvest: { icon:'ph-basket',    color:'#e11d48', bg:'#ffe4e6', label:'Jadwal Panen',      emoji:'🌾' },
        planting:{ icon:'ph-plant',     color:'#16a34a', bg:'#dcfce7', label:'Penanaman',         emoji:'🌱' },
        custom:  { icon:'ph-bookmark',  color:'#0891b2', bg:'#ecfeff', label:'Kegiatan',          emoji:'📌' },
    };

    let html = '';
    evList.forEach((ev, idx) => {
        const cfg = stageMap[ev.type] || stageMap.custom;
        let title, subtitle, badge = '';

        if (ev.type === 'harvest') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Panen ${ev.plant_name}</span>`;
            subtitle = `${ev.location} · Umur: ${ev.days_old} hari`;
            if (ev.is_ready)      badge = `<span style="color:#dc2626;background:#fee2e2;padding:2px 7px;border-radius:4px;font-size:0.65rem;font-weight:700;">SIAP!</span>`;
            if (ev.harvested_by)  subtitle += ` · <i class="ph ph-user" style="font-size:0.7rem;"></i> ${ev.harvested_by}`;
        } else if (ev.type === 'semai') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Penyemaian ${ev.plant_name}</span>`;
            subtitle = `${ev.location} · Hari ke-${ev.stage_day}`;
            badge    = `<span style="color:#15803d;font-size:0.7rem;font-weight:600;">${ev.time || ''}</span>`;
        } else if (ev.type === 'tanam') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Fase Tanam ${ev.plant_name}</span>`;
            subtitle = `${ev.location} · Hari ke-${ev.stage_day}`;
        } else if (ev.type === 'remaja') {
            title    = `<span style="font-weight:700;">${cfg.emoji} Fase Remaja ${ev.plant_name}</span>`;
            subtitle = `${ev.location} · Hari ke-${ev.stage_day}`;
        } else if (ev.type === 'custom') {
            title    = `<span style="font-weight:700;">${cfg.emoji} ${ev.title}</span>`;
            subtitle = ev.description || 'Kegiatan Kustom';
            badge    = ev.time ? `<span style="color:#3b82f6;font-size:0.7rem;font-weight:600;">${ev.time}</span>` : '';
        } else {
            title    = `<span style="font-weight:700;">${cfg.emoji} ${ev.plant_name}</span>`;
            subtitle = ev.location || '';
        }

        if (ev.hole_count && ev.hole_count > 1) {
            title += ` <span style="font-size:0.75rem;font-weight:normal;color:var(--text-muted);">(${ev.hole_count} lubang)</span>`;
        }


        let plantRows = '';
        if (ev.plants_list && ev.plants_list.length > 0) {
            ev.plants_list.forEach(p => {
                let pIcon = 'ph-leaf';
                if (p.name.toLowerCase().includes('pakcoy')) pIcon = 'ph-plant';
                if (p.name.toLowerCase().includes('selada')) pIcon = 'ph-flower-lotus';
                if (p.name.toLowerCase().includes('bayam')) pIcon = 'ph-clover';
                
                plantRows += `
                <div style="display:flex; justify-content:space-between; align-items:center; padding: 0.5rem 0; border-bottom: 1px dashed var(--border-color);">
                    <div style="font-size: 0.82rem; font-weight: 600; color: var(--text-main); display:flex; align-items:center; gap:0.5rem;">
                        <span style="background:var(--bg-body); width:24px; height:24px; display:flex; align-items:center; justify-content:center; border-radius:6px; color:var(--text-muted);"><i class="ph ${pIcon}"></i></span>
                        ${p.name}
                    </div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); text-align:right;">
                        Rak ${p.racks}
                    </div>
                </div>`;
            });
            // remove last border
            plantRows = plantRows.replace(/border-bottom: 1px dashed var\(--border-color\);">$/, '">');
        }

        if (ev.type === 'custom') {
            html += `
            <div onclick="openEventDetails(${idx})" style="cursor:pointer; display:flex;align-items:center;gap:0.75rem;padding:0.75rem 1.25rem;border-bottom:1px solid var(--border-color); transition:background 0.2s;" onmouseover="this.style.background='var(--bg-body)'" onmouseout="this.style.background='transparent'">
                <div style="width:36px;height:36px;border-radius:50%;background:${cfg.bg};display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="ph ${cfg.icon}" style="color:${cfg.color};font-size:1.1rem;"></i>
                </div>
                <div style="flex:1;min-width:0;line-height:1.4;">
                    <div style="font-size:0.875rem;color:var(--text-main);">${title}</div>
                    <div style="font-size:0.72rem;color:var(--text-muted);line-height:1.4;margin-top:0.2rem;">${subtitle}</div>
                </div>
                <div>${badge}</div>
            </div>`;
        } else {
            html += `
            <div onclick="openEventDetails(${idx})" style="cursor:pointer; margin: 0.8rem 1rem; border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.02); transition:box-shadow 0.2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.08)'" onmouseout="this.style.boxShadow='0 2px 5px rgba(0,0,0,0.02)'">
                <div style="display:flex; align-items:center; gap:0.75rem; padding: 0.75rem 1rem; background: ${cfg.bg}; border-bottom: 1px solid rgba(0,0,0,0.05);">
                    <div style="width:34px;height:34px;border-radius:8px;background:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
                        <i class="ph ${cfg.icon}" style="color:${cfg.color};font-size:1.1rem;"></i>
                    </div>
                    <div style="flex:1;min-width:0;line-height:1.3;">
                        <div style="font-size:0.9rem;font-weight:700;color:var(--text-main);">${ev.gh_name || cfg.label}</div>
                        <div style="font-size:0.75rem;color:${cfg.color};font-weight:600;margin-top:2px;">${cfg.label} &bull; ${ev.hole_count ? ev.hole_count.toLocaleString('id-ID') + ' lubang' : ''}</div>
                    </div>
                    <div>${badge}</div>
                </div>
                <div style="padding: 0.25rem 1rem;">
                    ${plantRows}
                </div>
            </div>`;
        }
    });
    container.innerHTML = html;
}

function openEventDetails(idx) {
    const ev = window.currentEvList[idx];
    if(!ev) return;
    
    const stageMap = {
        semai:   { icon:'ph-seedling',  color:'#16a34a', bg:'#dcfce7', label:'Fase Penyemaian',  emoji:'🌱' },
        tanam:   { icon:'ph-plant',     color:'#2563eb', bg:'#dbeafe', label:'Fase Penanaman',   emoji:'🪴' },
        remaja:  { icon:'ph-leaf',      color:'#9333ea', bg:'#f3e8ff', label:'Fase Remaja',       emoji:'🌿' },
        harvest: { icon:'ph-basket',    color:'#e11d48', bg:'#ffe4e6', label:'Jadwal Panen',      emoji:'🌾' },
        planting:{ icon:'ph-plant',     color:'#16a34a', bg:'#dcfce7', label:'Penanaman',         emoji:'🌱' },
        custom:  { icon:'ph-bookmark',  color:'#0891b2', bg:'#ecfeff', label:'Kegiatan',          emoji:'📌' },
    };
    const cfg = stageMap[ev.type] || stageMap.custom;

    let title, subtitle;
    if (ev.type === 'harvest') {
        title    = `Panen ${ev.plant_name}`;
        subtitle = ev.location || '';
    } else if (ev.type === 'semai') {
        title    = `Penyemaian ${ev.plant_name}`;
        subtitle = ev.location || '';
    } else if (ev.type === 'tanam') {
        title    = `Fase Tanam ${ev.plant_name}`;
        subtitle = ev.location || '';
    } else if (ev.type === 'remaja') {
        title    = `Fase Remaja ${ev.plant_name}`;
        subtitle = ev.location || '';
    } else if (ev.type === 'custom') {
        title    = ev.title;
        subtitle = '';
    } else {
        title    = ev.plant_name;
        subtitle = ev.location || '';
    }

    let dateDisplay = window.currentActiveDate; 
    let dParts = window.currentActiveDate.split('-');
    if(dParts.length === 3) {
        const mNames = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        dateDisplay = dParts[2] + ' ' + mNames[parseInt(dParts[1], 10)-1] + ' ' + dParts[0];
    }

    let contentHtml = `
        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:1rem; padding-right: 2rem;">
            <span style="background:${cfg.bg}; color:${cfg.color}; padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:700; display:flex; align-items:center; gap:0.3rem;"><i class="ph ${cfg.icon}"></i> ${cfg.label.toUpperCase()}</span>
            ` + (ev.time ? `<span style="background:#f3f4f6; color:#4b5563; padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:700;"><i class="ph ph-clock"></i> ${ev.time}</span>` : '') + `
            ` + (ev.is_ready ? `<span style="background:#fee2e2; color:#dc2626; padding:4px 8px; border-radius:6px; font-size:0.75rem; font-weight:700;">SIAP PANEN</span>` : '') + `
        </div>
        <h3 style="margin:0 0 0.75rem 0; font-size:1.2rem; color:var(--text-main); font-weight:700; line-height:1.3;">${title}</h3>
        
        <div style="display:flex; align-items:center; gap:0.5rem; color:var(--text-muted); font-size:0.9rem; margin-bottom:1.5rem;">
            <i class="ph ph-calendar-blank" style="font-size:1.1rem;"></i> 
            <span>${dateDisplay}</span>
        </div>
        
        <div style="border-top:1px dashed var(--border-color); padding-top:1.25rem;">
            ` + (subtitle ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Lokasi</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;"><i class="ph ph-map-pin" style="color:var(--text-muted);"></i> ${subtitle}</div></div>` : '') + `
            ` + (ev.description ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Deskripsi</div><div style="font-size:0.9rem; color:var(--text-main); line-height:1.5;">${ev.description}</div></div>` : '') + `
            ` + (ev.harvested_by ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Dipanen Oleh</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;"><i class="ph ph-user" style="color:var(--text-muted);"></i> ${ev.harvested_by}</div></div>` : '') + `
            ` + (ev.days_old ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Umur Tanaman</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;">${ev.days_old} hari</div></div>` : '') + `
            ` + (ev.stage_day ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Lama Fase</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;">Hari ke-${ev.stage_day} pada fase ini</div></div>` : '') + `
            ` + (ev.hole_count ? `<div style="margin-bottom:1rem;"><div style="font-size:0.75rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:0.25rem;">Jumlah Lubang</div><div style="font-size:0.9rem; color:var(--text-main); font-weight:500;">${ev.hole_count.toLocaleString('id-ID')} lubang tanam</div></div>` : '') + `
        </div>
    `;

    document.getElementById('viewEventModalContent').innerHTML = contentHtml;
    document.getElementById('viewEventModal').classList.add('open');
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});

// ══════════════════════════════════════════════════════════════
//  CHARTS
// ══════════════════════════════════════════════════════════════
function initCharts() {
    const isDark     = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor  = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.06)';
    const textColor  = isDark ? '#94A3B8' : '#64748B';
    const cardBg     = isDark ? '#1E293B' : '#ffffff';

    const greenPalette  = ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0','#dcfce7','#f0fdf4','#15803d'];
    const orangePalette = ['#ea580c','#f97316','#fb923c','#fdba74','#fed7aa','#ffedd5','#fff7ed','#c2410c'];
    const fontOpts = { family: 'Inter', size: 12 };

    // ── 1. Tanaman Paling Sering Ditanam ──────────────────────
    const mostPlantedCtx = document.getElementById('chartMostPlanted');
    if (mostPlantedCtx) {
        new Chart(mostPlantedCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($mostPlantedLabels) !!},
                datasets: [{
                    label: 'Jumlah Lubang Ditanam',
                    data:   {!! json_encode($mostPlantedValues) !!},
                    backgroundColor: greenPalette,
                    borderRadius: 7,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: fontOpts } },
                    y: { grid: { display: false }, ticks: { color: textColor, font: fontOpts } }
                }
            }
        });
    }

    // ── 2. Tanaman Paling Sering Dipanen ──────────────────────
    const mostHarvestedCtx = document.getElementById('chartMostHarvested');
    if (mostHarvestedCtx) {
        new Chart(mostHarvestedCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($mostHarvestedLabels) !!},
                datasets: [{
                    label: 'Jumlah Panen',
                    data:   {!! json_encode($mostHarvestedValues) !!},
                    backgroundColor: orangePalette,
                    borderRadius: 7,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: fontOpts } },
                    y: { grid: { display: false }, ticks: { color: textColor, font: fontOpts } }
                }
            }
        });
    }

    // ── 3. Perputaran / Tingkat Keterisian per Green House ───────────────
    const rotCtx = document.getElementById('chartRotation');
    if (rotCtx && rotationData.length > 0) {
        new Chart(rotCtx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: rotationData.map(r => r.name),
                datasets: [
                    {
                        label: 'Ditanam',
                        data:  rotationData.map(r => r.planted),
                        backgroundColor: 'rgba(22,163,74,0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'Siap Panen',
                        data:  rotationData.map(r => r.ready),
                        backgroundColor: 'rgba(234,88,12,0.8)',
                        borderRadius: 6
                    },
                    {
                        label: 'Sudah Panen',
                        data:  rotationData.map(r => r.harvested),
                        backgroundColor: 'rgba(124,58,237,0.7)',
                        borderRadius: 6
                    },
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: textColor, font: fontOpts, usePointStyle: true, boxWidth: 10 } },
                    tooltip: {
                        callbacks: {
                            afterBody: (items) => {
                                const rd = rotationData[items[0].dataIndex];
                                return [`Keterisian: ${rd.rate}%`];
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor, font: fontOpts } },
                    y: { beginAtZero: true, grid: { color: gridColor }, ticks: { color: textColor, font: fontOpts } }
                }
            }
        });
    }

    // ── 3B. Distribusi Green House (Pie) ─────────────────────────
    const ghCtx = document.getElementById('chartGHDistribution');
    if (ghCtx) {
        const ghDistData = {!! json_encode($ghDistribution ?? []) !!};
        if (ghDistData && ghDistData.length > 0) {
            const labels = ghDistData.map(item => item.name);
            const data = ghDistData.map(item => item.racks);
            const bgColors = ['#10b981', '#f59e0b', '#06b6d4', '#ec4899', '#8b5cf6', '#3b82f6', '#ef4444', '#14b8a6', '#f97316'];
            
            new Chart(ghCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: bgColors.slice(0, labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: 10 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                                callbacks: {
                                label: function(context) {
                                    const val = context.parsed;
                                    const total = context.chart._metasets[context.datasetIndex].total;
                                    const percent = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${percent}% (${val} Rak)`;
                                }
                            }
                        }
                    },
                    onClick: (e, activeElements) => {
                        if (activeElements.length > 0) {
                            const dataIndex = activeElements[0].index;
                            const ghInfo = ghDistData[dataIndex];
                            
                            document.getElementById('ghPlantsModalTitle').innerText = 'Tanaman di ' + ghInfo.name;
                            const listEl = document.getElementById('ghPlantsModalList');
                            listEl.innerHTML = '';
                            
                            if (ghInfo.plants && ghInfo.plants.length > 0) {
                                ghInfo.plants.forEach((p, idx) => {
                                    let li = document.createElement('li');
                                    li.innerText = `${idx + 1}. ${p}`;
                                    li.style.padding = '0.75rem 1rem';
                                    li.style.borderBottom = idx < ghInfo.plants.length - 1 ? '1px solid var(--border-color)' : 'none';
                                    li.style.fontSize = '0.9rem';
                                    li.style.color = 'var(--text-main)';
                                    listEl.appendChild(li);
                                });
                            } else {
                                let li = document.createElement('li');
                                li.innerText = 'Tidak ada tanaman saat ini.';
                                li.style.padding = '1rem';
                                li.style.color = 'var(--text-muted)';
                                li.style.textAlign = 'center';
                                listEl.appendChild(li);
                            }
                            
                            document.getElementById('ghPlantsModal').style.display = 'block';
                        }
                    }
                }
            });

            // Build Custom HTML Legend
            const legendContainer = document.getElementById('ghLegendContainer');
            if (legendContainer) {
                let legendHtml = `<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-top: 1rem;">`;
                ghDistData.forEach((item, idx) => {
                    const color = bgColors[idx % bgColors.length];
                    legendHtml += `
                        <div style="display: flex; align-items: center; gap: 0.5rem; background: var(--bg-color); padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid var(--border-color);">
                            <div style="width: 14px; height: 14px; border-radius: 4px; background-color: ${color}; flex-shrink: 0;"></div>
                            <div style="line-height: 1.2;">
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-main);">${item.name}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">${item.racks} Rak</div>
                            </div>
                        </div>
                    `;
                });
                legendHtml += `</div>`;
                legendContainer.innerHTML = legendHtml;
            }
        }
    }
}

    function fetchProduksiStats(month, year) {
    const monthName = monthsStr[month - 1];

    if (document.getElementById('produksiTitleText')) {
        document.getElementById('produksiTitleText').textContent = `Produksi ${monthName} ${year}`;
    }
}


// ── Chart UI interactions & API Updates ─────────────────────────
function toggleChartMenu(btn) {
    document.querySelectorAll('.chart-export-dropdown.show').forEach(menu => {
        if (menu !== btn.nextElementSibling) menu.classList.remove('show');
    });
    btn.nextElementSibling.classList.toggle('show');
}
document.addEventListener('click', function(e) {
    if(!e.target.closest('.chart-export-wrapper')) {
        document.querySelectorAll('.chart-export-dropdown.show').forEach(menu => menu.classList.remove('show'));
    }
});

function exportChart(chartId, action) {
    const canvas = document.getElementById(chartId);
    if (!canvas) return;
    if (action === 'fullscreen') {
        const wrapper = canvas.closest('.chart-card');
        if (wrapper.requestFullscreen) { wrapper.requestFullscreen(); }
        else if (wrapper.webkitRequestFullscreen) { wrapper.webkitRequestFullscreen(); }
    } else if (action === 'png') {
        const link = document.createElement('a');
        link.download = chartId + '.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    } else if (action === 'jpeg') {
        const link = document.createElement('a');
        link.download = chartId + '.jpeg';
        link.href = canvas.toDataURL('image/jpeg', 1.0);
        link.click();
    } else if (action === 'print') {
        const win = window.open();
        win.document.write('<img src="' + canvas.toDataURL() + '" onload="window.print();window.close();" />');
    } else {
        alert("Fitur " + action + " sedang dalam pengembangan.");
    }
}


function updateSummaryCards(month, year) {
    if (!month || !year) {
        month = new Date().getMonth() + 1;
        year = new Date().getFullYear();
    }
    
    // Update title
    const monthsStr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    document.getElementById('summaryGlobalTitle').textContent = 'Ringkasan ' + monthsStr[month - 1] + ' ' + year;

      // Set loading state
      const ids = ['card-lubang-kosong', 'card-lubang-terisi', 'card-siap-panen', 'card-sudah-panen', 'card-gagal-panen'];
      ids.forEach(id => {
          // User requested to show 0 instead of a loading spinner
          if(document.getElementById(id)) document.getElementById(id).innerHTML = '0';
      });
    
    fetch('/hydroponics/dashboard/summary-cards?month=' + month + '&year=' + year)
        .then(res => res.json())
        .then(data => {
            if(document.getElementById('card-lubang-kosong')) {
                let elKosong = document.getElementById('card-lubang-kosong'); if(elKosong) elKosong.textContent = data.lubang_kosong;
                let elTerisi = document.getElementById('card-lubang-terisi'); if(elTerisi) elTerisi.textContent = data.lubang_terisi;
                let elTerisiSub = document.getElementById('card-lubang-terisi-sub'); if(elTerisiSub) elTerisiSub.textContent = data.lubang_terisi_sub;
                let elSiap = document.getElementById('card-siap-panen'); if(elSiap) elSiap.textContent = data.siap_panen;
                let elSiapSub = document.getElementById('card-siap-panen-sub'); if(elSiapSub) elSiapSub.textContent = data.siap_panen_sub;
                let elSudah = document.getElementById('card-sudah-panen'); if(elSudah) elSudah.textContent = data.sudah_panen;
                let elSudahSub = document.getElementById('card-sudah-panen-sub'); if(elSudahSub) elSudahSub.textContent = data.sudah_panen_sub;
                let elGagal = document.getElementById('card-gagal-panen'); if(elGagal) elGagal.textContent = data.gagal_panen;
                let elGagalSub = document.getElementById('card-gagal-panen-sub'); if(elGagalSub) elGagalSub.textContent = data.gagal_panen_sub;
            }
            if(document.getElementById('val-total-tanam')) {
                document.getElementById('val-total-tanam').textContent = data.total_tanam_bulan_ini + ' Lubang';
            }
            if(document.getElementById('val-total-panen')) {
                document.getElementById('val-total-panen').textContent = data.total_panen_bulan_ini + ' Lubang';
            }
            if(document.getElementById('val-total-semai')) {
                document.getElementById('val-total-semai').textContent = data.total_semai_bulan_ini + ' Benih';
            }
        });
}

document.addEventListener('DOMContentLoaded', function() {
    if (typeof initCalendar === 'function') initCalendar();
    if (typeof initCharts === 'function') initCharts();
});

// ══════════════════════════════════════════════════════════════
//  PERIOD FILTER (Tahun Ini / Bulan Ini / Minggu Ini / Hari Ini)
// ══════════════════════════════════════════════════════════════
function switchPeriod(period, btn) {
    // Update tab active state
    document.querySelectorAll('.period-tab').forEach(t => {
        t.style.background = 'transparent';
        t.style.color = 'var(--text-muted)';
        t.classList.remove('period-tab-active');
    });
    btn.style.background = 'var(--asr-green)';
    btn.style.color = 'white';
    btn.classList.add('period-tab-active');

    // Show loading indicator on stat cards
    const loadingCards = ['val-total-semai', 'val-total-tanam', 'card-sudah-panen', 'card-gagal-panen'];
    loadingCards.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.opacity = '0.5';
    });

    // Fetch period stats via AJAX
    fetch(`/hydroponics/dashboard/period-stats?period=${period}`)
        .then(r => r.json())
        .then(data => {
            // Update title
            const titleEl = document.getElementById('summaryGlobalTitle');
            if (titleEl) titleEl.textContent = 'Ringkasan: ' + data.period_label;

            // Update stat card values
            const elSemai = document.getElementById('val-total-semai');
            if (elSemai) {
                elSemai.textContent = Number(data.total_semai_benih).toLocaleString('id-ID') + ' Benih';
                const sub = document.getElementById('val-total-semai-sub');
                if (sub) sub.textContent = data.total_semai_jenis + ' Jenis Tanaman';
            }

            const elTanam = document.getElementById('val-total-tanam');
            if (elTanam) elTanam.textContent = Number(data.total_tanam).toLocaleString('id-ID') + ' Lubang';

            const elPanen = document.getElementById('card-sudah-panen');
            if (elPanen) elPanen.textContent = Number(data.total_panen).toLocaleString('id-ID');

            const elRusak = document.getElementById('card-gagal-panen');
            if (elRusak) elRusak.textContent = Number(data.total_rusak).toLocaleString('id-ID');

            // Restore opacity
            loadingCards.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.opacity = '1';
            });
        })
        .catch(err => {
            console.error('Period filter error:', err);
            loadingCards.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.opacity = '1';
            });
        });
}
</script>
</div>
@endsection
