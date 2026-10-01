@extends('layouts.app')
@section('title', 'Bedengan – ' . $zona->nama)
@section('content')

@php
    $kode   = $zona->kode;
    $posisi = $kode->posisi;
    $lahan  = $posisi->lahan;
@endphp

{{-- Breadcrumb --}}
<nav style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1.25rem;">
    <a href="{{ route('konven.v2.lahan') }}" style="color:var(--asr-green); text-decoration:none;">Lahan</a>
    <i class="ph ph-caret-right"></i>
    <a href="{{ route('konven.v2.posisi', $lahan->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $lahan->nama }}</a>
    <i class="ph ph-caret-right"></i>
    <a href="{{ route('konven.v2.kode', $posisi->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $posisi->nama }}</a>
    <i class="ph ph-caret-right"></i>
    <a href="{{ route('konven.v2.zona', $kode->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $kode->kode }}</a>
    <i class="ph ph-caret-right"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $zona->nama }}</span>
</nav>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.2rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-rows"></i> Bedengan – {{ $zona->nama }}
        </h2>
        <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.25rem;">{{ $bedengans->count() }} bedengan</p>
    </div>
    <button onclick="document.getElementById('modalTambahBedengan').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plus"></i> Tambah Bedengan
    </button>
</div>

@if(session('success'))
<div style="background:#dcfce7; color:#166534; padding:0.9rem 1rem; border-radius:8px; margin-bottom:1rem; font-weight:500;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:1rem;">
    @forelse($bedengans as $bedengan)
    @php
        $pct = $bedengan->lubang_count > 0 ? round(($bedengan->terisi_count / $bedengan->lubang_count) * 100) : 0;
    @endphp
    <div class="card" style="padding:1.1rem; border:1px solid var(--border-color); border-radius:12px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
            <div style="font-size:1.6rem; font-weight:800; color:var(--text-main);">
                #{{ $bedengan->nomor }}
            </div>
            <span style="background:#f1f5f9; color:#475569; padding:0.15rem 0.5rem; border-radius:50px; font-size:0.7rem; font-weight:600;">
                {{ $bedengan->lubang_count }} lubang
            </span>
        </div>
        @if($bedengan->nama_display)
        <div style="font-size:0.78rem; color:var(--text-muted); margin-bottom:0.5rem;">{{ $bedengan->nama_display }}</div>
        @endif
        {{-- Progress bar --}}
        <div style="margin-bottom:0.75rem;">
            <div style="display:flex; justify-content:space-between; font-size:0.7rem; color:var(--text-muted); margin-bottom:0.25rem;">
                <span><span style="color:#16a34a; font-weight:600;">{{ $bedengan->terisi_count }}</span> ditanam</span>
                <span>{{ $pct }}%</span>
            </div>
            <div style="background:#e2e8f0; border-radius:4px; height:6px; overflow:hidden;">
                <div style="background:var(--asr-green); height:100%; width:{{ $pct }}%; border-radius:4px;"></div>
            </div>
        </div>
        <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
            <a href="{{ route('konven.v2.bedengan.detail', $bedengan->id) }}"
               style="flex:1; text-align:center; padding:0.45rem; background:var(--asr-green); color:white; border-radius:8px; text-decoration:none; font-size:0.75rem; font-weight:600;">
                Detail
            </a>
            <button onclick="openEditBedengan({{ $bedengan->id }}, {{ $bedengan->nomor }}, '{{ addslashes($bedengan->nama_display ?? '') }}')"
                    style="padding:0.45rem 0.6rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:8px; cursor:pointer; font-size:0.75rem;">
                <i class="ph ph-pencil"></i>
            </button>
            <form method="POST" action="{{ route('konven.v2.bedengan.destroy', $bedengan->id) }}"
                  onsubmit="return confirm('Hapus bedengan #{{ $bedengan->nomor }}?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="padding:0.45rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; cursor:pointer; color:#dc2626; font-size:0.75rem;">
                    <i class="ph ph-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:3rem; border:1px dashed var(--border-color); border-radius:12px;">
        <i class="ph ph-rows" style="font-size:3rem; color:var(--text-muted); display:block; margin-bottom:0.5rem;"></i>
        <p style="color:var(--text-muted);">Belum ada bedengan. Tambahkan bedengan baru.</p>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div id="modalTambahBedengan" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Bedengan Baru</h3>
            <button onclick="document.getElementById('modalTambahBedengan').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('konven.v2.bedengan.store', $zona->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nomor Bedengan *</label>
                <input type="number" name="nomor" required min="1" placeholder="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Display <small style="color:var(--text-muted);">(opsional)</small></label>
                <input type="text" name="nama_display" placeholder="Contoh: Bedengan Utara"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">
                    Jumlah Lubang Tanam * <small style="color:var(--text-muted);">(otomatis dibuat)</small>
                </label>
                <input type="number" name="jumlah_lubang_rencana" required min="0" value="5"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahBedengan').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan & Generate Lubang</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditBedengan" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Edit Bedengan</h3>
            <button onclick="document.getElementById('modalEditBedengan').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formEditBedengan" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nomor Bedengan *</label>
                <input type="number" id="editBedenganNomor" name="nomor" required min="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Display</label>
                <input type="text" id="editBedenganNama" name="nama_display"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalEditBedengan').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditBedengan(id, nomor, nama) {
    document.getElementById('editBedenganNomor').value = nomor;
    document.getElementById('editBedenganNama').value  = nama;
    document.getElementById('formEditBedengan').action = '/konvensional/v2/bedengan/' + id;
    document.getElementById('modalEditBedengan').style.display = 'flex';
}
</script>
@endsection
