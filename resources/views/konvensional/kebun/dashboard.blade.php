@extends('layouts.app')
@section('title', 'Dashboard Kebun Konvensional')

@section('content')
<style>
    .dash-grid { display: grid; gap: 1.5rem; grid-template-columns: repeat(12, 1fr); }
    .col-3 { grid-column: span 3; }
    .col-4 { grid-column: span 4; }
    .col-6 { grid-column: span 6; }
    .col-8 { grid-column: span 8; }
    .col-12 { grid-column: span 12; }
    @media (max-width: 1024px) {
        .col-3 { grid-column: span 6; }
    }
    @media (max-width: 768px) {
        .col-3, .col-4, .col-6, .col-8 { grid-column: span 12; }
    }

    .bedeng-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
        gap: 0.5rem;
    }
    .bedeng-cell {
        border: 2px solid transparent;
        border-radius: 6px;
        padding: 0.5rem;
        text-align: center;
        cursor: pointer;
        transition: transform 0.1s, box-shadow 0.1s;
        min-height: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        touch-action: manipulation;
    }
    .bedeng-cell:active {
        transform: scale(0.95);
    }
    .bedeng-cell:hover {
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .bedeng-kode {
        font-weight: 700;
        font-size: 0.85rem;
    }
    .bedeng-tanaman {
        font-size: 0.7rem;
        margin-top: 0.25rem;
        line-height: 1.1;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s;
    }
    .modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .modal-content {
        background: var(--card-bg, #fff);
        width: 90%;
        max-width: 400px;
        border-radius: 12px;
        padding: 1.5rem;
        transform: translateY(50px);
        transition: transform 0.3s;
    }
    .modal-overlay.active .modal-content {
        transform: translateY(0);
    }
    @media (max-width: 768px) {
        .modal-overlay {
            align-items: flex-end;
        }
        .modal-content {
            width: 100%;
            max-width: 100%;
            border-radius: 20px 20px 0 0;
            transform: translateY(100%);
            padding-bottom: env(safe-area-inset-bottom, 1.5rem);
        }
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.5rem;
    }

    .h-100 { height: 100%; }
</style>

<!-- Hero Header -->
<div class="project-hero">
    <div>
        <h1 style="margin:0; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem;"><i class="ph ph-plant"></i> Kebun Konvensional</h1>
        <p style="margin: 0.5rem 0 0; opacity: 0.9;">Total {{ $totalBedeng }} Bedengan Terkelola</p>
    </div>
</div>

<!-- Summary Cards -->
<div class="dash-grid" style="margin-bottom: 1.5rem;">
    <!-- Bedeng Terpakai / Total -->
    <div class="card col-3">
        <div class="flex-start">
            <div class="icon-box green"><i class="ph ph-rows"></i></div>
            <div style="flex: 1;">
                <div class="stat-label">Bedeng Terpakai</div>
                <div class="stat-value" style="font-size: 1.25rem;">{{ $terpakai }} / {{ $totalBedeng }}</div>
                <div style="background: var(--border-color); height: 6px; border-radius: 3px; margin-top: 4px; overflow: hidden; width: 100%;">
                    <div style="background: var(--asr-green); height: 100%; width: {{ $pctPemanfaatan }}%;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Siap Panen -->
    <div class="card col-3">
        <div class="flex-start">
            <div class="icon-box" style="background-color: rgba(249,115,22,0.15); color: #f97316;"><i class="ph ph-basket"></i></div>
            <div>
                <div class="stat-label">Siap Panen</div>
                <div class="stat-value" style="font-size: 1.25rem;">{{ $siapPanen }} Bedeng</div>
            </div>
        </div>
    </div>

    <!-- Mendekati Panen -->
    <div class="card col-3">
        <div class="flex-start">
            <div class="icon-box yellow"><i class="ph ph-clock"></i></div>
            <div>
                <div class="stat-label">Mendekati Panen</div>
                <div class="stat-value" style="font-size: 1.25rem;">{{ $mendekatiPanen }} Bedeng</div>
            </div>
        </div>
    </div>

    <!-- Terlambat Panen -->
    @if($terlambatPanen > 0)
    <div class="card col-3">
        <div class="flex-start">
            <div class="icon-box red" style="background-color: rgba(239,68,68,0.15); color: #ef4444;"><i class="ph ph-warning-circle"></i></div>
            <div>
                <div class="stat-label" style="color: #ef4444;">Terlambat Panen</div>
                <div class="stat-value" style="font-size: 1.25rem; color: #ef4444;">{{ $terlambatPanen }} Bedeng</div>
            </div>
        </div>
    </div>
    @else
    <div class="card col-3">
        <div class="flex-start">
            <div class="icon-box blue"><i class="ph ph-check-circle"></i></div>
            <div>
                <div class="stat-label">Terlambat Panen</div>
                <div class="stat-value" style="font-size: 1.25rem;">0 Bedeng</div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Estimasi vs Realisasi -->
<div class="card" style="margin-bottom: 1.5rem;">
    <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1rem; color: var(--text-main);"><i class="ph ph-chart-line-up" style="color: var(--asr-green);"></i> Produksi Bulan Ini (Kg)</h3>
    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; font-weight: 600; color: var(--text-main);">
        <span>Estimasi: {{ number_format($estimasiBulanIni, 2, ',', '.') }}</span>
        <span>Realisasi: {{ number_format($realisasiBulanIni, 2, ',', '.') }}</span>
    </div>
    @php
        $maxProd = max($estimasiBulanIni, $realisasiBulanIni, 1);
        $pctEst = ($estimasiBulanIni / $maxProd) * 100;
        $pctReal = ($realisasiBulanIni / $maxProd) * 100;
    @endphp
    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 70px; font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Estimasi</div>
            <div style="flex: 1; background: var(--border-color); height: 16px; border-radius: 8px; overflow: hidden;">
                <div style="background: #3b82f6; height: 100%; width: {{ $pctEst }}%; transition: width 0.5s;"></div>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 70px; font-size: 0.85rem; color: var(--text-muted); font-weight: 500;">Realisasi</div>
            <div style="flex: 1; background: var(--border-color); height: 16px; border-radius: 8px; overflow: hidden;">
                <div style="background: var(--asr-green); height: 100%; width: {{ $pctReal }}%; transition: width 0.5s;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Peta Bedeng -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <h3 style="font-size: 1.1rem; margin: 0; color: var(--text-main);"><i class="ph ph-map-trifold" style="color: var(--asr-green);"></i> Peta Bedeng</h3>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; width: 100%; max-width: 400px;">
            <select id="filterStatus" class="form-control" style="flex: 1; min-width: 120px; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-main);">
                <option value="semua">Semua Status</option>
                <option value="kosong">Kosong</option>
                <option value="tumbuh">Tumbuh</option>
                <option value="mendekati_panen">Mendekati Panen</option>
                <option value="siap_panen">Siap Panen</option>
                <option value="terlambat">Terlambat</option>
            </select>
            <input type="text" id="filterTanaman" placeholder="Cari tanaman..." class="form-control" style="flex: 1; min-width: 120px; padding: 0.5rem; border-radius: 6px; border: 1px solid var(--border-color); background: var(--bg-color); color: var(--text-main);">
        </div>
    </div>

    @forelse($bedengMap as $lahan)
        <div class="lahan-container" style="margin-bottom: 2rem; border: 1px solid var(--border-color); border-radius: 8px; padding: 1.25rem;">
            <h4 style="font-size: 1.1rem; margin: 0 0 1.25rem 0; color: var(--asr-green); border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
                <i class="ph ph-mountains"></i> Lahan: {{ $lahan->nama }}
            </h4>
            
            @forelse($lahan->zona as $zona)
                <div class="zona-container" style="margin-bottom: 1.5rem; margin-left: 0.5rem;">
                    <h5 style="font-size: 0.95rem; margin: 0 0 0.75rem 0; color: var(--text-main);">
                        <i class="ph ph-bounding-box" style="color: var(--text-muted);"></i> Zona: {{ $zona->nama }}
                    </h5>
                    
                    @forelse($zona->pola as $pola)
                        <div class="pola-container" style="margin-bottom: 1rem; background: var(--bg-color); padding: 1rem; border-radius: 8px;">
                            <div style="font-size: 0.85rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-muted);">
                                Pola: {{ $pola->nama }}
                            </div>
                            
                            <div class="bedeng-grid">
                                @forelse($pola->bedeng as $bedeng)
                                    @php
                                        $status = $bedeng->tanamAktif ? $bedeng->tanamAktif->status_tampilan : 'kosong';
                                        
                                        $bg = '#e2e8f0'; $border = '#cbd5e1'; $color = '#475569';
                                        if($status == 'tumbuh') { $bg = '#dcfce7'; $border = '#16a34a'; $color = '#166534'; }
                                        elseif($status == 'mendekati_panen') { $bg = '#fef9c3'; $border = '#eab308'; $color = '#854d0e'; }
                                        elseif($status == 'siap_panen') { $bg = '#ffedd5'; $border = '#f97316'; $color = '#9a3412'; }
                                        elseif($status == 'terlambat') { $bg = '#fee2e2'; $border = '#ef4444'; $color = '#991b1b'; }
                                        
                                        $tanamanNama = $bedeng->tanamAktif ? $bedeng->tanamAktif->tanaman->nama : 'Kosong';
                                    @endphp
                                    <div class="bedeng-cell" 
                                         data-id="{{ $bedeng->id }}"
                                         data-status="{{ $status }}"
                                         data-tanaman="{{ strtolower($tanamanNama) }}"
                                         data-detail="{{ json_encode([
                                             'kode' => $bedeng->kode,
                                             'luas' => $bedeng->luas_m2,
                                             'status' => $status,
                                             'tanaman' => $tanamanNama,
                                             'tgl_tanam' => $bedeng->tanamAktif ? \Carbon\Carbon::parse($bedeng->tanamAktif->tanggal_tanam)->format('d M Y') : '-',
                                             'estimasi' => $bedeng->tanamAktif ? \Carbon\Carbon::parse($bedeng->tanamAktif->estimasi_tanggal_panen)->format('d M Y') : '-',
                                             'umur' => $bedeng->tanamAktif ? $bedeng->tanamAktif->umur_hari : 0,
                                             'sisa_hari' => $bedeng->tanamAktif ? $bedeng->tanamAktif->sisa_hari : 0,
                                             'jumlah' => $bedeng->tanamAktif ? $bedeng->tanamAktif->jumlah_tanam : 0,
                                         ]) }}"
                                         style="background: {{ $bg }}; border-color: {{ $border }}; color: {{ $color }};"
                                         onclick="showBedengDetail(this)">
                                        <div class="bedeng-kode">{{ $bedeng->kode }}</div>
                                        <div class="bedeng-tanaman" title="{{ $tanamanNama }}">{{ $bedeng->tanamAktif ? $tanamanNama : '' }}</div>
                                    </div>
                                @empty
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">Tidak ada bedeng.</div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">Tidak ada pola.</div>
                    @endforelse
                </div>
            @empty
                <div style="font-size: 0.85rem; color: var(--text-muted);">Tidak ada zona.</div>
            @endforelse
        </div>
    @empty
        <div style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data lahan dan bedeng.</div>
    @endforelse
</div>

<!-- Modal Bedeng -->
<div class="modal-overlay" id="bedengModal" onclick="closeBedengDetail(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h3 id="modalKode" style="margin: 0; color: var(--text-main);">Bedeng</h3>
            <button onclick="closeBedengDetail()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color: var(--text-muted);">&times;</button>
        </div>
        <div class="modal-body">
            <div id="modalContent"></div>
            <div id="modalActions" style="margin-top: 1.5rem; display: flex; gap: 0.5rem; flex-wrap: wrap;"></div>
        </div>
    </div>
</div>

<div class="dash-grid">
    <!-- Agenda Panen -->
    <div class="col-6">
        <div class="card h-100">
            <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1rem; color: var(--text-main);"><i class="ph ph-calendar-check" style="color: var(--warning);"></i> Agenda Panen (14 Hari Kedepan)</h3>
            @if(count($agendaPanen) > 0)
                <ul class="issue-list" style="padding: 0; margin: 0; list-style: none;">
                @foreach($agendaPanen as $tanam)
                    @php
                        $color = $tanam->sisa_hari < 0 ? '#ef4444' : ($tanam->sisa_hari <= 3 ? '#f97316' : 'var(--text-main)');
                        $bgBadge = $tanam->sisa_hari < 0 ? '#fee2e2' : ($tanam->sisa_hari <= 3 ? '#ffedd5' : 'var(--bg-color)');
                    @endphp
                    <li style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px dashed var(--border-color);">
                        <div>
                            <div style="font-weight: 600; color: var(--text-main);">{{ $tanam->bedeng->kode }} - {{ $tanam->tanaman->nama }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">Est: {{ \Carbon\Carbon::parse($tanam->estimasi_tanggal_panen)->format('d M Y') }}</div>
                        </div>
                        <div class="badge" style="background: {{ $bgBadge }}; color: {{ $color }}; padding: 0.4rem 0.6rem; border-radius: 4px; font-size: 0.75rem;">
                            {{ $tanam->sisa_hari < 0 ? 'Terlambat ' . abs($tanam->sisa_hari) . ' hari' : $tanam->sisa_hari . ' hari lagi' }}
                        </div>
                    </li>
                @endforeach
                </ul>
            @else
                <p style="color: var(--text-muted); text-align: center; padding: 2rem 0; margin: 0;">Tidak ada jadwal panen dalam 14 hari ke depan.</p>
            @endif
        </div>
    </div>

    <!-- Ringkasan Tanaman Aktif -->
    <div class="col-6">
        <div class="card h-100">
            <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1rem; color: var(--text-main);"><i class="ph ph-plant" style="color: var(--asr-green);"></i> Ringkasan Tanaman Aktif</h3>
            <div class="table-container" style="overflow-x: auto;">
                <table style="width: 100%; text-align: left; border-collapse: collapse; min-width: 300px;">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--border-color);">
                            <th style="padding: 0.75rem 0.5rem; background: transparent; color: var(--text-muted) !important; font-weight: 600;">Tanaman</th>
                            <th style="padding: 0.75rem 0.5rem; text-align: center; background: transparent; color: var(--text-muted) !important; font-weight: 600;">Bedeng</th>
                            <th style="padding: 0.75rem 0.5rem; text-align: right; background: transparent; color: var(--text-muted) !important; font-weight: 600;">Populasi</th>
                            <th style="padding: 0.75rem 0.5rem; text-align: right; background: transparent; color: var(--text-muted) !important; font-weight: 600;">Est. Hasil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tanamanAktifSummary as $sum)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem 0.5rem; font-weight: 500; color: var(--text-main);">{{ $sum->tanaman_nama }}</td>
                            <td style="padding: 0.75rem 0.5rem; text-align: center; color: var(--text-main);">{{ $sum->bedeng_count }}</td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right; color: var(--text-main);">{{ number_format($sum->total_populasi, 0, ',', '.') }}</td>
                            <td style="padding: 0.75rem 0.5rem; text-align: right; color: var(--text-main);">{{ number_format($sum->total_estimasi, 2, ',', '.') }} {{ $sum->satuan }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">Belum ada tanaman aktif.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Chart Panen -->
    <div class="col-6">
        <div class="card h-100">
            <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1rem; color: var(--text-main);"><i class="ph ph-chart-bar" style="color: #3b82f6;"></i> Panen per Bulan (Tahun Ini)</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="chartPanen"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart Zona -->
    <div class="col-6">
        <div class="card h-100">
            <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1rem; color: var(--text-main);"><i class="ph ph-chart-pie-slice" style="color: #8b5cf6;"></i> Keterisian per Zona</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="chartZona"></canvas>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="col-12">
        <div class="card">
            <h3 style="font-size: 1.1rem; margin-top: 0; margin-bottom: 1rem; color: var(--text-main);"><i class="ph ph-clock-counter-clockwise" style="color: var(--text-muted);"></i> Aktivitas Terbaru</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse($aktivitasTerbaru as $akt)
                    <div style="display: flex; gap: 1rem; align-items: flex-start; padding: 1rem; background: var(--bg-color); border-radius: 8px; border: 1px solid var(--border-color);">
                        <div class="icon-box {{ $akt->jenis == 'tanam' ? 'green' : 'yellow' }}" style="width: 44px; height: 44px; flex-shrink: 0; border-radius: 50%;">
                            <i class="ph {{ $akt->jenis == 'tanam' ? 'ph-plant' : 'ph-basket' }}"></i>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--text-main); font-size: 0.95rem;">
                                {{ $akt->jenis == 'tanam' ? 'Tanam Baru' : 'Panen' }} - {{ $akt->tanaman }} <span style="color: var(--text-muted); font-weight: 400;">(Bedeng {{ $akt->bedeng }})</span>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.35rem; display: flex; align-items: center; gap: 0.5rem;">
                                <span><i class="ph ph-calendar"></i> {{ \Carbon\Carbon::parse($akt->tanggal)->format('d M Y H:i') }}</span>
                                <span>•</span>
                                <span style="font-weight: 500;">{{ $akt->jenis == 'tanam' ? number_format($akt->jumlah, 0, ',', '.') . ' bibit' : number_format($akt->hasil, 2, ',', '.') . ' ' . ($akt->satuan ?? 'kg') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="color: var(--text-muted); text-align: center; margin: 0; padding: 2rem 0;">Belum ada aktivitas tercatat.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart Panen
    const ctxPanen = document.getElementById('chartPanen');
    if (ctxPanen) {
        const dataPanen = {!! json_encode($chartPanenBulanan) !!};
        new Chart(ctxPanen.getContext('2d'), {
            type: 'bar',
            data: {
                labels: dataPanen.labels,
                datasets: [
                    {
                        label: 'Estimasi',
                        data: dataPanen.estimasi,
                        backgroundColor: '#93c5fd',
                        borderRadius: 4
                    },
                    {
                        label: 'Realisasi',
                        data: dataPanen.realisasi,
                        backgroundColor: '#16a34a',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Chart Zona
    const ctxZona = document.getElementById('chartZona');
    if (ctxZona) {
        const dataZona = {!! json_encode($chartPerZona) !!};
        new Chart(ctxZona.getContext('2d'), {
            type: 'bar',
            data: {
                labels: dataZona.labels,
                datasets: [
                    {
                        label: 'Terisi',
                        data: dataZona.terisi,
                        backgroundColor: '#16a34a',
                        borderRadius: 4
                    },
                    {
                        label: 'Kosong',
                        data: dataZona.kosong,
                        backgroundColor: '#cbd5e1',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Filter Logic
    const filterStatus = document.getElementById('filterStatus');
    const filterTanaman = document.getElementById('filterTanaman');

    function applyFilter() {
        const status = filterStatus.value;
        const tanam = filterTanaman.value.toLowerCase();
        
        document.querySelectorAll('.bedeng-cell').forEach(cell => {
            const cStatus = cell.dataset.status;
            const cTanam = cell.dataset.tanaman;
            
            let show = true;
            if (status !== 'semua' && cStatus !== status) show = false;
            if (tanam !== '' && !cTanam.includes(tanam)) show = false;
            
            cell.style.display = show ? 'flex' : 'none';
        });
    }

    if (filterStatus) filterStatus.addEventListener('change', applyFilter);
    if (filterTanaman) filterTanaman.addEventListener('input', applyFilter);
});

// Modal Logic
function showBedengDetail(el) {
    const data = JSON.parse(el.dataset.detail);
    document.getElementById('modalKode').innerText = 'Bedeng ' + data.kode;
    
    let html = `
        <div style="margin-bottom: 1.5rem; background: var(--bg-color); padding: 1rem; border-radius: 8px;">
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.25rem;">Tanaman Saat Ini</div>
            <div style="font-weight: 700; font-size: 1.25rem; color: var(--text-main);">${data.tanaman}</div>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1rem; color: var(--text-main);">
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Status</div>
                <div style="font-weight: 600;">
                    <span class="badge" style="background: var(--bg-color); padding: 0.25rem 0.5rem; border: 1px solid var(--border-color);">${data.status.replace('_', ' ').toUpperCase()}</span>
                </div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Luas Area</div>
                <div style="font-weight: 600;">${data.luas} m²</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Tanggal Tanam</div>
                <div style="font-weight: 600;">${data.tgl_tanam}</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Umur Tanaman</div>
                <div style="font-weight: 600;">${data.umur} Hari</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Est. Panen</div>
                <div style="font-weight: 600;">${data.estimasi}</div>
            </div>
            <div>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Sisa Waktu</div>
                <div style="font-weight: 600; color: ${data.sisa_hari < 0 ? '#ef4444' : 'inherit'};">${data.sisa_hari} Hari</div>
            </div>
            <div style="grid-column: span 2; border-top: 1px dashed var(--border-color); padding-top: 1rem;">
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Populasi Ditanam</div>
                <div style="font-weight: 600;">${new Intl.NumberFormat('id-ID').format(data.jumlah)} Tanaman</div>
            </div>
        </div>
    `;
    document.getElementById('modalContent').innerHTML = html;
    
    let actionsHtml = '';
    if(data.status === 'kosong') {
        actionsHtml = `<button class="btn btn-primary" onclick="window.location.href='/konvensional/tanam/create?bedeng_id=${el.dataset.id}'" style="width: 100%; justify-content: center; padding: 0.75rem;"><i class="ph ph-plant"></i> Tanam Baru</button>`;
    } else {
        actionsHtml = `
            <button class="btn btn-primary" onclick="window.location.href='/konvensional/panen/create?bedeng_id=${el.dataset.id}'" style="flex: 1; justify-content: center; padding: 0.75rem;"><i class="ph ph-basket"></i> Catat Panen</button>
            <button class="btn btn-outline" style="flex: 1; justify-content: center; border-color: #ef4444; color: #ef4444; padding: 0.75rem;" onclick="alert('Fitur Tandai Gagal (Segera Hadir)')"><i class="ph ph-warning-circle"></i> Gagal</button>
            <button class="btn btn-outline" onclick="window.location.href='/konvensional/tanam/riwayat?bedeng_id=${el.dataset.id}'" style="width: 100%; justify-content: center; margin-top: 0.5rem; padding: 0.75rem; background: var(--bg-color);"><i class="ph ph-clock-counter-clockwise"></i> Lihat Riwayat</button>
        `;
    }
    document.getElementById('modalActions').innerHTML = actionsHtml;
    
    document.getElementById('bedengModal').classList.add('active');
}

function closeBedengDetail(e) {
    if(e && e.target !== document.getElementById('bedengModal')) return;
    document.getElementById('bedengModal').classList.remove('active');
}
</script>
@endsection
