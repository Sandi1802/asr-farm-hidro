@extends('layouts.app')

@section('title', 'Master Data — Vessels')

@section('content')
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.25rem;">Data Kapal (Vessels)</h2>
            <p style="font-size: 0.85rem; color: var(--text-muted);">Kelola data kapal yang terlibat dalam proyek refurbishment</p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button class="btn btn-outline" type="button"><i class="ph ph-export"></i> Export</button>
            <button class="btn btn-primary" type="button" onclick="document.getElementById('addModal').classList.add('active')"><i class="ph ph-plus"></i> Tambah Kapal</button>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: var(--asr-green); color: white; border-radius: var(--radius-md); margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Vessel Cards Grid -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-bottom: 2rem;">
        @forelse($vessels as $vessel)
        <div class="card" style="border-top: 3px solid {{ $vessel->status == 'Active' ? '#059669' : ($vessel->status == 'Maintenance' ? 'var(--asr-green)' : '#2563EB') }}; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 1rem; right: 1rem; display: flex; gap: 0.5rem; align-items: center;">
                <span class="badge {{ $vessel->status == 'Active' ? 'badge-success' : ($vessel->status == 'Maintenance' ? 'badge-danger' : 'badge-warning') }}">{{ $vessel->status }}</span>
                <form action="{{ route('master-data.vessels.delete', $vessel->id) }}" method="POST" onsubmit="return confirm('Hapus kapal ini?');" style="margin: 0;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background: none; border: none; color: var(--danger); cursor: pointer;"><i class="ph ph-trash" style="font-size: 1.2rem;"></i></button>
                </form>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div class="icon-box {{ $vessel->status == 'Active' ? 'green' : ($vessel->status == 'Maintenance' ? 'red' : 'blue') }}" style="width: 44px; height: 44px;"><i class="ph-fill ph-boat" style="font-size: 1.3rem;"></i></div>
                <div>
                    <div style="font-weight: 600; font-size: 1rem;">{{ $vessel->name }}</div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">Hull No. {{ $vessel->hull_no }}</div>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">
                <div><i class="ph ph-map-pin" style="margin-right: 0.25rem;"></i> {{ $vessel->location }}</div>
                <div><i class="ph ph-ruler" style="margin-right: 0.25rem;"></i> {{ $vessel->length }}m</div>
                <div><i class="ph ph-calendar" style="margin-right: 0.25rem;"></i> {{ $vessel->year }}</div>
                <div><i class="ph ph-tag" style="margin-right: 0.25rem;"></i> {{ $vessel->type }}</div>
            </div>
        </div>
        @empty
        <div style="grid-column: span 3; padding: 3rem; text-align: center; color: var(--text-muted); background: var(--card-bg); border-radius: var(--radius-lg);">
            <i class="ph ph-boat" style="font-size: 3rem; margin-bottom: 1rem; color: var(--border-color);"></i>
            <p>Belum ada data kapal. Silakan tambah data baru.</p>
        </div>
        @endforelse
    </div>

    <!-- Add Modal -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Tambah Data Kapal</h3>
                <i class="ph ph-x close-modal" onclick="document.getElementById('addModal').classList.remove('active')"></i>
            </div>
            <form action="{{ route('master-data.vessels.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Nama Kapal</label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: KRI Hiu-Todak">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Hull No.</label>
                        <input type="text" name="hull_no" class="form-control" required placeholder="401">
                    </div>
                    <div class="form-group">
                        <label>Panjang (meter)</label>
                        <input type="number" name="length" class="form-control" required placeholder="57">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Tahun Pembuatan</label>
                        <input type="number" name="year" class="form-control" required placeholder="1990">
                    </div>
                    <div class="form-group">
                        <label>Tipe/Kelas</label>
                        <input type="text" name="type" class="form-control" required placeholder="FPB-57">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Lokasi Saat Ini</label>
                        <input type="text" name="location" class="form-control" required placeholder="Surabaya">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="Active">Active</option>
                            <option value="Dry Dock">Dry Dock</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                </div>
                <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-outline" onclick="document.getElementById('addModal').classList.remove('active')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
