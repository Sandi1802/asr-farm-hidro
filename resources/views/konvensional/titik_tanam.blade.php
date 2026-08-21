@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
    <div>
        <a href="{{ route('konvensional.bedengan', $bedengan->lahan_id) }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.875rem; display: flex; align-items: center; gap: 0.25rem; margin-bottom: 0.5rem;"><i class="ph ph-arrow-left"></i> Kembali ke Bedengan</a>
        <h1 style="margin: 0; font-size: 1.5rem; color: var(--text-color);">Titik Tanam - {{ $bedengan->nama_bedengan }}</h1>
        <p style="margin: 0.25rem 0 0; color: var(--text-muted);">Lahan: {{ $bedengan->lahan->nama_lahan }} 
        @if($bedengan->pakai_mulsa)
            <span style="display: inline-block; background: #334155; color: white; padding: 0.1rem 0.4rem; border-radius: 4px; font-size: 0.7rem; margin-left: 0.5rem;">Memakai Mulsa</span>
        @endif
        </p>
    </div>
    
    <div style="display: flex; gap: 0.5rem;">
        <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn" style="background: white; border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-plus"></i> Tambah Lubang
        </button>
    </div>
</div>

@if(session('success'))
<div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
    {{ session('success') }}
</div>
@endif

<div style="background: white; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    
    @if($bedengan->titik_tanam->count() > 0)
    <div style="overflow-x: auto;">
        <table class="datatable" style="width: 100%; border-collapse: collapse; min-width: 600px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nama Titik</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Tanaman (Bibit)</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bedengan->titik_tanam as $titik)
                @php
                    $statusColors = [
                        'kosong' => ['bg' => '#f1f5f9', 'text' => '#64748b'],
                        'persiapan' => ['bg' => '#fef9c3', 'text' => '#854d0e'],
                        'ditanam' => ['bg' => '#dcfce7', 'text' => '#166534'],
                        'siap_panen' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                        'panen' => ['bg' => '#f3e8ff', 'text' => '#6b21a8'],
                        'rusak' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                    ];
                    $color = $statusColors[$titik->status] ?? $statusColors['kosong'];
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; vertical-align: middle; font-weight: 600; color: var(--text-main);">
                        {{ $titik->nama_titik }}
                    </td>
                    <td style="padding: 1rem; vertical-align: middle;">
                        <span style="display: inline-block; background: {{ $color['bg'] }}; color: {{ $color['text'] }}; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                            {{ str_replace('_', ' ', strtoupper($titik->status)) }}
                        </span>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle;">
                        @if($titik->nama_tanaman)
                            <span style="font-size: 0.85rem; color: var(--text-color); font-weight: 500;"><i class="ph ph-plant" style="color: var(--asr-green);"></i> {{ $titik->nama_tanaman }}</span>
                        @else
                            <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">Belum ditanami</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; vertical-align: middle; text-align: right;">
                        <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                            <button onclick="editTitik({{ $titik->toJson() }})" style="padding: 0.35rem 0.6rem; background: white; border: 1px solid var(--border-color); border-radius: 6px; color: var(--asr-green); cursor: pointer; font-size: 0.75rem; font-weight: 500; display: inline-flex; align-items: center; gap: 0.25rem;">
                                <i class="ph ph-pencil-simple"></i> Update
                            </button>
                            <form action="{{ route('konvensional.titik_tanam', $titik->id) }}" method="POST" onsubmit="return confirm('Hapus titik tanam ini?');" style="margin: 0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 0.35rem 0.6rem; background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; border-radius: 6px; cursor: pointer; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.25rem;">
                                    <i class="ph ph-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div style="text-align: center; padding: 4rem 1rem;">
        <i class="ph ph-dots-nine" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <h3 style="margin: 0; color: var(--text-color);">Belum ada titik tanam</h3>
        <p style="color: var(--text-muted); margin-top: 0.5rem; margin-bottom: 1.5rem;">Silakan generate jumlah lubang tanam di bedengan ini secara otomatis.</p>
        <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn" style="background: var(--asr-green); color: white; border: none; padding: 0.75rem 1.5rem; border-radius: 6px; cursor: pointer;">
            <i class="ph ph-plus"></i> Generate Lubang Tanam
        </button>
    </div>
    @endif
</div>

<!-- Modal Tambah Masal -->
<div id="modalTambah" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 400px; padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Tambah Lubang Tanam</h3>
            <button onclick="document.getElementById('modalTambah').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;"><i class="ph ph-x"></i></button>
        </div>
        
        <form action="{{ route('konvensional.titik_tanam', $bedengan->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Berapa titik/lubang yang ingin dibuat?</label>
                <input type="number" name="jumlah_titik" min="1" max="100" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Contoh: 10">
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Titik akan diberi nama secara berurutan otomatis (Titik 1, Titik 2, dst).</p>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="padding: 0.75rem 1.5rem; border: 1px solid var(--border-color); background: white; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border: none; background: var(--asr-green); color: white; border-radius: 6px; cursor: pointer;">Generate</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Update Status -->
<div id="modalUpdate" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 400px; padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Update <span id="updateTitle"></span></h3>
            <button onclick="document.getElementById('modalUpdate').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;"><i class="ph ph-x"></i></button>
        </div>
        
        <form id="formUpdate" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Status</label>
                <select name="status" id="updateStatus" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" onchange="checkStatus(this.value)">
                    <option value="kosong">Kosong</option>
                    <option value="persiapan">Persiapan Lahan</option>
                    <option value="ditanam">Mulai Ditanam</option>
                    <option value="siap_panen">Siap Panen</option>
                    <option value="panen">Sudah Dipanen</option>
                    <option value="rusak">Gagal/Rusak</option>
                </select>
            </div>
            
            <div id="divTanaman" style="margin-bottom: 1.5rem; display: none;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Tanaman (Bibit)</label>
                <select name="nama_tanaman" id="updateTanaman" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    <option value="">-- Pilih Bibit --</option>
                    @foreach($bibits as $b)
                        <option value="{{ $b->nama_bibit }}">{{ $b->nama_bibit }} ({{ $b->estimasi_panen_hari }} hari)</option>
                    @endforeach
                </select>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Pilih bibit yang ada dari Master Data Bibit Konvensional</p>
            </div>
            
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalUpdate').style.display='none'" style="padding: 0.75rem 1.5rem; border: 1px solid var(--border-color); background: white; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border: none; background: var(--asr-green); color: white; border-radius: 6px; cursor: pointer;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editTitik(titik) {
    document.getElementById('updateTitle').innerText = titik.nama_titik;
    document.getElementById('updateStatus').value = titik.status;
    document.getElementById('updateTanaman').value = titik.nama_tanaman || '';
    
    document.getElementById('formUpdate').action = "/konvensional/titik-tanam/" + titik.id;
    
    checkStatus(titik.status);
    document.getElementById('modalUpdate').style.display='flex';
}

function checkStatus(val) {
    if(val === 'ditanam' || val === 'siap_panen') {
        document.getElementById('divTanaman').style.display = 'block';
    } else {
        document.getElementById('divTanaman').style.display = 'none';
    }
}
</script>
@endsection
