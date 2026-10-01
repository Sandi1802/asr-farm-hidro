@extends('layouts.app')
@section('title', 'Manajemen Lahan – Konvensional V2')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.25rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-plant"></i> Lahan Konvensional V2
        </h2>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:0.25rem;">
            Kelola daftar lahan untuk pertanian konvensional
        </p>
    </div>
    <button onclick="document.getElementById('modalTambahLahan').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plus"></i> Tambah Lahan
    </button>
</div>

@if(session('success'))
<div style="background:#dcfce7; color:#166534; padding:0.9rem 1rem; border-radius:8px; margin-bottom:1rem; font-weight:500;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1rem;">
    @forelse($lahans as $lahan)
    <div class="card" style="padding:1.25rem; border:1px solid var(--border-color); border-radius:12px;">
        <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:1rem;">
            <div style="width:42px; height:42px; background:var(--asr-green-light); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <i class="ph ph-plant" style="font-size:1.3rem; color:var(--asr-green);"></i>
            </div>
            <div>
                <div style="font-weight:700; font-size:1rem; color:var(--text-main);">{{ $lahan->nama }}</div>
                <div style="font-size:0.75rem; color:var(--text-muted);">{{ $lahan->posisis_count }} posisi</div>
            </div>
        </div>
        @if($lahan->catatan)
        <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1rem;">{{ $lahan->catatan }}</p>
        @endif
        <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
            <a href="{{ route('konven.v2.posisi', $lahan->id) }}"
               style="flex:1; text-align:center; padding:0.5rem; background:var(--asr-green); color:white; border-radius:8px; text-decoration:none; font-size:0.8rem; font-weight:600;">
                <i class="ph ph-arrow-right"></i> Kelola
            </a>
            <button onclick="openEditLahan({{ $lahan->id }}, '{{ addslashes($lahan->nama) }}', '{{ addslashes($lahan->catatan ?? '') }}')"
                    style="padding:0.5rem 0.75rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:8px; cursor:pointer; font-size:0.8rem;">
                <i class="ph ph-pencil"></i>
            </button>
            <form method="POST" action="{{ route('konven.v2.lahan.destroy', $lahan->id) }}"
                  onsubmit="return confirm('Hapus lahan {{ $lahan->nama }}? Semua data di dalamnya akan terhapus.')" style="margin:0;">
                @csrf @method('DELETE')
                <button type="submit" style="padding:0.5rem 0.75rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; cursor:pointer; color:#dc2626; font-size:0.8rem;">
                    <i class="ph ph-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="grid-column:1/-1; text-align:center; padding:3rem; border:1px dashed var(--border-color); border-radius:12px;">
        <i class="ph ph-plant" style="font-size:3rem; color:var(--text-muted); display:block; margin-bottom:0.5rem;"></i>
        <p style="color:var(--text-muted);">Belum ada lahan. Tambahkan lahan baru untuk memulai.</p>
    </div>
    @endforelse
</div>

{{-- Modal Tambah --}}
<div id="modalTambahLahan" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Lahan Baru</h3>
            <button onclick="document.getElementById('modalTambahLahan').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('konven.v2.lahan.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Lahan *</label>
                <input type="text" name="nama" required placeholder="Contoh: Lahan 1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Catatan</label>
                <textarea name="catatan" rows="3" placeholder="Deskripsi opsional..."
                          style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahLahan').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit --}}
<div id="modalEditLahan" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Edit Lahan</h3>
            <button onclick="document.getElementById('modalEditLahan').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formEditLahan" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Lahan *</label>
                <input type="text" id="editLahanNama" name="nama" required
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Catatan</label>
                <textarea id="editLahanCatatan" name="catatan" rows="3"
                          style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalEditLahan').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditLahan(id, nama, catatan) {
    document.getElementById('editLahanNama').value    = nama;
    document.getElementById('editLahanCatatan').value = catatan;
    document.getElementById('formEditLahan').action   = '/konvensional/v2/lahan/' + id;
    document.getElementById('modalEditLahan').style.display = 'flex';
}
</script>
@endsection
