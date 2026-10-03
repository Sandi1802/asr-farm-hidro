@extends('layouts.app')
@section('title', 'Manajemen Baris - Paprika V2')
@section('content')

<div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.75rem; display:flex; align-items:center; gap:0.4rem;">
    <a href="{{ route('paprika.v2.index') }}" style="color:var(--text-muted); text-decoration:none;"><i class="ph ph-house-line"></i> Paprika V2</a>
    <i class="ph ph-caret-right"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $gh->nama_gh }}</span>
</div>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.25rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-rows"></i> Daftar Baris Paprika
        </h2>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:0.25rem;">
            Kelola baris dan kapasitas pot di dalam GH {{ $gh->nama_gh }}
        </p>
    </div>
    <button onclick="document.getElementById('modalTambahBaris').style.display='flex'"
            style="background:var(--asr-green); color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plus"></i> Tambah Baris
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
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Nama Baris</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Kapasitas Pot</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($baris as $b)
            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:600; color:var(--text-main);">{{ $b->id }}</td>
                <td style="padding:1rem; font-weight:600;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <i class="ph ph-rows" style="color:var(--asr-green);"></i> {{ $b->nama_baris }}
                    </div>
                </td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">
                    <span style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:4px; font-weight:600;">{{ $b->pots_count }} Pot</span>
                </td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route('paprika.v2.pot', $b->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-arrow-right"></i> Masuk Pot</a>
                        <form method="POST" action="{{ route('paprika.v2.baris.destroy', $b->id) }}" onsubmit="return confirm('Yakin ingin menghapus Baris ini beserta Pot-nya?')" style="margin:0;">
                            @csrf @method('DELETE')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="padding:3rem; text-align:center; color:var(--text-muted);">Belum ada baris. Silakan tambahkan baris baru.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Modal Tambah Baris --}}
<div id="modalTambahBaris" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tambah Baris Paprika</h3>
            <button type="button" onclick="document.getElementById('modalTambahBaris').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('paprika.v2.baris.store', $gh->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Baris *</label>
                <input type="text" name="nama_baris" required placeholder="Contoh: Baris A"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Pot Otomatis *</label>
                <input type="number" name="jumlah_pot" required min="1" max="500" placeholder="Berapa pot di dalam baris ini?"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                <small style="color:var(--text-muted); display:block; margin-top:4px;">Sistem akan otomatis me-generate jumlah pot di atas ke dalam Baris ini.</small>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahBaris').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection