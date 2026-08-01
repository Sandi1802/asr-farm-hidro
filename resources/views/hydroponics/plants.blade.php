@extends('layouts.app')
@section('title', 'Master Data Jenis Tanaman')
@section('content')

<style>
    /* Stage pill badges */
    .stage-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.68rem;
        font-weight: 600;
        border: 1px solid transparent;
        margin-bottom: 1px;
        line-height: 1.3;
    }
    .stage-semai  { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }
    .stage-tanam  { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }
    .stage-remaja { background: #fefce8; color: #854d0e; border-color: #fef08a; }
    .stage-dewasa { background: #fef2f2; color: #991b1b; border-color: #fecaca; }
    .stage-pill i { font-size: 0.75rem; }

    /* Duration badge */
    .duration-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #f3f4f6;
        color: #374151;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        border: 1px solid #e5e7eb;
    }

    /* Form */
    .form-group { margin-bottom: 1.25rem; }
    .form-group label {
        display: block; font-size: 0.875rem; font-weight: 500;
        color: #374151; margin-bottom: 0.5rem;
    }
    .form-group label span { color: #dc2626; }
    .form-control {
        width: 100%; padding: 0.625rem 0.75rem;
        border: 1px solid #d1d5db; border-radius: 6px;
        font-size: 0.875rem; color: #111827;
        background: #ffffff; transition: all 0.2s;
        box-sizing: border-box;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .form-control:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1); }
    
    .btn-primary-form {
        width: 100%; padding: 0.75rem;
        background: #059669;
        color: white; border: none; border-radius: 6px; font-size: 0.95rem;
        font-weight: 600; cursor: pointer; display: flex; align-items: center;
        justify-content: center; gap: 0.5rem; transition: background 0.2s;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .btn-primary-form:hover { background: #047857; }

    /* Growth Stage Section */
    .stage-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
    }
    .stage-section-title {
        font-size: 0.875rem; font-weight: 600; color: #0f172a;
        display: flex; align-items: center; gap: 0.5rem;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .stage-row {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    .stage-row-header {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.875rem; font-weight: 600;
    }
    .stage-inputs {
        display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;
    }
    .stage-input-wrap { position: relative; }
    .stage-input-wrap label {
        display: block; font-size: 0.75rem; font-weight: 500; color: #475569;
        margin-bottom: 0.25rem;
    }
    .stage-input-wrap input {
        width: 100%; padding: 0.5rem 0.5rem 0.5rem 0.5rem;
        border: 1px solid #cbd5e1; border-radius: 4px; font-size: 0.875rem;
        box-sizing: border-box; transition: border-color 0.15s;
        background: white;
    }
    .stage-input-wrap input:focus { outline: none; border-color: #059669; box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1); }
    
    /* Timeline Preview */
    .timeline-preview {
        display: flex; align-items: stretch; border-radius: 6px;
        overflow: hidden; margin-top: 1rem; height: 16px; background: #e2e8f0;
    }
    .timeline-segment { display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
    .tl-semai  { background: #22c55e; }
    .tl-tanam  { background: #3b82f6; }
    .tl-remaja { background: #eab308; }
    .tl-dewasa { background: #ef4444; }

    /* Stat bar */
    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem; }
    @media (max-width: 768px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    .stat-card {
        background: #ffffff; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 1.25rem;
        display: flex; align-items: center; gap: 1rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex-shrink: 0;
    }
    .stat-label { font-size: 0.75rem; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.25rem; }
    .stat-value { font-size: 1.25rem; font-weight: 700; color: #111827; line-height: 1.2; }
</style>

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #111827;">Master Data Jenis Tanaman</h1>
        <p style="color:#6b7280;font-size:0.9rem;margin:0.25rem 0 0;">Kelola jenis bibit, tahapan tumbuh, beserta standar PPM & pH</p>
    </div>

    @if(session('success'))
    <div style="padding: 1rem 1.25rem; background: #dcfce7; color: #15803d; border-radius: 10px; border-left: 4px solid #16a34a; font-weight: 500;">
        <i class="ph ph-check-circle"></i> {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div style="padding: 1rem 1.25rem; background: #fef2f2; color: #b91c1c; border-radius: 10px; border-left: 4px solid #dc2626; font-weight: 500;">
        <i class="ph ph-warning-circle"></i> {{ $errors->first() }}
    </div>
    @endif

    {{-- Summary stats --}}
    @php
        $avgDays = $plantTypes->count() > 0 ? round($plantTypes->avg('growth_days')) : 0;
        $fastest = $plantTypes->sortBy('growth_days')->first();
        $withStages = $plantTypes->where('semai_days', '>', 0)->count();
    @endphp
    <div class="stats-row">
        <div class="stat-card">
            <div class="stat-icon" style="background:#f0fdf4; color:#059669;"><i class="ph ph-leaf"></i></div>
            <div><div class="stat-label">Total Jenis</div><div class="stat-value">{{ $plantTypes->count() }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#eff6ff; color:#2563eb;"><i class="ph ph-clock"></i></div>
            <div><div class="stat-label">Rata-rata Panen</div><div class="stat-value">{{ $avgDays }} <span style="font-size:0.875rem;font-weight:500;color:#6b7280;">hari</span></div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#fef3c7; color:#d97706;"><i class="ph ph-lightning"></i></div>
            <div><div class="stat-label">Tercepat</div><div class="stat-value" style="font-size:1.1rem;">{{ $fastest ? $fastest->name . ' (' . $fastest->growth_days . 'h)' : '—' }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#f3e8ff; color:#9333ea;"><i class="ph ph-chart-bar"></i></div>
            <div><div class="stat-label">Terjadwal</div><div class="stat-value">{{ $withStages }}</div></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 480px; gap: 1.5rem; align-items: start;">
        {{-- Table --}}
        <div class="card" style="padding: 0;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0;"><i class="ph ph-list-dashes"></i> Daftar Tanaman</h2>
                <span style="background:var(--bg-color); padding:0.25rem 0.75rem; border-radius:20px; font-size:0.75rem; font-weight:600; border:1px solid var(--border-color);">{{ $plantTypes->count() }} Data</span>
            </div>
            <div style="padding: 1.5rem;">
                <table class="table datatable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th style="width: 40px;">NO</th>
                            <th>TANAMAN</th>
                            <th>TAHAPAN (HARI, PPM, pH)</th>
                            <th>MASA TUMBUH</th>
                            <th style="width: 100px; text-align: right;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plantTypes as $i => $plant)
                        <tr>
                            <td style="color:#6b7280; text-align: center;">{{ $i + 1 }}</td>
                            <td>
                                <strong style="font-size:0.95rem;color:#111827;">{{ $plant->name }}</strong>
                                @if($plant->description)
                                <div style="font-size:0.75rem;color:#6b7280;margin-top:0.1rem;">{{ $plant->description }}</div>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;flex-direction:column;gap:2px;">
                                    @if($plant->semai_days > 0)
                                    <div class="stage-pill stage-semai">
                                        <i class="ph ph-seedling"></i> Semai {{ $plant->semai_days }}h · {{ $plant->semai_ppm ?? '-' }}ppm · {{ $plant->semai_ph ?? '-' }}
                                    </div>
                                    @endif
                                    @if($plant->tanam_days > 0)
                                    <div class="stage-pill stage-tanam">
                                        <i class="ph ph-plant"></i> Tanam {{ $plant->tanam_days }}h · {{ $plant->tanam_ppm ?? '-' }}ppm · {{ $plant->tanam_ph ?? '-' }}
                                    </div>
                                    @endif
                                    @if($plant->remaja_days > 0)
                                    <div class="stage-pill stage-remaja">
                                        <i class="ph ph-tree"></i> Remaja {{ $plant->remaja_days }}h · {{ $plant->remaja_ppm ?? '-' }}ppm · {{ $plant->remaja_ph ?? '-' }}
                                    </div>
                                    @endif
                                    @if($plant->dewasa_days > 0)
                                    <div class="stage-pill stage-dewasa">
                                        <i class="ph ph-basket"></i> Panen {{ $plant->dewasa_days }}h · {{ $plant->dewasa_ppm ?? '-' }}ppm · {{ $plant->dewasa_ph ?? '-' }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="duration-badge">
                                    <i class="ph ph-calendar-blank"></i> {{ $plant->growth_days }} Hari
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 4px; justify-content: flex-end;">
                                    <button type="button" class="dt-action-btn dt-btn-edit" title="Edit" onclick="openEditModal(
                                        {{ $plant->id }}, '{{ addslashes($plant->name) }}',
                                        {{ $plant->semai_days ?? 0 }}, {{ $plant->semai_ppm ?? 'null' }}, {{ $plant->semai_ph ?? 'null' }},
                                        {{ $plant->tanam_days ?? 0 }}, {{ $plant->tanam_ppm ?? 'null' }}, {{ $plant->tanam_ph ?? 'null' }},
                                        {{ $plant->remaja_days ?? 0 }}, {{ $plant->remaja_ppm ?? 'null' }}, {{ $plant->remaja_ph ?? 'null' }},
                                        {{ $plant->dewasa_days ?? 0 }}, {{ $plant->dewasa_ppm ?? 'null' }}, {{ $plant->dewasa_ph ?? 'null' }},
                                        '{{ addslashes($plant->description ?? '') }}'
                                    )">
                                        <i class="ph ph-pencil-simple"></i>
                                    </button>
                                    <form method="POST" action="/hydroponics/master-data/plants/{{ $plant->id }}" style="display:inline; margin:0;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tanaman {{ addslashes($plant->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dt-action-btn dt-btn-delete" title="Hapus"><i class="ph ph-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Add form --}}
        <div class="card" style="position:sticky; top:90px;">
            <div style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0; display:flex; align-items:center; gap:0.5rem;"><i class="ph ph-plus-circle"></i> Tambah Tanaman</h2>
            </div>
            <form method="POST" action="/hydroponics/master-data/plants" id="addPlantForm">
                @csrf
                <div class="form-group">
                    <label>Nama Tanaman <span>*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Pakcoy, Selada..." value="{{ old('name') }}" required>
                </div>

                {{-- Tahapan Pertumbuhan & Standar --}}
                <div class="stage-section">
                    <div class="stage-section-title">
                        <i class="ph ph-sliders"></i> Tahapan & Standar (PPM, pH)
                    </div>
                    
                    <div class="stage-row">
                        <div class="stage-row-header" style="color: #166534;"><i class="ph ph-seedling"></i> Penyemaian</div>
                        <div class="stage-inputs">
                            <div class="stage-input-wrap"><label>Durasi(hr)*</label><input type="number" name="semai_days" id="semai_days" min="1" max="365" value="{{ old('semai_days', 5) }}" required></div>
                            <div class="stage-input-wrap"><label>PPM</label><input type="number" name="semai_ppm" min="0" value="{{ old('semai_ppm') }}"></div>
                            <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="semai_ph" min="0" max="14" value="{{ old('semai_ph') }}"></div>
                        </div>
                    </div>
                    
                    <div class="stage-row">
                        <div class="stage-row-header" style="color: #1e40af;"><i class="ph ph-plant"></i> Penanaman</div>
                        <div class="stage-inputs">
                            <div class="stage-input-wrap"><label>Durasi(hr)*</label><input type="number" name="tanam_days" id="tanam_days" min="1" max="365" value="{{ old('tanam_days', 10) }}" required></div>
                            <div class="stage-input-wrap"><label>PPM</label><input type="number" name="tanam_ppm" min="0" value="{{ old('tanam_ppm') }}"></div>
                            <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="tanam_ph" min="0" max="14" value="{{ old('tanam_ph') }}"></div>
                        </div>
                    </div>
                    
                    <div class="stage-row">
                        <div class="stage-row-header" style="color: #854d0e;"><i class="ph ph-tree"></i> Remaja</div>
                        <div class="stage-inputs">
                            <div class="stage-input-wrap"><label>Durasi(hr)*</label><input type="number" name="remaja_days" id="remaja_days" min="1" max="365" value="{{ old('remaja_days', 10) }}" required></div>
                            <div class="stage-input-wrap"><label>PPM</label><input type="number" name="remaja_ppm" min="0" value="{{ old('remaja_ppm') }}"></div>
                            <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="remaja_ph" min="0" max="14" value="{{ old('remaja_ph') }}"></div>
                        </div>
                    </div>

                    <div class="stage-row">
                        <div class="stage-row-header" style="color: #991b1b;"><i class="ph ph-basket"></i> Panen</div>
                        <div class="stage-inputs">
                            <div class="stage-input-wrap"><label>Durasi(hr)*</label><input type="number" name="dewasa_days" id="dewasa_days" min="1" max="365" value="{{ old('dewasa_days', 5) }}" required></div>
                            <div class="stage-input-wrap"><label>PPM</label><input type="number" name="dewasa_ppm" min="0" value="{{ old('dewasa_ppm') }}"></div>
                            <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="dewasa_ph" min="0" max="14" value="{{ old('dewasa_ph') }}"></div>
                        </div>
                    </div>

                    <div class="timeline-preview" id="timelinePreview">
                        <div class="timeline-segment tl-semai" id="tl-semai" style="width:25%;"></div>
                        <div class="timeline-segment tl-tanam" id="tl-tanam" style="width:33%;"></div>
                        <div class="timeline-segment tl-remaja" id="tl-remaja" style="width:25%;"></div>
                        <div class="timeline-segment tl-dewasa" id="tl-dewasa" style="width:17%;"></div>
                    </div>
                    <div style="font-size:0.75rem;color:#6b7280;margin-top:0.5rem;text-align:center;">
                        Total Masa: <strong id="totalDaysPreview" style="color:#059669;">30</strong> hr
                    </div>
                </div>

                <div class="form-group">
                    <label>Keterangan Tambahan</label>
                    <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                </div>

                <button type="submit" class="btn-primary-form">
                    <i class="ph ph-floppy-disk"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Data Tanaman</h3>
            <button onclick="closeEditModal()" class="close-modal">&times;</button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="form-group">
                <label>Nama Tanaman <span>*</span></label>
                <input type="text" name="name" id="editName" class="form-control" required>
            </div>

            <div class="stage-section" style="padding:1rem;">
                <div class="stage-section-title" style="margin-bottom:0.75rem;"><i class="ph ph-sliders"></i> Tahapan & Standar</div>
                
                <div class="stage-row" style="padding:0.75rem;margin-bottom:0.5rem;">
                    <div class="stage-row-header" style="color: #166534;font-size:0.8rem;"><i class="ph ph-seedling"></i> Penyemaian</div>
                    <div class="stage-inputs">
                        <div class="stage-input-wrap"><label>Durasi*</label><input type="number" name="semai_days" id="editSemaiDays" min="1" required></div>
                        <div class="stage-input-wrap"><label>PPM</label><input type="number" name="semai_ppm" id="editSemaiPpm" min="0"></div>
                        <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="semai_ph" id="editSemaiPh" min="0" max="14"></div>
                    </div>
                </div>

                <div class="stage-row" style="padding:0.75rem;margin-bottom:0.5rem;">
                    <div class="stage-row-header" style="color: #1e40af;font-size:0.8rem;"><i class="ph ph-plant"></i> Penanaman</div>
                    <div class="stage-inputs">
                        <div class="stage-input-wrap"><label>Durasi*</label><input type="number" name="tanam_days" id="editTanamDays" min="1" required></div>
                        <div class="stage-input-wrap"><label>PPM</label><input type="number" name="tanam_ppm" id="editTanamPpm" min="0"></div>
                        <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="tanam_ph" id="editTanamPh" min="0" max="14"></div>
                    </div>
                </div>

                <div class="stage-row" style="padding:0.75rem;margin-bottom:0.5rem;">
                    <div class="stage-row-header" style="color: #854d0e;font-size:0.8rem;"><i class="ph ph-tree"></i> Remaja</div>
                    <div class="stage-inputs">
                        <div class="stage-input-wrap"><label>Durasi*</label><input type="number" name="remaja_days" id="editRemajaDays" min="1" required></div>
                        <div class="stage-input-wrap"><label>PPM</label><input type="number" name="remaja_ppm" id="editRemajaPpm" min="0"></div>
                        <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="remaja_ph" id="editRemajaPh" min="0" max="14"></div>
                    </div>
                </div>

                <div class="stage-row" style="padding:0.75rem;margin-bottom:0.5rem;">
                    <div class="stage-row-header" style="color: #991b1b;font-size:0.8rem;"><i class="ph ph-basket"></i> Panen</div>
                    <div class="stage-inputs">
                        <div class="stage-input-wrap"><label>Durasi*</label><input type="number" name="dewasa_days" id="editDewasaDays" min="1" required></div>
                        <div class="stage-input-wrap"><label>PPM</label><input type="number" name="dewasa_ppm" id="editDewasaPpm" min="0"></div>
                        <div class="stage-input-wrap"><label>pH</label><input type="number" step="0.1" name="dewasa_ph" id="editDewasaPh" min="0" max="14"></div>
                    </div>
                </div>

                <div class="timeline-preview" id="editTimelinePreview" style="margin-top:0.75rem;height:12px;">
                    <div class="timeline-segment tl-semai" id="etl-semai" style="width:25%;"></div>
                    <div class="timeline-segment tl-tanam" id="etl-tanam" style="width:33%;"></div>
                    <div class="timeline-segment tl-remaja" id="etl-remaja" style="width:25%;"></div>
                    <div class="timeline-segment tl-dewasa" id="etl-dewasa" style="width:17%;"></div>
                </div>
                <div style="font-size:0.75rem;color:#6b7280;margin-top:0.5rem;text-align:center;">
                    Total Masa Tumbuh: <strong id="editTotalDays" style="color:#059669;">30</strong> hr
                </div>
            </div>

            <div class="form-group">
                <label>Keterangan Tambahan</label>
                <input type="text" name="description" id="editDescription" class="form-control">
            </div>
            
            <div style="display:flex;gap:1rem;margin-top:1.5rem;">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline" style="flex:1;">Batalkan</button>
                <button type="submit" class="btn btn-primary" style="flex:2;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function updateTimeline(semaiId, tanamId, remajaId, dewasaId, totalId, tls) {
    const s = Math.max(0, parseInt(document.getElementById(semaiId)?.value) || 0);
    const t = Math.max(0, parseInt(document.getElementById(tanamId)?.value) || 0);
    const r = Math.max(0, parseInt(document.getElementById(remajaId)?.value) || 0);
    const d = Math.max(0, parseInt(document.getElementById(dewasaId)?.value) || 0);
    const total = s + t + r + d;

    if (totalId) document.getElementById(totalId).textContent = total;

    if (total > 0) {
        const pS = Math.round((s / total) * 100);
        const pT = Math.round((t / total) * 100);
        const pR = Math.round((r / total) * 100);
        const pD = 100 - pS - pT - pR;
        if (document.getElementById(tls + '-semai'))  document.getElementById(tls + '-semai').style.width  = pS + '%';
        if (document.getElementById(tls + '-tanam'))  document.getElementById(tls + '-tanam').style.width  = pT + '%';
        if (document.getElementById(tls + '-remaja')) document.getElementById(tls + '-remaja').style.width = pR + '%';
        if (document.getElementById(tls + '-dewasa')) document.getElementById(tls + '-dewasa').style.width = Math.max(0, pD) + '%';
    }
}

// Add form listeners
['semai_days','tanam_days','remaja_days','dewasa_days'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => updateTimeline('semai_days','tanam_days','remaja_days','dewasa_days','totalDaysPreview','tl'));
});
updateTimeline('semai_days','tanam_days','remaja_days','dewasa_days','totalDaysPreview','tl');

// Edit form listeners
['editSemaiDays','editTanamDays','editRemajaDays','editDewasaDays'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => updateTimeline('editSemaiDays','editTanamDays','editRemajaDays','editDewasaDays','editTotalDays','etl'));
});

// Edit Modal
function openEditModal(id, name, s_days, s_ppm, s_ph, t_days, t_ppm, t_ph, r_days, r_ppm, r_ph, d_days, d_ppm, d_ph, description) {
    document.getElementById('editName').value = name;
    
    document.getElementById('editSemaiDays').value = s_days;
    document.getElementById('editSemaiPpm').value = s_ppm !== null ? s_ppm : '';
    document.getElementById('editSemaiPh').value = s_ph !== null ? s_ph : '';
    
    document.getElementById('editTanamDays').value = t_days;
    document.getElementById('editTanamPpm').value = t_ppm !== null ? t_ppm : '';
    document.getElementById('editTanamPh').value = t_ph !== null ? t_ph : '';
    
    document.getElementById('editRemajaDays').value = r_days;
    document.getElementById('editRemajaPpm').value = r_ppm !== null ? r_ppm : '';
    document.getElementById('editRemajaPh').value = r_ph !== null ? r_ph : '';
    
    document.getElementById('editDewasaDays').value = d_days;
    document.getElementById('editDewasaPpm').value = d_ppm !== null ? d_ppm : '';
    document.getElementById('editDewasaPh').value = d_ph !== null ? d_ph : '';
    
    document.getElementById('editDescription').value = description;
    
    document.getElementById('editForm').action = '/hydroponics/master-data/plants/' + id;
    document.getElementById('editModal').classList.add('active');
    updateTimeline('editSemaiDays','editTanamDays','editRemajaDays','editDewasaDays','editTotalDays','etl');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Close modal on backdrop click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>

@endsection
