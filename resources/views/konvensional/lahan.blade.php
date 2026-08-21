@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="margin: 0; font-size: 1.5rem; color: var(--text-color);">Manajemen Lahan (Konvensional)</h1>
        <p style="margin: 0.25rem 0 0; color: var(--text-muted);">Kelola data lahan untuk pertanian konvensional</p>
    </div>
    <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn" style="background: var(--asr-green); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
        <i class="ph ph-plus"></i> Tambah Lahan
    </button>
</div>

@if(session('success'))
<div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
    {{ session('success') }}
</div>
@endif

<div class="card" style="padding: 1.5rem; overflow-x: auto; border: 1px solid var(--border-color);">
    <table class="datatable" style="width: 100%; border-collapse: collapse; min-width: 800px;">
        <thead>
            <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Lahan</th>
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: center;">Jumlah Bedengan</th>
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lahans as $lahan)
            <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                <td style="padding: 1rem; vertical-align: top;">
                    <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                        <div class="icon-box green" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; border-radius: 8px; background: var(--asr-green-light); color: var(--asr-green);">
                            <i class="ph ph-tree" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <a href="{{ route('konvensional.bedengan', $lahan->id) }}" style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                {{ $lahan->nama_lahan }}
                            </a>
                            <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem; margin-bottom: 0;">{{ $lahan->deskripsi ?: 'Tidak ada deskripsi.' }}</p>
                        </div>
                    </div>
                </td>
                
                <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                    <div style="background: #f1f5f9; display: inline-block; padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 700; color: #475569; font-size: 1.1rem;">
                        {{ $lahan->bedengan_count }} <span style="font-size: 0.75rem; font-weight: 600;">Bedengan</span>
                    </div>
                </td>

                <td style="padding: 1rem; vertical-align: middle;">
                    <span class="badge" style="background: {{ $lahan->status == 'aktif' ? '#dcfce7' : '#fee2e2' }}; color: {{ $lahan->status == 'aktif' ? '#166534' : '#991b1b' }}; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                        {{ ucfirst($lahan->status) }}
                    </span>
                </td>

                <td style="padding: 1rem; vertical-align: middle; text-align: right;">
                    <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                        <a href="{{ route('konvensional.bedengan', $lahan->id) }}" class="btn btn-outline" style="padding: 0.35rem 0.6rem; font-size: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-main); text-decoration: none;">
                            Lihat Detail
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($lahans->count() === 0)
    <div style="text-align: center; padding: 3rem; background: white; border-radius: 12px; border: 1px dashed var(--border-color);">
        <i class="ph ph-tree" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <h3 style="margin: 0; color: var(--text-color);">Belum ada data lahan</h3>
        <p style="color: var(--text-muted); margin-top: 0.5rem;">Silakan tambahkan lahan baru untuk memulai.</p>
    </div>
    @endif
</div>

<!-- Modal Tambah -->
<div id="modalTambah" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 500px; padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Tambah Lahan Baru</h3>
            <button onclick="document.getElementById('modalTambah').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;"><i class="ph ph-x"></i></button>
        </div>
        
        <form action="{{ route('konvensional.lahan') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Nama Lahan *</label>
                <input type="text" name="nama_lahan" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Contoh: Lahan A">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Deskripsi</label>
                <textarea name="deskripsi" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Keterangan lahan..."></textarea>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="padding: 0.75rem 1.5rem; border: 1px solid var(--border-color); background: white; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border: none; background: var(--asr-green); color: white; border-radius: 6px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
