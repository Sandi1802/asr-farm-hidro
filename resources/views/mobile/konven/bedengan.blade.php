@extends('mobile.konven.layout')

@section('title', $bedengan->nama_display ?: 'Bedengan ' . $bedengan->nomor)
@section('back_url', route('m.konven.zona', $bedengan->zona_id))

@section('styles')
<style>
    .action-modal {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 100;
        align-items: flex-end;
    }
    .action-modal.show { display: flex; }
    .modal-content {
        background: white;
        width: 100%;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
        max-height: 90vh;
        overflow-y: auto;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .modal-title { font-size: 1.25rem; font-weight: 700; }
    .close-modal {
        background: #f3f4f6;
        border: none;
        width: 32px; height: 32px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        cursor: pointer;
    }
    .stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
    }
    .stat-value { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 600; }
</style>
@endsection

@section('content')
<div style="margin-bottom: 1.5rem;">
    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.5rem;">
        {{ $bedengan->zona->kode->lahan->nama }} &bull; Kode {{ $bedengan->zona->kode->kode }} &bull; {{ $bedengan->zona->nama }}
    </div>
    <h2 style="font-size: 1.5rem; font-weight: 800;">{{ $bedengan->nama_display ?: 'Bedengan ' . $bedengan->nomor }}</h2>
</div>

<!-- Stats -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-value" style="color: #374151;">{{ $stats['total'] }}</div>
        <div class="stat-label">Total Lubang</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: var(--primary-dark);">{{ $stats['ditanam'] }}</div>
        <div class="stat-label">Ditanam</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: var(--text-muted);">{{ $stats['kosong'] }}</div>
        <div class="stat-label">Kosong</div>
    </div>
    <div class="stat-card">
        <div class="stat-value" style="color: var(--danger);">{{ $stats['rusak'] }}</div>
        <div class="stat-label">Rusak</div>
    </div>
</div>

<!-- Actions -->
<h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Aksi Bedengan</h3>
<div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem; margin-bottom: 2rem;">
    <button type="button" class="btn btn-primary" onclick="openModal('modalTanam')">
        <i class="ph ph-plant"></i> Tanam Massal
    </button>
    <button type="button" class="btn btn-warning" onclick="openModal('modalPanen')">
        <i class="ph ph-scissors"></i> Panen Massal
    </button>
    <div class="grid-2">
        <button type="button" class="btn btn-info" onclick="openModal('modalPerawatan')">
            <i class="ph ph-drop"></i> Perawatan
        </button>
        <button type="button" class="btn btn-danger" onclick="openModal('modalRusak')">
            <i class="ph ph-warning"></i> Kerusakan
        </button>
    </div>
</div>

<!-- Detail Lubang -->
<h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;">Detail Lubang</h3>
<div class="card" style="padding: 0;">
    <ul style="list-style: none;">
        @foreach($lubangs as $lubang)
        <li style="padding: 1rem; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="font-weight: 600; margin-bottom: 0.25rem;">Lubang {{ $lubang->nomor_lubang }}</div>
                @if($lubang->plant_name)
                    <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $lubang->plant_name }}</div>
                @endif
            </div>
            <div>
                <span class="badge badge-{{ $lubang->status }}">{{ ucfirst($lubang->status) }}</span>
            </div>
        </li>
        @endforeach
    </ul>
</div>

<!-- Modal Tanam -->
<div id="modalTanam" class="action-modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Tanam Massal</div>
            <button class="close-modal" onclick="closeModal('modalTanam')"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('m.konven.tanam', $bedengan->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Tanaman Utama (1)</label>
                <input type="text" name="plant_name_1" class="form-control" required placeholder="Contoh: Pakcoy">
            </div>
            <div class="form-group">
                <label class="form-label">Tanaman Tumpang Sari (2)</label>
                <input type="text" name="plant_name_2" class="form-control" placeholder="Contoh: Selada (Opsional)">
            </div>
            <div class="form-group">
                <label class="form-label">Perkiraan Panen</label>
                <input type="date" name="estimated_harvest_at" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Simpan Tanam</button>
        </form>
    </div>
</div>

<!-- Modal Panen -->
<div id="modalPanen" class="action-modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Panen Massal</div>
            <button class="close-modal" onclick="closeModal('modalPanen')"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('m.konven.panen', $bedengan->id) }}" method="POST">
            @csrf
            <p style="margin-bottom: 1rem; color: var(--text-muted);">Tandai semua tanaman yang sedang ditanam di bedengan ini sebagai panen (status lubang menjadi kosong).</p>
            <button type="submit" class="btn btn-warning">Konfirmasi Panen</button>
        </form>
    </div>
</div>

<!-- Modal Perawatan -->
<div id="modalPerawatan" class="action-modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Catat Perawatan</div>
            <button class="close-modal" onclick="closeModal('modalPerawatan')"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('m.konven.perawatan', $bedengan->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Jenis Perawatan</label>
                <select name="jenis" class="form-control" required>
                    <option value="pemupukan">Pemupukan</option>
                    <option value="penyemprotan">Penyemprotan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Bahan / Pupuk</label>
                <input type="text" name="nama_bahan" class="form-control" required placeholder="Contoh: NPK">
            </div>
            <div class="form-group">
                <label class="form-label">Dosis</label>
                <input type="text" name="dosis" class="form-control" placeholder="Contoh: 100ml / tangki">
            </div>
            <div class="form-group">
                <label class="form-label">Keterangan Tambahan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan opsional"></textarea>
            </div>
            <button type="submit" class="btn btn-info" style="margin-top: 1rem;">Simpan Perawatan</button>
        </form>
    </div>
</div>

<!-- Modal Rusak -->
<div id="modalRusak" class="action-modal">
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-title">Lapor Kerusakan</div>
            <button class="close-modal" onclick="closeModal('modalRusak')"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('m.konven.kerusakan', $bedengan->id) }}" method="POST">
            @csrf
            <p style="margin-bottom: 1rem; color: var(--danger);">Perhatian! Ini akan menandai semua lubang yang sedang ditanam menjadi rusak.</p>
            <button type="submit" class="btn btn-danger">Tandai Rusak</button>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('show');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
    }
    
    // Close modal when clicking outside
    document.querySelectorAll('.action-modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if(e.target === this) {
                this.classList.remove('show');
            }
        });
    });
</script>
@endsection
