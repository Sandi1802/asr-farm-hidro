@extends('layouts.app')
@section('title', 'Zona – ' . $kode->kode)
@section('content')

{{-- Breadcrumb --}}
<nav style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1.25rem;">
    <a href="{{ route('konven.v2.lahan') }}" style="color:var(--asr-green); text-decoration:none;">Lahan</a>
    <i class="ph ph-caret-right"></i>
    @if($kode->lahan)
    <a href="{{ route('konven.v2.kode.bylahan', $kode->lahan->id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $kode->lahan->nama }}</a>
    @elseif($kode->posisi)
    <a href="{{ route('konven.v2.posisi', $kode->posisi->lahan_id) }}" style="color:var(--asr-green); text-decoration:none;">{{ $kode->posisi->lahan->nama }}</a>
    @endif
    <i class="ph ph-caret-right"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $kode->kode }}</span>
</nav>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.2rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-grid-four"></i> Zona – Kode {{ $kode->kode }}
        </h2>
        <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.25rem;">Kelola zona dalam kode {{ $kode->kode }}</p>
    </div>
    <button onclick="document.getElementById('modalTambahZona').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plus"></i> Tambah Zona
    </button>
</div>

@if(session('success'))
<div style="background:#dcfce7; color:#166534; padding:0.9rem 1rem; border-radius:8px; margin-bottom:1rem; font-weight:500;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="background:white; border-radius:12px; border:1px solid var(--border-color); overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; text-align:left; min-width:600px;">
        <thead style="background:#f8fafc; border-bottom:1px solid var(--border-color);">
            <tr>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Nama Zona</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Jumlah Bedengan</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($zonas as $zona)
            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:600; color:var(--text-main);">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <i class="ph ph-squares-four" style="color:var(--asr-green);"></i> {{ $zona->nama }}
                    </div>
                </td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ $zona->bedengans_count }} bedengan</td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route('konven.v2.bedengan', $zona->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-rows"></i> Kelola Bedengan</a>
                        <button onclick="openEditZona({{ $zona->id }}, '{{ addslashes($zona->nama) }}')" style="padding:0.4rem 0.6rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:6px; cursor:pointer;"><i class="ph ph-pencil"></i></button>
                        <form method="POST" action="{{ route('konven.v2.zona.destroy', $zona->id) }}" onsubmit="return confirm('Hapus zona ini?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" style="padding:3rem; text-align:center; color:var(--text-muted);">Belum ada zona.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah --}}
<div id="modalTambahZona" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:420px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Zona Baru</h3>
            <button onclick="document.getElementById('modalTambahZona').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('konven.v2.zona.store', $kode->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Zona yang Dibuat *</label>
                <input type="number" name="jumlah_zona" required min="1" max="500" value="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Prefix Nama Zona *</label>
                <input type="text" name="prefix_nama" required value="Zona" placeholder="Contoh: Zona"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                <small style="color:var(--text-muted); font-size:0.75rem;">Sistem akan menambahkan angka di belakangnya (Misal: Zona 1, Zona 2).</small>
            </div>
            <div style="margin-bottom:1rem; padding-top:1rem; border-top:1px dashed var(--border-color);">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Bedengan per Zona</label>
                <input type="number" name="jumlah_bedengan" required min="0" max="500" value="5"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Lubang per Bedengan</label>
                <input type="number" name="jumlah_lubang" required min="0" max="2000" value="50"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahZona').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan & Generate</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditZona" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:420px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Edit Zona</h3>
            <button onclick="document.getElementById('modalEditZona').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formEditZona" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Zona *</label>
                <input type="text" id="editZonaNama" name="nama" required
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalEditZona').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditZona(id, nama) {
    document.getElementById('editZonaNama').value  = nama;
    document.getElementById('formEditZona').action = '/konvensional/v2/zona/' + id;
    document.getElementById('modalEditZona').style.display = 'flex';
}
</script>
@endsection
