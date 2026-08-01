@extends('layouts.app')
@section('title', 'Catatan Kerusakan')
@section('content')

<style>
/* ── Layout ────────────────────────────────── */
.dn-layout { display: grid; grid-template-columns: 1fr 360px; gap: 1.5rem; align-items: start; }
@media (max-width: 920px) { .dn-layout { grid-template-columns: 1fr; } }

/* ── Card ─────────────────────────────────── */
.card { background: var(--card-bg, white); border-radius: 14px; border: 1px solid var(--border-color, #e5e7eb); box-shadow: 0 2px 8px rgba(0,0,0,0.05); }
.card-header { padding: 1rem 1.4rem; border-bottom: 1px solid var(--border-color, #e5e7eb); display: flex; align-items: center; gap: 0.6rem; }
.card-header h2 { font-size: 1rem; font-weight: 700; color: var(--text-primary); margin: 0; }
.card-body { padding: 1.25rem 1.4rem; }

/* ── Stats row ─────────────────────────────── */
.stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.4rem; }
@media (max-width: 700px) { .stats-row { grid-template-columns: repeat(2,1fr); } }
.stat-card { background: white; border: 1px solid #e5e7eb; border-radius: 11px; padding: 0.9rem 1rem; display: flex; align-items: center; gap: 0.65rem; }
.stat-icon { width: 40px; height: 40px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.stat-label { font-size: 0.7rem; color: #6b7280; font-weight: 600; text-transform: uppercase; }
.stat-value  { font-size: 1.3rem; font-weight: 700; color: #111827; }

/* ── Table ─────────────────────────────────── */
.dn-table { width: 100%; border-collapse: collapse; }
.dn-table th {
    text-align: left; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;
    color: #6b7280; font-weight: 700; padding: 0.6rem 0.85rem;
    border-bottom: 2px solid #e5e7eb; white-space: nowrap;
}
.dn-table td { padding: 0.75rem 0.85rem; border-bottom: 1px solid #f3f4f6; vertical-align: middle; font-size: 0.875rem; color: var(--text-primary); }
.dn-table tr:last-child td { border-bottom: none; }
.dn-table tr:hover td { background: #f9fafb; }

/* ── Badges ────────────────────────────────── */
.badge { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.2rem 0.6rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; }

/* ── Form ──────────────────────────────────── */
.form-group { margin-bottom: 0.9rem; }
.form-group label { display: block; font-size: 0.82rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.35rem; }
.form-group label span { color: #dc2626; }
.form-control {
    width: 100%; padding: 0.55rem 0.8rem; border: 1.5px solid #d1d5db; border-radius: 8px;
    font-size: 0.875rem; color: var(--text-primary); background: var(--card-bg, white);
    transition: border-color 0.15s; box-sizing: border-box;
}
.form-control:focus { outline: none; border-color: #dc2626; box-shadow: 0 0 0 3px rgba(220,38,38,0.1); }
textarea.form-control { resize: vertical; min-height: 80px; }
.btn-primary {
    width: 100%; padding: 0.65rem; background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white; border: none; border-radius: 9px; font-size: 0.9rem; font-weight: 600;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    transition: all 0.2s;
}
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(220,38,38,0.35); }

/* ── Filter bar ────────────────────────────── */
.filter-bar { display: flex; gap: 0.6rem; flex-wrap: wrap; margin-bottom: 1rem; align-items: flex-end; }
.filter-bar select, .filter-bar input[type="text"], .filter-bar input[type="date"] {
    padding: 0.45rem 0.7rem; border: 1.5px solid #d1d5db; border-radius: 8px;
    font-size: 0.82rem; color: #374151; background: white; min-width: 0;
}
.filter-bar button {
    padding: 0.45rem 1rem; border-radius: 8px; font-size: 0.82rem; font-weight: 600;
    cursor: pointer; border: none; background: #374151; color: white; white-space: nowrap;
}
.filter-bar .btn-clear { background: #f3f4f6; color: #374151; }

/* ── Modals ────────────────────────────────── */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1000; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal-box { background: white; border-radius: 14px; padding: 1.5rem; width: 100%; max-width: 440px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
.modal-box h3 { font-size: 1.1rem; font-weight: 700; margin: 0 0 1.2rem; color: #111827; }

/* ── Alert ─────────────────────────────────── */
.alert { padding: 0.75rem 1rem; border-radius: 9px; margin-bottom: 1.2rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; }
.alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
.alert-error   { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

/* ── Empty ─────────────────────────────────── */
.empty-state { text-align: center; padding: 3rem 1rem; color: #9ca3af; }
.empty-state i { font-size: 3rem; margin-bottom: 0.75rem; display: block; }

/* ── Action buttons ─────────────────────────── */
.btn-sm { display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.28rem 0.65rem; border-radius: 7px; font-size: 0.75rem; font-weight: 600; cursor: pointer; border: none; transition: all 0.15s; }
.btn-update { background: #fef3c7; color: #d97706; }
.btn-update:hover { background: #fde68a; }
.btn-delete { background: #fee2e2; color: #dc2626; }
.btn-delete:hover { background: #fecaca; }
</style>

{{-- Page header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:700;color:var(--text-primary);margin:0;display:flex;align-items:center;gap:0.5rem;">
            <i class="ph ph-warning-octagon" style="color:#dc2626;"></i> Catatan Kerusakan
        </h1>
        <p style="color:#6b7280;font-size:0.85rem;margin:0.25rem 0 0;">Laporan dan pencatatan kerusakan tanaman di seluruh greenhouse</p>
    </div>
    <button onclick="document.getElementById('addModal').classList.add('open')"
        style="padding:0.6rem 1.25rem;background:linear-gradient(135deg,#dc2626,#b91c1c);color:white;border:none;border-radius:9px;font-weight:600;font-size:0.875rem;cursor:pointer;display:flex;align-items:center;gap:0.4rem;">
        <i class="ph ph-plus"></i> Tambah Catatan
    </button>
</div>

@if(session('success'))
<div class="alert alert-success"><i class="ph ph-check-circle"></i> {{ session('success') }}</div>
@endif
@if($errors->any())
<div class="alert alert-error"><i class="ph ph-warning-circle"></i> {{ $errors->first() }}</div>
@endif

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;"><i class="ph ph-warning-octagon" style="color:#dc2626;"></i></div>
        <div><div class="stat-label">Belum Ditangani</div><div class="stat-value" style="color:#dc2626;">{{ $totalOpen }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fef3c7;"><i class="ph ph-spinner" style="color:#d97706;"></i></div>
        <div><div class="stat-label">Sedang Ditangani</div><div class="stat-value" style="color:#d97706;">{{ $totalHandling }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#dcfce7;"><i class="ph ph-check-circle" style="color:#16a34a;"></i></div>
        <div><div class="stat-label">Sudah Selesai</div><div class="stat-value" style="color:#16a34a;">{{ $totalResolved }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:#fee2e2;"><i class="ph ph-fire" style="color:#dc2626;"></i></div>
        <div><div class="stat-label">Kerusakan Berat</div><div class="stat-value" style="color:#dc2626;">{{ $totalBerat }}</div></div>
    </div>
</div>

{{-- Filter bar --}}
<form method="GET" action="{{ route('hydroponics.damage-notes') }}">
    <div class="filter-bar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari tanaman / lokasi..." style="flex:1;min-width:160px;">
        <select name="status">
            <option value="">Semua Status</option>
            <option value="open"     {{ request('status')=='open'?'selected':'' }}>❌ Belum Ditangani</option>
            <option value="handling" {{ request('status')=='handling'?'selected':'' }}>⏳ Sedang Ditangani</option>
            <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>✅ Selesai</option>
        </select>
        <select name="severity">
            <option value="">Semua Tingkat</option>
            <option value="ringan" {{ request('severity')=='ringan'?'selected':'' }}>🟢 Ringan</option>
            <option value="sedang" {{ request('severity')=='sedang'?'selected':'' }}>🟡 Sedang</option>
            <option value="berat"  {{ request('severity')=='berat'?'selected':'' }}>🔴 Berat</option>
        </select>
        <select name="damage_type">
            <option value="">Semua Jenis</option>
            @foreach(['umum','hama','penyakit','kekeringan','nutrisi','fisik','lainnya'] as $dt)
            <option value="{{ $dt }}" {{ request('damage_type')==$dt?'selected':'' }}>{{ ucfirst($dt) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" title="Dari tanggal">
        <input type="date" name="date_to"   value="{{ request('date_to') }}"   title="Sampai tanggal">
        <button type="submit"><i class="ph ph-funnel"></i> Filter</button>
        <a href="{{ route('hydroponics.damage-notes') }}" class="btn-sm" style="padding:0.45rem 0.85rem;background:#f3f4f6;color:#374151;border-radius:8px;text-decoration:none;font-weight:600;font-size:0.82rem;">Reset</a>
    </div>
</form>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <i class="ph ph-table" style="color:#dc2626;font-size:1.1rem;"></i>
        <h2>Daftar Catatan Kerusakan</h2>
        <span style="margin-left:auto;background:#fee2e2;color:#dc2626;padding:0.2rem 0.65rem;border-radius:20px;font-size:0.75rem;font-weight:700;">
            {{ $notes->count() }} catatan
        </span>
    </div>
    <div style="overflow-x:auto;">
        @if($notes->isEmpty())
        <div class="empty-state">
            <i class="ph ph-check-circle" style="color:#16a34a;"></i>
            <p>Tidak ada catatan kerusakan{{ request()->anyFilled(['search','status','severity','damage_type']) ? ' yang sesuai filter' : '' }}.</p>
        </div>
        @else
        <table class="table datatable" style="width: 100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Waktu</th>
                    <th>Tanaman</th>
                    <th>Lokasi</th>
                    <th>Jenis Kerusakan</th>
                    <th>Deskripsi</th>
                    <th>Tingkat</th>
                    <th>Status</th>
                    <th>Tindakan</th>
                    <th>Dicatat Oleh</th>
                    <th style="width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notes as $i => $note)
                <tr>
                    <td style="color:#9ca3af;font-size:0.78rem;">{{ $i + 1 }}</td>
                    <td style="white-space:nowrap;font-size:0.8rem;">
                        <div style="font-weight:600;">{{ $note->damaged_at->format('d M Y') }}</div>
                        <div style="color:#9ca3af;">{{ $note->damaged_at->format('H:i') }}</div>
                    </td>
                    <td>
                        @if($note->plant_name)
                            <span style="display:inline-flex;align-items:center;gap:0.3rem;font-weight:600;">
                                <i class="ph ph-plant" style="color:#16a34a;"></i> {{ $note->plant_name }}
                            </span>
                        @else
                            <span style="color:#9ca3af;">—</span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem;max-width:180px;">
                        @if($note->location)
                            <span title="{{ $note->location }}" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:180px;">
                                <i class="ph ph-map-pin" style="color:#6b7280;font-size:0.8rem;"></i> {{ $note->location }}
                            </span>
                        @else
                            <span style="color:#9ca3af;">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                        $dtIcon = ['hama'=>'ph-bug','penyakit'=>'ph-virus','kekeringan'=>'ph-drop-slash','nutrisi'=>'ph-flask','fisik'=>'ph-hammer','umum'=>'ph-warning','lainnya'=>'ph-dots-three'];
                        @endphp
                        <span style="display:inline-flex;align-items:center;gap:0.3rem;font-size:0.8rem;">
                            <i class="ph {{ $dtIcon[$note->damage_type] ?? 'ph-warning' }}"></i>
                            {{ ucfirst($note->damage_type) }}
                        </span>
                    </td>
                    <td style="max-width:220px;">
                        <span title="{{ $note->description }}" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:220px;font-size:0.82rem;">
                            {{ $note->description }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:{{ $note->severityBg() }};color:{{ $note->severityBadgeColor() }};">
                            {{ ucfirst($note->severity) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge" style="background:{{ $note->statusBg() }};color:{{ $note->statusBadgeColor() }};">
                            @if($note->status === 'open') ❌
                            @elseif($note->status === 'handling') ⏳
                            @else ✅
                            @endif
                            {{ match($note->status) { 'open'=>'Belum Ditangani', 'handling'=>'Ditangani', 'resolved'=>'Selesai', default=>$note->status } }}
                        </span>
                    </td>
                    <td style="max-width:160px;font-size:0.8rem;">
                        @if($note->action_taken)
                            <span title="{{ $note->action_taken }}" style="display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:160px;">
                                {{ $note->action_taken }}
                            </span>
                        @else
                            <span style="color:#9ca3af;">—</span>
                        @endif
                    </td>
                    <td style="font-size:0.8rem;white-space:nowrap;">
                        {{ $note->user->name ?? '—' }}<br>
                        <span style="color:#9ca3af;font-size:0.72rem;">{{ $note->created_at->diffForHumans() }}</span>
                    </td>
                    <td>
                        <div style="display:flex;gap:4px;justify-content:flex-end;">
                            <button class="dt-action-btn dt-btn-edit" title="Update"
                                onclick="openUpdateModal({{ $note->id }}, '{{ $note->status }}', '{{ addslashes($note->action_taken ?? '') }}')">
                                <i class="ph ph-pencil-simple"></i>
                            </button>
                            <button class="dt-action-btn dt-btn-delete" title="Hapus"
                                onclick="confirmAction('Hapus Catatan?', 'Yakin ingin menghapus catatan kerusakan ini?', '{{ route('hydroponics.damage-notes.destroy', $note->id) }}', 'DELETE')">
                                <i class="ph ph-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- ===== ADD MODAL ===== --}}
<div class="modal-overlay" id="addModal">
    <div class="modal-box" style="max-width:500px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
            <h3 style="color:#dc2626;"><i class="ph ph-warning-octagon"></i> Tambah Catatan Kerusakan</h3>
            <button onclick="document.getElementById('addModal').classList.remove('open')"
                style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#6b7280;">×</button>
        </div>
        <form method="POST" action="{{ route('hydroponics.damage-notes.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 0.75rem;">
                <div class="form-group">
                    <label>Nama Tanaman</label>
                    <input type="text" name="plant_name" class="form-control" placeholder="Pakcoy, Selada...">
                </div>
                <div class="form-group">
                    <label>Waktu Kerusakan</label>
                    <input type="datetime-local" name="damaged_at" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0 0.75rem;">
                <div class="form-group">
                    <label>Jenis Kerusakan <span>*</span></label>
                    <select name="damage_type" class="form-control" required>
                        <option value="umum">Umum</option>
                        <option value="hama">Hama</option>
                        <option value="penyakit">Penyakit</option>
                        <option value="kekeringan">Kekeringan</option>
                        <option value="nutrisi">Defisiensi Nutrisi</option>
                        <option value="fisik">Kerusakan Fisik</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tingkat Keparahan <span>*</span></label>
                    <select name="severity" class="form-control" required>
                        <option value="ringan">🟢 Ringan</option>
                        <option value="sedang" selected>🟡 Sedang</option>
                        <option value="berat">🔴 Berat</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Lokasi (manual, opsional)</label>
                <input type="text" name="location_manual" class="form-control" placeholder="cth: GH A › Rak 1 › L5">
            </div>
            <div class="form-group">
                <label>Deskripsi Kerusakan <span>*</span></label>
                <textarea name="description" class="form-control" required placeholder="Jelaskan kondisi kerusakan yang terjadi..."></textarea>
            </div>
            <div class="form-group">
                <label>Tindakan yang Sudah Diambil</label>
                <textarea name="action_taken" class="form-control" placeholder="Pestisida, pemisahan, penggantian nutrisi..."></textarea>
            </div>
            <button type="submit" class="btn-primary">
                <i class="ph ph-plus"></i> Simpan Catatan Kerusakan
            </button>
        </form>
    </div>
</div>

{{-- ===== UPDATE STATUS MODAL ===== --}}
<div class="modal-overlay" id="updateModal">
    <div class="modal-box" style="max-width:400px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;">
            <h3><i class="ph ph-pencil" style="color:#d97706;"></i> Update Status Penanganan</h3>
            <button onclick="document.getElementById('updateModal').classList.remove('open')"
                style="background:none;border:none;font-size:1.5rem;cursor:pointer;color:#6b7280;">×</button>
        </div>
        <form method="POST" id="updateForm">
            @csrf
            <div class="form-group">
                <label>Status Penanganan <span style="color:#dc2626;">*</span></label>
                <select name="status" id="updateStatus" class="form-control" required>
                    <option value="open">❌ Belum Ditangani</option>
                    <option value="handling">⏳ Sedang Ditangani</option>
                    <option value="resolved">✅ Sudah Selesai</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tindakan yang Diambil</label>
                <textarea name="action_taken" id="updateAction" class="form-control" rows="3" placeholder="Deskripsikan tindakan penanganan..."></textarea>
            </div>
            <div style="display:flex;gap:0.75rem;margin-top:0.5rem;">
                <button type="button" onclick="document.getElementById('updateModal').classList.remove('open')"
                    style="flex:1;padding:0.6rem;border:1.5px solid #d1d5db;border-radius:8px;background:white;color:#374151;font-weight:600;cursor:pointer;">Batal</button>
                <button type="submit"
                    style="flex:2;padding:0.6rem;background:linear-gradient(135deg,#d97706,#b45309);color:white;border:none;border-radius:8px;font-weight:600;cursor:pointer;">
                    <i class="ph ph-check"></i> Simpan Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openUpdateModal(id, status, action) {
    document.getElementById('updateStatus').value = status;
    document.getElementById('updateAction').value = action;
    document.getElementById('updateForm').action = '/hydroponics/damage-notes/' + id;
    document.getElementById('updateModal').classList.add('open');
}
// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(el => {
    el.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});
</script>
@endsection
