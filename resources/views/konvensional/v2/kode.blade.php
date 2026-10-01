@extends('layouts.app')
@section('title', 'Kode – ' . $lahan->nama)
@section('content')

{{-- Breadcrumb --}}
<nav style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1.25rem;">
    <a href="{{ route('konven.v2.lahan') }}" style="color:var(--asr-green); text-decoration:none;">Lahan</a>
    <i class="ph ph-caret-right"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $lahan->nama }}</span>
</nav>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.2rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-list-numbers"></i> Kode – {{ $lahan->nama }}
        </h2>
        <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.25rem;">
            Kelola kode lahan (A1, B1, C2, dst.) langsung di bawah lahan
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

<div style="background:white; border-radius:12px; border:1px solid var(--border-color); overflow-x:auto;">
    <table class="table datatable" style="width:100%; border-collapse:collapse; text-align:left; min-width:600px;">
        <thead style="background:#f8fafc; border-bottom:1px solid var(--border-color);">
            <tr>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Kode</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Posisi</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Jumlah Zona</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kodes as $kode)
            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:700; font-size:1.1rem; color:var(--asr-green);">{{ $kode->kode }}</td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ $kode->label_posisi ?? '-' }}</td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ $kode->zonas_count }} zona</td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route('konven.v2.zona', $kode->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-squares-four"></i> Kelola Zona</a>
                        <button onclick="openEditKode({{ $kode->id }}, '{{ preg_replace('/[0-9]/','',$kode->kode) }}', '{{ $kode->nomor_urut }}', '{{ addslashes($kode->label_posisi ?? '') }}')" style="padding:0.4rem 0.6rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:6px; cursor:pointer;"><i class="ph ph-pencil"></i></button>
                        <form method="POST" action="{{ route('konven.v2.kode.destroy', $kode->id) }}" onsubmit="return confirm('Hapus kode ini?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:3rem; text-align:center; color:var(--text-muted);">Belum ada kode lahan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah --}}
<div id="modalTambahKode" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:440px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Kode Baru</h3>
            <button onclick="document.getElementById('modalTambahKode').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('konven.v2.kode.store.direct', $lahan->id) }}" method="POST">
            @csrf
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1rem;">
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">
                        Prefix * <small style="color:var(--text-muted);">(1 huruf, mis. A)</small>
                    </label>
                    <input type="text" name="prefix_kode" required maxlength="1" placeholder="A"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box; text-transform:uppercase;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">
                        Nomor Urut *
                    </label>
                    <input type="number" name="nomor_urut" required min="1" placeholder="1"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">
                    Label Posisi <small style="color:var(--text-muted);">(opsional, mis. Atas, Bawah)</small>
                </label>
                <input type="text" name="label_posisi" maxlength="50" placeholder="Atas"
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
    <div style="background:white; border-radius:12px; width:100%; max-width:440px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Edit Kode</h3>
            <button onclick="document.getElementById('modalEditKode').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formEditKode" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nomor Urut *</label>
                <input type="number" id="editKodeNomor" name="nomor_urut" required min="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Label Posisi</label>
                <input type="text" id="editKodeLabelPosisi" name="label_posisi" maxlength="50" placeholder="Atas"
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
function openEditKode(id, nomor, labelPosisi) {
    document.getElementById('editKodeNomor').value         = nomor;
    document.getElementById('editKodeLabelPosisi').value   = labelPosisi;
    document.getElementById('formEditKode').action         = '/konvensional/v2/kode/' + id;
    document.getElementById('modalEditKode').style.display = 'flex';
}
</script>
@endsection
