@extends('layouts.app')
@section('title', 'Manajemen GH - Paprika V2')
@section('content')

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.25rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-house-line"></i> Paprika V2 - Daftar Greenhouse
        </h2>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:0.25rem;">
            Kelola daftar Greenhouse untuk modul Paprika
        </p>
    </div>
    <button onclick="document.getElementById('modalTambahGh').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plus"></i> Tambah GH
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
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:50px;">ID</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Nama GH</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Keterangan</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ghs as $gh)
            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:600; color:var(--text-main);">{{ $gh->id }}</td>
                <td style="padding:1rem; font-weight:600;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <i class="ph ph-house-line" style="color:var(--asr-green);"></i> {{ $gh->nama_gh }}
                    </div>
                </td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ $gh->keterangan ?: '-' }}</td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route('paprika.v2.baris', $gh->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-arrow-right"></i> Masuk Baris</a>
                        <form method="POST" action="{{ route('paprika.v2.gh.destroy', $gh->id) }}" onsubmit="return confirm('Yakin ingin menghapus GH ini beserta isinya?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:3rem; text-align:center; color:var(--text-muted);">Belum ada Greenhouse. Silakan tambahkan GH baru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah GH --}}
<div id="modalTambahGh" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Greenhouse Paprika</h3>
            <button type="button" onclick="document.getElementById('modalTambahGh').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('paprika.v2.gh.store') }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama GH *</label>
                <input type="text" name="nama_gh" required placeholder="Contoh: GH Paprika 1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Keterangan</label>
                <textarea name="keterangan" rows="3" placeholder="Deskripsi opsional..."
                          style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;"></textarea>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahGh').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection