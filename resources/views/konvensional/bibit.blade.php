@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
    <div>
        <h1 style="margin: 0; font-size: 1.5rem; color: var(--text-color);">Master Data Bibit Konvensional</h1>
        <p style="margin: 0.25rem 0 0; color: var(--text-muted);">Kelola data tanaman/bibit khusus untuk pertanian di tanah</p>
    </div>
    <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn" style="background: var(--asr-green); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
        <i class="ph ph-plus"></i> Tambah Bibit
    </button>
</div>

@if(session('success'))
<div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
    {{ session('success') }}
</div>
@endif

<div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;">
    <table class="table datatable" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
            <tr>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Nama Bibit</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Estimasi Panen (Hari)</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Deskripsi</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bibits as $bibit)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem; font-weight: 500;">
                    {{ $bibit->nama_bibit }}
                </td>
                <td style="padding: 1rem;">
                    {{ $bibit->estimasi_panen_hari }} Hari
                </td>
                <td style="padding: 1rem; color: var(--text-muted); font-size: 0.9rem;">
                    {{ $bibit->deskripsi ?: '-' }}
                </td>
                <td style="padding: 1rem; text-align: right; display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <form action="{{ route('konvensional.bibit', $bibit->id) }}" method="POST" onsubmit="return confirm('Hapus data bibit ini?');" style="margin: 0; display: inline;">
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
            <h3 style="margin: 0;">Tambah Bibit Baru</h3>
            <button onclick="document.getElementById('modalTambah').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;"><i class="ph ph-x"></i></button>
        </div>
        
        <form action="{{ route('konvensional.bibit') }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Nama Bibit *</label>
                <input type="text" name="nama_bibit" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Contoh: Cabai Merah">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Estimasi Panen (Hari) *</label>
                <input type="number" name="estimasi_panen_hari" min="1" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Contoh: 90">
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Deskripsi</label>
                <textarea name="deskripsi" rows="3" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Opsional..."></textarea>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="padding: 0.75rem 1.5rem; border: 1px solid var(--border-color); background: white; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border: none; background: var(--asr-green); color: white; border-radius: 6px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
