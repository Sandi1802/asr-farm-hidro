@extends('layouts.app')
@section('title', 'Detail Bedengan #' . $bedengan->nomor)
@section('content')

@php
    $zona   = $bedengan->zona;
    $kode   = $zona->kode;
    // Dukungan hierarki baru (lahan_id langsung) dan lama (via posisi)
    $lahan  = $kode->lahan ?? $kode->posisi?->lahan;
@endphp

<style>
.lubang-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 1rem; }
.lubang-item {
    width: 52px; height: 52px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.65rem; font-weight: 700; cursor: pointer;
    border: 2px solid transparent;
    transition: transform 0.15s, box-shadow 0.15s;
    color: white; flex-direction: column; gap: 1px;
    text-align: center; line-height: 1.1;
}
.lubang-item:hover { transform: scale(1.12); box-shadow: 0 3px 10px rgba(0,0,0,0.2); z-index:5; }
.lubang-kosong  { background: #cbd5e1; border-color: #94a3b8; color: #475569; }
.lubang-ditanam { background: #16a34a; border-color: #15803d; }
.lubang-panen   { background: #2563eb; border-color: #1d4ed8; }
.lubang-rusak   { background: #dc2626; border-color: #b91c1c; }
</style>

{{-- Breadcrumb --}}
<nav style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1.25rem;">
    <a href="{{ route('konven.v2.lahan') }}" style="color:var(--asr-green); text-decoration:none;">Lahan</a>
    <i class="ph ph-caret-right"></i>
    @if($kode->lahan)
    <a href="{{ route('konven.v2.kode.bylahan', $lahan->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $lahan->nama }}</a>
    @elseif($kode->posisi)
    <a href="{{ route('konven.v2.posisi', $lahan->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $lahan->nama }}</a>
    @endif
    <i class="ph ph-caret-right"></i>
    <a href="{{ route('konven.v2.zona', $kode->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $kode->kode }}</a>
    <i class="ph ph-caret-right"></i>
    <a href="{{ route('konven.v2.bedengan', $zona->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $zona->nama }}</a>
    <i class="ph ph-caret-right"></i>
    <span style="color:var(--text-main); font-weight:600;">Bedengan #{{ $bedengan->nomor }}</span>
</nav>

{{-- Header --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem; flex-wrap:wrap; gap:0.75rem;">
    <div>
        <h2 style="margin:0; font-size:1.2rem; font-weight:700; color:var(--text-main);">
            Bedengan #{{ $bedengan->nomor }} {{ $bedengan->nama_display ? '– '.$bedengan->nama_display : '' }}
        </h2>
        <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.2rem;">
            {{ $kode->kode }} › {{ $zona->nama }}
        </p>
    </div>
    @if($lubangStats['kosong'] > 0)
    <button onclick="document.getElementById('modalTanamMassal').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.65rem 1.25rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-leaf"></i> Tanam Massal ({{ $lubangStats['kosong'] }} kosong)
    </button>
    @endif
</div>

{{-- Stats Bar --}}
<div style="display:flex; flex-wrap:wrap; gap:0.75rem; margin-bottom:1.5rem;">
    <div style="background:white; border:1px solid var(--border-color); border-radius:10px; padding:0.75rem 1.25rem; display:flex; align-items:center; gap:0.5rem;">
        <div style="width:12px; height:12px; background:#cbd5e1; border-radius:50%;"></div>
        <span style="font-size:0.85rem; color:var(--text-muted);">Kosong:</span>
        <strong>{{ $lubangStats['kosong'] }}</strong>
    </div>
    <div style="background:white; border:1px solid var(--border-color); border-radius:10px; padding:0.75rem 1.25rem; display:flex; align-items:center; gap:0.5rem;">
        <div style="width:12px; height:12px; background:#16a34a; border-radius:50%;"></div>
        <span style="font-size:0.85rem; color:var(--text-muted);">Ditanam:</span>
        <strong>{{ $lubangStats['ditanam'] }}</strong>
    </div>
    <div style="background:white; border:1px solid var(--border-color); border-radius:10px; padding:0.75rem 1.25rem; display:flex; align-items:center; gap:0.5rem;">
        <div style="width:12px; height:12px; background:#2563eb; border-radius:50%;"></div>
        <span style="font-size:0.85rem; color:var(--text-muted);">Panen:</span>
        <strong>{{ $lubangStats['panen'] }}</strong>
    </div>
    <div style="background:white; border:1px solid var(--border-color); border-radius:10px; padding:0.75rem 1.25rem; display:flex; align-items:center; gap:0.5rem;">
        <div style="width:12px; height:12px; background:#dc2626; border-radius:50%;"></div>
        <span style="font-size:0.85rem; color:var(--text-muted);">Rusak:</span>
        <strong>{{ $lubangStats['rusak'] }}</strong>
    </div>
    <div style="background:white; border:1px solid #94a3b8; border-radius:10px; padding:0.75rem 1.25rem; display:flex; align-items:center; gap:0.5rem;">
        <span style="font-size:0.85rem; color:var(--text-muted);">Total:</span>
        <strong>{{ $lubangStats['total'] }}</strong>
    </div>
</div>

{{-- Lubang Grid --}}
<div class="card" style="padding:1.5rem; border:1px solid var(--border-color); border-radius:12px;">
    <h3 style="margin:0 0 0.5rem; font-size:0.95rem; font-weight:600; color:var(--text-main);">Peta Lubang Tanam</h3>
    <p style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.5rem;">Klik lubang untuk update status</p>
    <div style="display:flex; gap:1rem; flex-wrap:wrap; margin-bottom:0.75rem; font-size:0.72rem;">
        <span><span style="display:inline-block; width:10px; height:10px; background:#cbd5e1; border-radius:50%; margin-right:3px;"></span>Kosong</span>
        <span><span style="display:inline-block; width:10px; height:10px; background:#16a34a; border-radius:50%; margin-right:3px;"></span>Ditanam</span>
        <span><span style="display:inline-block; width:10px; height:10px; background:#2563eb; border-radius:50%; margin-right:3px;"></span>Panen</span>
        <span><span style="display:inline-block; width:10px; height:10px; background:#dc2626; border-radius:50%; margin-right:3px;"></span>Rusak</span>
    </div>

    <div class="lubang-grid">
        @foreach($bedengan->lubangTanams as $lubang)
        <div class="lubang-item lubang-{{ $lubang->status }}"
             onclick="openEditLubang({{ $lubang->id }}, '{{ $lubang->status }}', '{{ addslashes($lubang->plant_name ?? '') }}', '{{ $lubang->planted_at?->format('Y-m-d') ?? '' }}', '{{ $lubang->estimated_harvest_at?->format('Y-m-d') ?? '' }}', '{{ $lubang->harvested_at?->format('Y-m-d') ?? '' }}', '{{ addslashes($lubang->catatan ?? '') }}')"
             title="Lubang #{{ $lubang->nomor_lubang }}{{ $lubang->plant_name ? ' – '.$lubang->plant_name : '' }}">
            <span>{{ $lubang->nomor_lubang }}</span>
            @if($lubang->plant_name)
            <span style="font-size:0.5rem; opacity:0.85; max-width:46px; overflow:hidden; white-space:nowrap; text-overflow:ellipsis;">
                {{ Str::limit($lubang->plant_name, 5, '') }}
            </span>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- Modal Edit Lubang --}}
<div id="modalEditLubang" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:500px; padding:1.5rem; margin:1rem; max-height:90vh; overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Update Lubang Tanam</h3>
            <button onclick="document.getElementById('modalEditLubang').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formEditLubang" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Status *</label>
                <select id="editLubangStatus" name="status" required
                        style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                    <option value="kosong">Kosong</option>
                    <option value="ditanam">Ditanam</option>
                    <option value="panen">Panen</option>
                    <option value="rusak">Rusak</option>
                </select>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Tanaman</label>
                <input type="text" id="editLubangPlant" name="plant_name" placeholder="Contoh: Pakcoy, Selada"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanggal Tanam</label>
                    <input type="date" id="editLubangPlanted" name="planted_at"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Estimasi Panen</label>
                    <input type="date" id="editLubangEstHarvest" name="estimated_harvest_at"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanggal Panen Aktual</label>
                <input type="date" id="editLubangHarvested" name="harvested_at"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Catatan</label>
                <textarea id="editLubangCatatan" name="catatan" rows="2"
                          style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalEditLubang').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Tanam Massal --}}
<div id="modalTanamMassal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;"><i class="ph ph-leaf"></i> Tanam Massal</h3>
            <button onclick="document.getElementById('modalTanamMassal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <p style="font-size:0.85rem; color:var(--text-muted); margin-bottom:1rem;">
            Akan menanam <strong>{{ $lubangStats['kosong'] }}</strong> lubang kosong sekaligus.
        </p>
        <form action="{{ route('konven.v2.tanam.massal', $bedengan->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanaman Utama *</label>
                <input type="text" name="plant_name_1" required placeholder="Contoh: Selada"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanaman Tumpang Sari (Opsional)</label>
                <input type="text" name="plant_name_2" placeholder="Contoh: Daun Bawang"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1.25rem;">
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanggal Tanam *</label>
                    <input type="date" name="planted_at" required value="{{ date('Y-m-d') }}"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Estimasi Panen</label>
                    <input type="date" name="estimated_harvest_at"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTanamMassal').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">
                    <i class="ph ph-leaf"></i> Tanam Semua
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditLubang(id, status, plant, planted, estHarvest, harvested, catatan) {
    document.getElementById('editLubangStatus').value      = status;
    document.getElementById('editLubangPlant').value       = plant;
    document.getElementById('editLubangPlanted').value     = planted;
    document.getElementById('editLubangEstHarvest').value  = estHarvest;
    document.getElementById('editLubangHarvested').value   = harvested;
    document.getElementById('editLubangCatatan').value     = catatan;
    document.getElementById('formEditLubang').action       = '/konvensional/v2/lubang/' + id;
    document.getElementById('modalEditLubang').style.display = 'flex';
}
</script>
@endsection
