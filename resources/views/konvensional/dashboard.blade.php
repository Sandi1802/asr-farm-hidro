@extends('layouts.app')
@section('title', 'Dashboard Konvensional ASR FARM')
@section('content')

<style>
.chart-card { background: var(--card-bg, white); border-radius: 14px; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 1.25rem 1.5rem; min-width: 0; }
.chart-card-title { font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main); }


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
            <i class="ph ph-squares-four" style="color:var(--asr-green);"></i> <span id="summaryKonvTitle">Ringkasan Global Konvensional</span>
        </h2>
        <div style="display: flex; gap: 0.25rem; background: var(--bg-main); border-radius: 10px; padding: 4px; border: 1px solid var(--border-color);">
            <button onclick="switchPeriodKonv('year', this)" class="period-tab-k" style="border:none; background:transparent; padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:var(--text-muted); cursor:pointer; transition:all 0.2s; font-family:inherit;">Tahun Ini</button>
            <button onclick="switchPeriodKonv('month', this)" class="period-tab-k period-tab-k-active" style="border:none; background:var(--asr-green); padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:white; cursor:pointer; transition:all 0.2s; font-family:inherit;">Bulan Ini</button>
            <button onclick="switchPeriodKonv('week', this)" class="period-tab-k" style="border:none; background:transparent; padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:var(--text-muted); cursor:pointer; transition:all 0.2s; font-family:inherit;">Minggu Ini</button>
            <button onclick="switchPeriodKonv('today', this)" class="period-tab-k" style="border:none; background:transparent; padding:0.4rem 0.85rem; border-radius:7px; font-size:0.78rem; font-weight:700; color:var(--text-muted); cursor:pointer; transition:all 0.2s; font-family:inherit;">Hari Ini</button>
        </div>
    </div>
    <div class="dashboard-stats">
        @php
        $combinedStats = [
            // Baris 1: Kapasitas & Aset
            ['label' => 'Total Lahan', 'value' => $totalLahan . ' Lahan', 'icon' => 'ph-mountains', 'class' => 'sbc-dark-green', 'link' => '/konvensional/lahan'],
            ['label' => 'Total Bedengan', 'value' => $totalBedengan . ' Bedengan', 'icon' => 'ph-rows', 'class' => 'sbc-mid-green'],
            ['label' => 'Total Titik Tanam', 'value' => number_format($totalTitik,0,',','.'), 'icon' => 'ph-circle-dashed', 'class' => 'sbc-slate-farm', 'sub' => 'Kapasitas maksimal'],
            ['label' => 'Titik Kosong', 'value' => number_format($titikKosong,0,',','.'), 'icon' => 'ph-warning-circle', 'class' => 'sbc-rust', 'sub' => 'Belum ditanami'],
            
            // Baris 2: Status Produksi
            ['label' => 'Titik Terisi', 'value' => number_format($titikTerisi,0,',','.'), 'icon' => 'ph-plant', 'class' => 'sbc-teal-farm', 'sub' => 'Sedang ditanam'],
            ['label' => 'Total Jenis Bibit', 'value' => $totalJenisBibit . ' Jenis', 'icon' => 'ph-seedling', 'class' => 'sbc-olive', 'link' => '/konvensional/bibit'],
            ['label' => 'Siap Panen', 'value' => number_format($siapPanen,0,',','.'), 'icon' => 'ph-trophy', 'class' => 'sbc-gold', 'sub' => 'Sudah masuk masa panen'],
            ['id' => 'kv-panen', 'label' => 'Panen Bulan Ini', 'value' => number_format($panenBulanIni,0,',','.'), 'icon' => 'ph-basket', 'class' => 'sbc-gold'],
            
            // Baris 3: Perawatan & Kendala
            ['label' => 'Rata-rata Usia Panen', 'value' => round($rataPanenBibit) . ' Hari', 'icon' => 'ph-clock-countdown', 'class' => 'sbc-teal-farm'],
            ['id' => 'kv-gagal', 'label' => 'Gagal Panen', 'value' => number_format($gagalPanen,0,',','.'), 'icon' => 'ph-warning', 'class' => 'sbc-earth'],
            ['id' => 'kv-pemupukan', 'label' => 'Pemupukan Bulan Ini', 'value' => $pemupukanBulanIni . ' Kali', 'icon' => 'ph-flask','class' => 'sbc-mid-green', 'link' => '/konvensional/pemupukan'],
            ['id' => 'kv-penyemprotan', 'label' => 'Penyemprotan Bulan Ini', 'value' => $penyemprotanBulanIni . ' Kali', 'icon' => 'ph-drop', 'class' => 'sbc-slate-farm', 'link' => '/konvensional/penyemprotan'],
        ];
        @endphp

        @foreach($combinedStats as $s)
        <a @if(isset($s['link'])) href="{{ $s['link'] }}" @endif style="text-decoration:none;">
            <div class="stat-big-card {{ $s['class'] }}">
                <div>
                    <div class="sbc-value" {!! isset($s['id']) ? 'id="'.$s['id'].'"' : '' !!}>{{ $s['value'] }}</div>
                    <div class="sbc-label">{{ $s['label'] }}</div>
                    @if(isset($s['sub']))
                    <div style="font-size:0.75rem; color: rgba(255,255,255,0.85); font-weight:600; margin-top:0.35rem; letter-spacing:0.3px;" {!! isset($s['id']) ? 'id="'.$s['id'].'-sub"' : '' !!}>{{ $s['sub'] }}</div>
                    @endif
                </div>
                @if(isset($s['link']))
                <div class="sbc-link">Selengkapnya <i class="ph ph-arrow-right"></i></div>
                @endif
                <i class="ph {{ $s['icon'] }} sbc-icon"></i>
            </div>
        </a>
        @endforeach
    </div>

    {{-- CALENDAR + DAILY SCHEDULE (2-col) AT TOP --}}
<div class="responsive-grid-cal">

        {{-- HARVEST CALENDAR --}}
        <div class="card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
            <div style="padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <h2 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph ph-calendar-check" style="color: var(--asr-green);"></i> Kalender Pertumbuhan
                </h2>
                <div style="display: flex; align-items: center; gap: 0.5rem; position: relative;">
                    
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



    {{-- GRAFIK SECTION (2 KOLOM) --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
        
        {{-- Grafik Keterisian Lahan --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('keterisianChart', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('keterisianChart', 'print')">Cetak grafik</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('keterisianChart', 'png')">Unduh gambar PNG</a>
                    <a class="chart-export-item" onclick="exportChart('keterisianChart', 'jpeg')">Unduh gambar JPEG</a>
                </div>
            </div>
            <h3 class="chart-card-title" style="padding-right: 30px;"><i class="ph ph-chart-pie-slice" style="color:var(--asr-green);font-size:1.2rem;"></i> Distribusi Keterisian per Lahan</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="keterisianChart"></canvas>
            </div>
        </div>

        {{-- Grafik Tren Perawatan --}}
        <div class="chart-card" style="position: relative;">
            <div class="chart-export-wrapper">
                <button class="chart-export-btn" onclick="toggleChartMenu(this)"><i class="ph ph-list"></i></button>
                <div class="chart-export-dropdown">
                    <a class="chart-export-item" onclick="exportChart('perawatanChart', 'fullscreen')">Lihat layar penuh</a>
                    <a class="chart-export-item" onclick="exportChart('perawatanChart', 'print')">Cetak grafik</a>
                    <div class="chart-export-separator"></div>
                    <a class="chart-export-item" onclick="exportChart('perawatanChart', 'png')">Unduh gambar PNG</a>
                    <a class="chart-export-item" onclick="exportChart('perawatanChart', 'jpeg')">Unduh gambar JPEG</a>
                </div>
            </div>
            <h3 class="chart-card-title" style="padding-right: 30px;"><i class="ph ph-chart-line-up" style="color:var(--asr-green);font-size:1.2rem;"></i> Tren Perawatan (4 Minggu Terakhir)</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="perawatanChart"></canvas>
            </div>
        </div>

    </div>

</div>

<!-- Memasukkan library Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js">
    if (typeof initCalendar === 'function') initCalendar();
// ══════════════════════════════════════════════════════════════
//  DATA FROM BLADE
// ══════════════════════════════════════════════════════════════
const calendarData  = {!! $calendarJson !!};



let currentYear, currentMonth;
const monthsStr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

window.calFilters = {
    semai: true, tanam: true, remaja: true, harvest: true, custom: false
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
        

    if (typeof initCalendar === 'function') initCalendar();
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Inisialisasi Grafik Keterisian Lahan (Bar Chart bertumpuk)
    const ctxKeterisian = document.getElementById('keterisianChart').getContext('2d');
    
    // Parse data dari PHP
    const keterisianData = @json($chartKeterisian);
    
    new Chart(ctxKeterisian, {
        type: 'bar',
        data: {
            labels: keterisianData.labels,
            datasets: [
                {
                    label: 'Terisi (Ditanam)',
                    data: keterisianData.terisi,
                    backgroundColor: '#16a34a', // ASR Green
                    borderRadius: 4
                },
                {
                    label: 'Kosong',
                    data: keterisianData.kosong,
                    backgroundColor: '#cbd5e1', // Slate 300
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    border: { display: false }
                }
            },
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            }
        }
    });

    // 2. Inisialisasi Grafik Tren Perawatan (Line Chart)
    const ctxPerawatan = document.getElementById('perawatanChart').getContext('2d');
    
    // Parse data dari PHP
    const perawatanData = @json($chartPerawatan);
    
    new Chart(ctxPerawatan, {
        type: 'line',
        data: {
            labels: perawatanData.labels,
            datasets: [
                {
                    label: 'Pemupukan',
                    data: perawatanData.pemupukan,
                    borderColor: '#0284c7', // Sky 600
                    backgroundColor: 'rgba(2, 132, 199, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#0284c7'
                },
                {
                    label: 'Penyemprotan',
                    data: perawatanData.penyemprotan,
                    borderColor: '#f59e0b', // Amber 500
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#f59e0b'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    border: { display: false },
                    ticks: { precision: 0 } // Angka bulat untuk frekuensi
                }
            },
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            }
        }
    });
});

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
    }
}

// ══════════════════════════════════════════════════════════════
//  PERIOD FILTER (Tahun Ini / Bulan Ini / Minggu Ini / Hari Ini)
// ══════════════════════════════════════════════════════════════
function switchPeriodKonv(period, btn) {
    // Update tab active state
    document.querySelectorAll('.period-tab-k').forEach(t => {
        t.style.background = 'transparent';
        t.style.color = 'var(--text-muted)';
        t.classList.remove('period-tab-k-active');
    });
    btn.style.background = 'var(--asr-green)';
    btn.style.color = 'white';
    btn.classList.add('period-tab-k-active');

    // Show loading indicator on stat cards
    const loadingCards = ['kv-panen', 'kv-gagal', 'kv-pemupukan', 'kv-penyemprotan'];
    loadingCards.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.closest('.stat-big-card').style.opacity = '0.5';
    });

    // Fetch period stats via AJAX
    fetch(`/konvensional/dashboard/period-stats?period=${period}`)
        .then(r => r.json())
        .then(data => {
            // Update title
            const titleEl = document.getElementById('summaryKonvTitle');
            if (titleEl) titleEl.textContent = 'Ringkasan: ' + data.period_label;

            // Update stat card values
            const elPanen = document.getElementById('kv-panen');
            if (elPanen) elPanen.textContent = Number(data.panen).toLocaleString('id-ID');

            const elGagal = document.getElementById('kv-gagal');
            if (elGagal) elGagal.textContent = Number(data.gagal).toLocaleString('id-ID');

            const elPupuk = document.getElementById('kv-pemupukan');
            if (elPupuk) elPupuk.textContent = Number(data.pemupukan).toLocaleString('id-ID') + ' Kali';

            const elSemprot = document.getElementById('kv-penyemprotan');
            if (elSemprot) elSemprot.textContent = Number(data.penyemprotan).toLocaleString('id-ID') + ' Kali';

            // Restore opacity
            loadingCards.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.closest('.stat-big-card').style.opacity = '1';
            });
        })
        .catch(err => {
            console.error('Period filter error:', err);
            loadingCards.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.closest('.stat-big-card').style.opacity = '1';
            });
        });
}

    if (typeof initCalendar === 'function') initCalendar();
// ══════════════════════════════════════════════════════════════
//  DATA FROM BLADE
// ══════════════════════════════════════════════════════════════
const calendarData  = {!! $calendarJson !!};



let currentYear, currentMonth;
const monthsStr = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

window.calFilters = {
    semai: true, tanam: true, remaja: true, harvest: true, custom: false
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
        

    if (typeof initCalendar === 'function') initCalendar();
</script>

@endsection
