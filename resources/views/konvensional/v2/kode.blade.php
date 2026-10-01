@extends('layouts.app')
@section('title', 'Kode – ' . $posisi->nama)
@section('content')

{{-- Breadcrumb --}}
<nav style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1.25rem;">
    <a href="{{ route('konven.v2.lahan') }}" style="color:var(--asr-green); text-decoration:none;">Lahan</a>
    <i class="ph ph-caret-right"></i>
    <a href="{{ route('konven.v2.posisi', $posisi->lahan_id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $posisi->lahan->nama }}</a>
    <i class="ph ph-caret-right"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $posisi->nama }}</span>
</nav>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.2rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-list-numbers"></i> Kode – {{ $posisi->nama }}
            <span style="background:var(--asr-green-light); color:var(--asr-green); padding:0.15rem 0.6rem; border-radius:50px; font-size:0.75rem; font-weight:700; margin-left:0.5rem;">
                Prefix: {{ $posisi->prefix_kode }}
            </span>
        </h2>
        <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.25rem;">
            Kode urutan dalam posisi ({{ $posisi->prefix_kode }}1, {{ $posisi->prefix_kode }}2, ...)
        </p>
    </div>
    <button onclick="document.getElementById('modalTambahKode').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plus"></i> Tambah Kode
    </button>
</div>

@if(session('success'))
<div style="background:#dcfce7; color:#166534; padding:0.9rem 1rem; border-radius:8px; margin-bottom:1rem; font-weight:500;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:1rem;">
    @forelse($kodes as $kode)
    <div class="card" style="padding:1.25rem; border:1px solid var(--border-color); border-radius:12px; text-align:center;">
        <div style="font-size:2rem; font-weight:800; color:var(--asr-green); margin-bottom:0.25rem;">
            {{ $kode->kode }}
        </div>
        <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:1rem;">
            {{ $kode->zonas_count }} zona
        </div>
        <div style="display:flex; flex-direction:column; gap:0.4rem;">
            <a href="{{ route('konven.v2.zona', $kode->id) }}"
               style="padding:0.45rem; background:var(--asr-green); color:white; border-radius:8px; text-decoration:none; font-size:0.78rem; font-weight:600;">
                <i class="ph ph-grid-four"></i> Kelola Zona
            </a>
            <div style="display:flex; gap:0.4rem;">
                <button onclick="openEditKode({{ $kode->id }}, {{ $kode->nomor_urut }})"
                        style="flex:1; padding:0.4rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:8px; cursor:pointer; font-size:0.75rem;">
                    <i class="ph ph-pencil"></i>
                </button>
                <form method="POST" action="{{ route('konven.v2.kode.destroy', $kode->id) }}"
                      onsubmit="return confirm('Hapus kode {{ $kode->kode }}?')" style="flex:1; margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" style="width:100%; padding:0.4rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; cursor:pointer; color:#dc2626; font-size:0.75rem;">
                        <i class="ph ph-trash"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:3rem; border:1px dashed var(--border-color); border-radius:12px;">
        <i class="ph ph-list-numbers" style="font-size:3rem; color:var(--text-muted); display:block; margin-bottom:0.5rem;"></i>
        <p style="color:var(--text-muted);">Belum ada kode. Tambahkan kode baru.</p>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div id="modalTambahKode" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:420px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Kode Baru</h3>
            <button onclick="document.getElementById('modalTambahKode').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('konven.v2.kode.store', $posisi->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">
                    Nomor Urut * <small style="color:var(--text-muted);">(akan jadi {{ $posisi->prefix_kode }}[nomor])</small>
                </label>
                <input type="number" name="nomor_urut" required min="1" placeholder="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahKode').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditKode" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:420px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Edit Kode</h3>
            <button onclick="document.getElementById('modalEditKode').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formEditKode" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nomor Urut *</label>
                <input type="number" id="editKodeNomor" name="nomor_urut" required min="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalEditKode').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditKode(id, nomor) {
    document.getElementById('editKodeNomor').value  = nomor;
    document.getElementById('formEditKode').action  = '/konvensional/v2/kode/' + id;
    document.getElementById('modalEditKode').style.display = 'flex';
}
</script>
@endsection
