@extends('layouts.app')
@section('title', 'Pencatatan Semai — ASR FARM')

@section('content')
<style>
.semai-stat-card {
    position: relative;
    border-radius: 16px; 
    padding: 1.5rem;
    display: flex; 
    flex-direction: column;
    justify-content: space-between;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    z-index: 1;
}
.semai-stat-card:hover { 
    transform: translateY(-5px); 
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    border-color: transparent;
}
.semai-stat-card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--theme-color, #ccc);
}
.semai-stat-icon {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; flex-shrink: 0;
    margin-bottom: 1.25rem;
    color: white;
    background: var(--theme-grad, #ccc);
    box-shadow: 0 4px 10px var(--theme-shadow, rgba(0,0,0,0.1));
}
.semai-stat-val { font-size: 2rem; font-weight: 800; line-height: 1.1; color: var(--text-main); letter-spacing: -0.5px;}
.semai-stat-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-top: 0.35rem; }

.semai-bg-icon {
    position: absolute;
    right: -10px;
    bottom: -15px;
    font-size: 6.5rem;
    color: var(--theme-color, #ccc);
    opacity: 0.06;
    z-index: -1;
    transform: rotate(-10deg);
    transition: all 0.4s ease;
}
.semai-stat-card:hover .semai-bg-icon {
    transform: rotate(0deg) scale(1.1);
    opacity: 0.12;
}

.badge-aktif    { background:#dcfce7; color:#15803d; }
.badge-siap     { background:#fef9c3; color:#854d0e; }
.badge-pindah   { background:#dbeafe; color:#1e40af; }
.badge-gagal    { background:#fee2e2; color:#991b1b; }

.semai-badge {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.25rem 0.65rem; border-radius: 50px;
    font-size: 0.72rem; font-weight: 700;
}

.remaining-chip {
    display: inline-block; padding: 0.2rem 0.6rem;
    border-radius: 6px; font-size: 0.75rem; font-weight: 700;
}
.remaining-chip.danger  { background:#fee2e2; color:#dc2626; }
.remaining-chip.warning { background:#fef9c3; color:#b45309; }
.remaining-chip.ok      { background:#dcfce7; color:#16a34a; }
.remaining-chip.done    { background:#dbeafe; color:#2563eb; }

.progress-semai {
    height: 6px; border-radius: 50px; background: var(--border-color);
    overflow: hidden; margin-top: 0.25rem;
}
.progress-semai-bar {
    height: 100%; border-radius: 50px; background: #16a34a;
    transition: width 0.4s ease;
}
</style>

<div style="display:flex; flex-direction:column; gap:1.5rem;">

    {{-- PAGE HEADER --}}
    <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1rem;">
        <div>
            <h1 style="font-size:1.4rem; font-weight:800; color:var(--text-main); margin:0; display:flex; align-items:center; gap:0.5rem;">
                <i class="ph ph-seedling" style="color:#16a34a;"></i> Pencatatan Semai
            </h1>
            <p style="margin:0.25rem 0 0; font-size:0.82rem; color:var(--text-muted);">
                Area khusus pembibitan sebelum dipindahkan ke Greenhouse
            </p>
        </div>
        @if(Auth::user()->role === 'super_admin')
        <button onclick="document.getElementById('modalTambahSemai').classList.add('active')"
            style="background:var(--asr-green); color:white; border:none; border-radius:9px; padding:0.65rem 1.25rem; font-weight:700; font-size:0.85rem; cursor:pointer; display:flex; align-items:center; gap:0.4rem;">
            <i class="ph ph-plus"></i> Catat Semai Baru
        </button>
        @endif
    </div>

    {{-- SUCCESS / ERROR ALERT --}}
    @if(session('success'))
    <div style="background:#dcfce7; border:1px solid #bbf7d0; border-radius:10px; padding:0.85rem 1.25rem; color:#15803d; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    {{-- STAT CARDS --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px,1fr)); gap:1rem;">
        <div class="semai-stat-card" style="--theme-color: #10b981; --theme-grad: linear-gradient(135deg, #34d399, #10b981); --theme-shadow: rgba(16, 185, 129, 0.3);">
            <div class="semai-stat-icon">
                <i class="ph ph-seedling"></i>
            </div>
            <div>
                <div class="semai-stat-val">{{ $totalAktif }}</div>
                <div class="semai-stat-label">Batch Aktif Disemai</div>
            </div>
            <i class="ph ph-seedling semai-bg-icon"></i>
        </div>
        
        <div class="semai-stat-card" style="--theme-color: #f59e0b; --theme-grad: linear-gradient(135deg, #fbbf24, #f59e0b); --theme-shadow: rgba(245, 158, 11, 0.3);">
            <div class="semai-stat-icon">
                <i class="ph ph-clock-countdown"></i>
            </div>
            <div>
                <div class="semai-stat-val">{{ $totalSiapPindah }}</div>
                <div class="semai-stat-label">Siap Pindah ke GH</div>
            </div>
            <i class="ph ph-clock-countdown semai-bg-icon"></i>
        </div>

        <div class="semai-stat-card" style="--theme-color: #0ea5e9; --theme-grad: linear-gradient(135deg, #38bdf8, #0ea5e9); --theme-shadow: rgba(14, 165, 233, 0.3);">
            <div class="semai-stat-icon">
                <i class="ph ph-arrow-square-right"></i>
            </div>
            <div>
                <div class="semai-stat-val">{{ $totalBenih }}</div>
                <div class="semai-stat-label">Total Lubang Aktif</div>
            </div>
            <i class="ph ph-arrow-square-right semai-bg-icon"></i>
        </div>

        <div class="semai-stat-card" style="--theme-color: #6366f1; --theme-grad: linear-gradient(135deg, #818cf8, #6366f1); --theme-shadow: rgba(99, 102, 241, 0.3);">
            <div class="semai-stat-icon">
                <i class="ph ph-check-square"></i>
            </div>
            <div>
                <div class="semai-stat-val">{{ $totalSudahPindah }}</div>
                <div class="semai-stat-label">Sudah Pindah ke GH</div>
            </div>
            <i class="ph ph-check-square semai-bg-icon"></i>
        </div>

        <div class="semai-stat-card" style="--theme-color: #ef4444; --theme-grad: linear-gradient(135deg, #f87171, #ef4444); --theme-shadow: rgba(239, 68, 68, 0.3);">
            <div class="semai-stat-icon">
                <i class="ph ph-x-circle"></i>
            </div>
            <div>
                <div class="semai-stat-val">{{ $totalGagal }}</div>
                <div class="semai-stat-label">Gagal Semai</div>
            </div>
            <i class="ph ph-x-circle semai-bg-icon"></i>
        </div>
    </div>



    {{-- ACTIVE SEMAI CARDS (Aktif) --}}
    @php $aktifList = $semais->where('status','aktif'); @endphp
    @if($aktifList->count() > 0)
    <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding:1rem 1.5rem; border-bottom:1px solid var(--border-color); display:flex; align-items:center; justify-content:space-between;">
            <h2 style="margin:0; font-size:1rem; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
                <i class="ph ph-seedling" style="color:#16a34a;"></i> Sedang Disemai
                <span style="background:#dcfce7; color:#15803d; font-size:0.72rem; padding:0.15rem 0.5rem; border-radius:50px;">{{ $aktifList->count() }} batch</span>
            </h2>
        </div>
        <div style="padding:1.25rem; display:grid; grid-template-columns:repeat(auto-fill, minmax(300px,1fr)); gap:1rem;">
            @foreach($aktifList as $s)
            @php
                $days = $s->daysOld();
                $total = $s->plantType?->semai_days ?? 7;
                $pct = min(100, $total > 0 ? round(($days / $total) * 100) : 0);
                $rem = $s->remainingDays();
                $chipClass = $rem < 0 ? 'danger' : ($rem === 0 ? 'warning' : 'ok');
                $chipText  = $rem < 0 ? abs($rem).' hari terlambat' : ($rem === 0 ? 'Siap hari ini!' : $rem.' hari lagi');
            @endphp
            <div class="card" style="padding:0; overflow:hidden;">
                {{-- Card top color band --}}
                <div style="height:5px; background:{{ $s->plantType?->color ?? '#16a34a' }};"></div>
                <div style="padding:1rem 1.25rem;">
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:0.75rem;">
                        <div>
                            <div style="font-weight:800; font-size:1rem; color:var(--text-main);">{{ $s->plant_name }}</div>
                            <div style="font-size:0.75rem; color:var(--text-muted); margin-top:0.1rem;">
                                Mulai: {{ \Carbon\Carbon::parse($s->semai_date)->format('d M Y') }}
                            </div>
                        </div>
                        <span class="semai-badge badge-aktif">
                            <span style="width:6px;height:6px;border-radius:50%;background:#16a34a;"></span>
                            Aktif
                        </span>
                    </div>

                    {{-- Progress bar --}}
                    <div style="display:flex; justify-content:space-between; font-size:0.72rem; color:var(--text-muted); margin-bottom:0.25rem;">
                        <span>Hari ke-{{ $days }} / {{ $total }} hari</span>
                        <span>{{ $pct }}%</span>
                    </div>
                    <div class="progress-semai">
                        <div class="progress-semai-bar" style="width:{{ $pct }}%; background:{{ $s->plantType?->color ?? '#16a34a' }};"></div>
                    </div>

                    {{-- Details grid --}}
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; margin-top:0.85rem; font-size:0.78rem;">
                        <div style="color:var(--text-muted);">Jumlah</div>
                        <div style="font-weight:700; color:var(--text-main);">{{ number_format($s->quantity) }} lubang</div>
                        <div style="color:var(--text-muted);">Estimasi Pindah</div>
                        <div style="font-weight:700; color:var(--text-main);">
                            {{ $s->estimated_transfer_date?->format('d M Y') ?? '-' }}
                        </div>
                        <div style="color:var(--text-muted);">Target GH</div>
                        <div style="font-weight:700; color:var(--text-main);">{{ $s->targetGreenhouse?->name ?? 'Belum ditentukan' }}</div>
                        <div style="color:var(--text-muted);">Status</div>
                        <div><span class="remaining-chip {{ $chipClass }}">{{ $chipText }}</span></div>
                    </div>

                    @if($s->notes)
                    <div style="margin-top:0.75rem; padding:0.5rem 0.75rem; background:var(--bg-color); border-radius:7px; font-size:0.75rem; color:var(--text-muted);">
                        <i class="ph ph-note"></i> {{ $s->notes }}
                    </div>
                    @endif

                    {{-- Actions --}}
                    @if(Auth::user()->role === 'super_admin')
                    <div style="display:flex; gap:0.5rem; margin-top:1rem; flex-wrap:wrap;">
                        <button onclick="openTransferModal({{ $s->id }}, '{{ addslashes($s->plant_name) }}')"
                            style="flex:1; background:var(--asr-green); color:white; border:none; border-radius:7px; padding:0.5rem; font-size:0.78rem; font-weight:700; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:0.3rem;">
                            <i class="ph ph-arrow-square-right"></i> Pindah ke GH
                        </button>
                        <button onclick="confirmFail({{ $s->id }}, '{{ addslashes($s->plant_name) }}')"
                            style="background:var(--border-color); color:var(--text-muted); border:none; border-radius:7px; padding:0.5rem 0.75rem; font-size:0.78rem; font-weight:700; cursor:pointer;">
                            <i class="ph ph-x"></i> Gagal
                        </button>
                        <form method="POST" action="/hydroponics/semai/{{ $s->id }}/delete" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Hapus catatan semai ini?')"
                                style="background:transparent; color:#dc2626; border:1px solid #fca5a5; border-radius:7px; padding:0.5rem 0.75rem; font-size:0.78rem; cursor:pointer;">
                                <i class="ph ph-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- HISTORY TABLE --}}
    @php $historyList = $semais->whereIn('status',['sudah_pindah','gagal']); @endphp
    @if($historyList->count() > 0)
    <div class="card" style="padding:0; overflow:hidden;">
        <div style="padding:1rem 1.5rem; border-bottom:1px solid var(--border-color);">
            <h2 style="margin:0; font-size:1rem; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
                <i class="ph ph-clock-counter-clockwise" style="color:var(--text-muted);"></i> Riwayat Semai
            </h2>
        </div>
        <div style="overflow-x:auto;">
            <table class="table datatable" style="width:100%;">
                <thead>
                    <tr style="background:var(--bg-color); border-bottom:2px solid var(--border-color);">
                        <th style="padding:0.75rem 1rem; text-align:left; font-weight:700; color:var(--text-muted); font-size:0.72rem; text-transform:uppercase; letter-spacing:0.5px;">Tanaman</th>
                        <th style="padding:0.75rem 1rem; text-align:center;">Jml</th>
                        <th style="padding:0.75rem 1rem; text-align:left;">Tgl Semai</th>
                        <th style="padding:0.75rem 1rem; text-align:left;">Pindah / Selesai</th>
                        <th style="padding:0.75rem 1rem; text-align:left;">Target GH</th>
                        <th style="padding:0.75rem 1rem; text-align:left;">Status</th>
                        @if(Auth::user()->role === 'super_admin')
                        <th style="padding:0.75rem 1rem; text-align:center;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($historyList->sortByDesc('semai_date') as $s)
                    <tr style="border-bottom:1px solid var(--border-color);">
                        <td style="padding:0.75rem 1rem; font-weight:700; color:var(--text-main);">{{ $s->plant_name }}</td>
                        <td style="padding:0.75rem 1rem; text-align:center; color:var(--text-muted);">{{ number_format($s->quantity) }}</td>
                        <td style="padding:0.75rem 1rem; color:var(--text-muted);">{{ $s->semai_date->format('d M Y') }}</td>
                        <td style="padding:0.75rem 1rem; color:var(--text-muted);">{{ $s->transferred_date?->format('d M Y') ?? '-' }}</td>
                        <td style="padding:0.75rem 1rem; color:var(--text-muted);">{{ $s->targetGreenhouse?->name ?? '-' }}</td>
                        <td style="padding:0.75rem 1rem;">
                            <span class="semai-badge {{ $s->status === 'sudah_pindah' ? 'badge-pindah' : 'badge-gagal' }}">
                                {{ $s->statusLabel() }}
                            </span>
                        </td>
                        @if(Auth::user()->role === 'super_admin')
                        <td style="padding:0.75rem 1rem; text-align:center;">
                            <form method="POST" action="/hydroponics/semai/{{ $s->id }}/delete" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus riwayat ini?')"
                                    style="background:transparent; color:#dc2626; border:1px solid #fca5a5; border-radius:6px; padding:0.3rem 0.6rem; font-size:0.75rem; cursor:pointer;">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- EMPTY STATE --}}
    @if($semais->count() === 0)
    <div class="card" style="text-align:center; padding:3.5rem 2rem; color:var(--text-muted);">
        <i class="ph ph-seedling" style="font-size:3.5rem; opacity:0.35; display:block; margin-bottom:1rem;"></i>
        <div style="font-size:1.1rem; font-weight:700; margin-bottom:0.5rem; color:var(--text-main);">Belum Ada Catatan Semai</div>
        <div style="font-size:0.85rem; margin-bottom:1.5rem;">Klik tombol "Catat Semai Baru" untuk mulai mencatat batch pembibitan.</div>
        @if(Auth::user()->role === 'super_admin')
        <button onclick="document.getElementById('modalTambahSemai').classList.add('active')"
            style="background:var(--asr-green); color:white; border:none; border-radius:9px; padding:0.75rem 1.5rem; font-weight:700; font-size:0.9rem; cursor:pointer; display:inline-flex; align-items:center; gap:0.5rem;">
            <i class="ph ph-plus"></i> Catat Semai Baru
        </button>
        @endif
    </div>
    @endif

</div>

{{-- ═══════════════════════ MODAL: Tambah Semai ═══════════════════════ --}}
<div class="modal-overlay" id="modalTambahSemai">
    <div class="modal-content" style="max-width:500px;">
        <div class="modal-header">
            <h3 style="margin:0; display:flex; align-items:center; gap:0.5rem;">
                <i class="ph ph-seedling" style="color:#16a34a;"></i> Catat Batch Semai Baru
            </h3>
            <button class="modal-close" onclick="document.getElementById('modalTambahSemai').classList.remove('active')">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <form method="POST" action="/hydroponics/semai" style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
            @csrf
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:700; color:var(--text-muted); margin-bottom:0.35rem;">Jenis Sayuran *</label>
                <select name="plant_name" required onchange="updateSemaiDays(this)"
                    style="width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:8px; background:var(--card-bg); color:var(--text-main); font-size:0.85rem;">
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($plantTypes as $pt)
                    <option value="{{ $pt->name }}" data-days="{{ $pt->semai_days ?? 7 }}">{{ $pt->name }} ({{ $pt->semai_days ?? 7 }} hari semai)</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:700; color:var(--text-muted); margin-bottom:0.35rem;">Jumlah (Lubang) *</label>
                    <input type="number" name="quantity" min="1" required placeholder="Misal: 100"
                        style="width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:8px; background:var(--card-bg); color:var(--text-main); font-size:0.85rem; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; font-size:0.8rem; font-weight:700; color:var(--text-muted); margin-bottom:0.35rem;">Tanggal Semai *</label>
                    <input type="date" name="semai_date" required value="{{ date('Y-m-d') }}"
                        style="width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:8px; background:var(--card-bg); color:var(--text-main); font-size:0.85rem; box-sizing:border-box;">
                </div>
            </div>

            <div>
                <label style="display:block; font-size:0.8rem; font-weight:700; color:var(--text-muted); margin-bottom:0.35rem;">Target Greenhouse (opsional)</label>
                <select name="target_greenhouse_id"
                    style="width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:8px; background:var(--card-bg); color:var(--text-main); font-size:0.85rem;">
                    <option value="">-- Belum ditentukan --</option>
                    @foreach($greenhouses as $gh)
                    <option value="{{ $gh->id }}">{{ $gh->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display:block; font-size:0.8rem; font-weight:700; color:var(--text-muted); margin-bottom:0.35rem;">Catatan (opsional)</label>
                <textarea name="notes" rows="2" placeholder="Kondisi benih, media semai, dll..."
                    style="width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:8px; background:var(--card-bg); color:var(--text-main); font-size:0.85rem; resize:vertical; box-sizing:border-box;"></textarea>
            </div>

            <div style="display:flex; gap:0.75rem; justify-content:flex-end; padding-top:0.5rem; border-top:1px solid var(--border-color);">
                <button type="button" onclick="document.getElementById('modalTambahSemai').classList.remove('active')"
                    style="padding:0.6rem 1.25rem; border:1px solid var(--border-color); border-radius:8px; background:transparent; color:var(--text-muted); font-weight:600; cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="padding:0.6rem 1.5rem; background:var(--asr-green); color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:0.4rem;">
                    <i class="ph ph-check"></i> Simpan Catatan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════ MODAL: Konfirmasi Pindah ke GH ═══════════════════════ --}}
<div class="modal-overlay" id="modalTransfer">
    <div class="modal-content" style="max-width:420px;">
        <div class="modal-header">
            <h3 style="margin:0; display:flex; align-items:center; gap:0.5rem;">
                <i class="ph ph-arrow-square-right" style="color:#2563eb;"></i>
                Pindahkan ke Greenhouse
            </h3>
            <button class="modal-close" onclick="document.getElementById('modalTransfer').classList.remove('active')">
                <i class="ph ph-x"></i>
            </button>
        </div>
        <form id="formTransfer" method="POST" style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">
            @csrf @method('PATCH')
            <p id="transferMsg" style="margin:0; font-size:0.9rem; color:var(--text-main);"></p>
            <div>
                <label style="display:block; font-size:0.8rem; font-weight:700; color:var(--text-muted); margin-bottom:0.35rem;">Tanggal Pindah</label>
                <input type="date" name="transferred_date" value="{{ date('Y-m-d') }}"
                    style="width:100%; padding:0.6rem 0.85rem; border:1px solid var(--border-color); border-radius:8px; background:var(--card-bg); color:var(--text-main); font-size:0.85rem; box-sizing:border-box;">
            </div>
            <div style="display:flex; gap:0.75rem; justify-content:flex-end; padding-top:0.5rem; border-top:1px solid var(--border-color);">
                <button type="button" onclick="document.getElementById('modalTransfer').classList.remove('active')"
                    style="padding:0.6rem 1.25rem; border:1px solid var(--border-color); border-radius:8px; background:transparent; color:var(--text-muted); font-weight:600; cursor:pointer;">
                    Batal
                </button>
                <button type="submit"
                    style="padding:0.6rem 1.5rem; background:#2563eb; color:white; border:none; border-radius:8px; font-weight:700; cursor:pointer; display:flex; align-items:center; gap:0.4rem;">
                    <i class="ph ph-check"></i> Konfirmasi Pindah
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden forms for fail --}}
<form id="formFail" method="POST" style="display:none;">@csrf @method('PATCH')</form>

<script>
function openTransferModal(id, name) {
    document.getElementById('formTransfer').action = '/hydroponics/semai/' + id + '/transfer';
    document.getElementById('transferMsg').textContent = 'Tandai batch "' + name + '" sebagai sudah dipindahkan ke Greenhouse?';
    document.getElementById('modalTransfer').classList.add('active');
}
function confirmFail(id, name) {
    if (confirm('Tandai batch "' + name + '" sebagai GAGAL semai?')) {
        const f = document.getElementById('formFail');
        f.action = '/hydroponics/semai/' + id + '/fail';
        f.submit();
    }
}
function updateSemaiDays(sel) {
    const opt = sel.options[sel.selectedIndex];
    const days = opt.dataset.days || 7;
    // Could update a helper text if needed
}
</script>
@endsection
