@extends('layouts.app')
@section('title', 'Posisi – ' . $lahan->nama)
@section('content')

{{-- Breadcrumb --}}
<nav style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1.25rem;">
    <a href="{{ route('konven.v2.lahan') }}" style="color:var(--asr-green); text-decoration:none;">Lahan</a>
    <i class="ph ph-caret-right"></i> <span style="color:var(--text-main); font-weight:600;">{{ $lahan->nama }}</span>
</nav>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.2rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-compass-tool"></i> Posisi – {{ $lahan->nama }}
        </h2>
        <p style="color:var(--text-muted); font-size:0.8rem; margin-top:0.25rem;">
            Kelola posisi (Atas/Bawah/Kiri/Kanan) dalam lahan
        </p>
    </div>
    <button onclick="document.getElementById('modalTambahPosisi').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plus"></i> Tambah Posisi
    </button>
</div>

@if(session('success'))
<div style="background:#dcfce7; color:#166534; padding:0.9rem 1rem; border-radius:8px; margin-bottom:1rem; font-weight:500;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:1rem;">
    @forelse($posisis as $posisi)
    <div class="card" style="padding:1.25rem; border:1px solid var(--border-color); border-radius:12px; border-top:4px solid var(--asr-green);">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:0.75rem;">
            <div>
                <div style="font-weight:700; font-size:1rem; color:var(--text-main);">{{ $posisi->nama }}</div>
                <div style="margin-top:0.25rem;">
                    <span style="background:var(--asr-green-light); color:var(--asr-green); padding:0.2rem 0.6rem; border-radius:50px; font-size:0.75rem; font-weight:700;">
                        Prefix: {{ $posisi->prefix_kode }}
                    </span>
                    <span style="background:#f1f5f9; color:#475569; padding:0.2rem 0.6rem; border-radius:50px; font-size:0.75rem; margin-left:0.25rem;">
                        {{ $posisi->kodes_count }} kode
                    </span>
                </div>
            </div>
        </div>
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="{{ route('konven.v2.kode', $posisi->id) }}"
               style="flex:1; text-align:center; padding:0.5rem; background:var(--asr-green); color:white; border-radius:8px; text-decoration:none; font-size:0.8rem; font-weight:600;">
                <i class="ph ph-list-numbers"></i> Lihat Kode
            </a>
            <button onclick="openEditPosisi({{ $posisi->id }}, '{{ addslashes($posisi->nama) }}', '{{ $posisi->prefix_kode }}')"
                    style="padding:0.5rem 0.75rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:8px; cursor:pointer; font-size:0.8rem;">
                <i class="ph ph-pencil"></i>
            </button>
            <form method="POST" action="{{ route('konven.v2.posisi.destroy', $posisi->id) }}"
                  onsubmit="return confirm('Hapus posisi {{ $posisi->nama }}?')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="padding:0.5rem 0.75rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; cursor:pointer; color:#dc2626; font-size:0.8rem;">
                    <i class="ph ph-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:3rem; border:1px dashed var(--border-color); border-radius:12px;">
        <i class="ph ph-compass-tool" style="font-size:3rem; color:var(--text-muted); display:block; margin-bottom:0.5rem;"></i>
        <p style="color:var(--text-muted);">Belum ada posisi. Tambahkan posisi baru.</p>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div id="modalTambahPosisi" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Posisi</h3>
            <button onclick="document.getElementById('modalTambahPosisi').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('konven.v2.posisi.store', $lahan->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Posisi *</label>
                <input type="text" name="nama" required placeholder="Contoh: Atas, Bawah, Kiri, Kanan"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Prefix Kode * <small style="color:var(--text-muted);">(1 huruf, misal A untuk Atas)</small></label>
                <input type="text" name="prefix_kode" required maxlength="1" placeholder="A"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box; text-transform:uppercase;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahPosisi').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditPosisi" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Edit Posisi</h3>
            <button onclick="document.getElementById('modalEditPosisi').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formEditPosisi" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Posisi *</label>
                <input type="text" id="editPosisiNama" name="nama" required
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Prefix Kode *</label>
                <input type="text" id="editPosisiPrefix" name="prefix_kode" required maxlength="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box; text-transform:uppercase;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalEditPosisi').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPosisi(id, nama, prefix) {
    document.getElementById('editPosisiNama').value   = nama;
    document.getElementById('editPosisiPrefix').value = prefix;
    document.getElementById('formEditPosisi').action  = '/konvensional/v2/posisi/' + id;
    document.getElementById('modalEditPosisi').style.display = 'flex';
}
</script>
@endsection
