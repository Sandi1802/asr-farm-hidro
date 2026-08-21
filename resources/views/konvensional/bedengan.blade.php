@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
    <div>
        <a href="{{ route('konvensional.lahan') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.5rem;"><i class="ph ph-arrow-left"></i> Kembali ke Lahan</a>
        <h1 style="margin: 0; font-size: 1.5rem; color: var(--text-color);">Manajemen Bedengan - {{ $lahan->nama_lahan }}</h1>
        <p style="margin: 0.25rem 0 0; color: var(--text-muted);">Kelola data bedengan dan pengaturan mulsa</p>
    </div>
    <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn" style="background: var(--asr-green); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
        <i class="ph ph-plus"></i> Tambah Bedengan
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
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nama Bedengan</th>
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Penggunaan Mulsa</th>
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Jml. Titik Tanam</th>
                <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lahan->bedengan as $bedengan)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem;">
                    <strong>{{ $bedengan->nama_bedengan }}</strong>
                </td>
                <td style="padding: 1rem;">
                    @if($bedengan->pakai_mulsa)
                        <span style="background: #334155; color: white; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Pakai Mulsa</span>
                    @else
                        <span style="background: #f1f5f9; color: #64748b; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem;">Tanpa Mulsa</span>
                    @endif
                </td>
                <td style="padding: 1rem;">
                    <span style="background: {{ $bedengan->status == 'aktif' ? '#dcfce7' : '#fee2e2' }}; color: {{ $bedengan->status == 'aktif' ? '#166534' : '#991b1b' }}; padding: 0.25rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 600;">
                        {{ ucfirst($bedengan->status) }}
                    </span>
                </td>
                <td style="padding: 1rem;">
                    {{ $bedengan->titik_tanam->count() }} titik
                </td>
                <td style="padding: 1rem; text-align: right; display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <a href="{{ route('konvensional.titik_tanam', $bedengan->id) }}" class="btn" style="background: var(--asr-green-light); color: var(--asr-green); text-decoration: none; padding: 0.4rem 0.75rem; border-radius: 6px; font-weight: 500; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                        <i class="ph ph-dots-nine"></i> Atur Titik Tanam
                    </a>
                    
                    <form action="{{ route('konvensional.bedengan', $bedengan->id) }}" method="POST" onsubmit="return confirm('Hapus bedengan beserta titik tanam di dalamnya?');" style="margin: 0; display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #fee2e2; color: #991b1b; border: none; padding: 0.4rem 0.5rem; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                            <i class="ph ph-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 400px; padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Tambah Bedengan Baru</h3>
            <button onclick="document.getElementById('modalTambah').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;"><i class="ph ph-x"></i></button>
        </div>
        
        <form action="{{ route('konvensional.bedengan', $lahan->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Nama Bedengan *</label>
                <input type="text" name="nama_bedengan" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Contoh: Bedengan 1">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; color: var(--text-color); cursor: pointer;">
                    <input type="checkbox" name="pakai_mulsa" value="1" style="width: 1.25rem; height: 1.25rem;">
                    Bedengan Pakai Mulsa (Plastik Hitam)?
                </label>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="padding: 0.75rem 1.5rem; border: 1px solid var(--border-color); background: white; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border: none; background: var(--asr-green); color: white; border-radius: 6px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
