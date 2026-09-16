@extends('layouts.app')

@section('content')



<div class="content-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h2 style="font-weight: 700; color: var(--text-main); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-clipboard-text"></i> Laporan Pemeliharaan
        </h2>
        <p style="color: var(--text-muted); margin: 0;">Log aktivitas penyemprotan, pengisian nutrisi, panen, dan kerusakan.</p>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <form action="{{ route('hydroponics.maintenance-logs.destroyAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus SELURUH log Tanam? Data yang dihapus tidak dapat dikembalikan!');">
            @csrf
            <input type="hidden" name="type" value="tanam">
            <button type="submit" style="background: #f59e0b; color: white; border: none; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#d97706'" onmouseout="this.style.background='#f59e0b'">
                <i class="ph ph-trash"></i> Hapus Log Tanam
            </button>
        </form>
        <form action="{{ route('hydroponics.maintenance-logs.destroyAll') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus SELURUH log Panen? Data yang dihapus tidak dapat dikembalikan!');">
            @csrf
            <input type="hidden" name="type" value="panen">
            <button type="submit" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                <i class="ph ph-trash"></i> Hapus Log Panen
            </button>
        </form>
    </div>
</div>

@if(session('success'))
<div style="background: #ecfdf5; color: #065f46; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
    <i class="ph ph-check-circle" style="font-size: 1.25rem;"></i> {{ session('success') }}
</div>
@endif

<!-- Filter Card -->
<div class="card" style="background: var(--bg-card); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm); margin-bottom: 1.5rem;">
    <form action="{{ route('hydroponics.maintenance-logs') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Tipe Aktivitas</label>
            <select name="action_type" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-main); color: var(--text-main); height: 38px;">
                <option value="">Semua Tipe</option>
                <option value="panen" {{ request('action_type') == 'panen' ? 'selected' : '' }}>Panen</option>
                <option value="rusak" {{ request('action_type') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="penyemprotan" {{ request('action_type') == 'penyemprotan' ? 'selected' : '' }}>Penyemprotan GH</option>
                <option value="kuras_tandon" {{ request('action_type') == 'kuras_tandon' ? 'selected' : '' }}>Kuras Tandon</option>
                <option value="isi_ab_mix" {{ request('action_type') == 'isi_ab_mix' ? 'selected' : '' }}>Isi Nutrisi AB Mix</option>
                <option value="pindah_tanam" {{ request('action_type') == 'pindah_tanam' ? 'selected' : '' }}>Pindah Tanam</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-main); color: var(--text-main); height: 38px;">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-main); color: var(--text-main); height: 38px;">
        </div>
        <div>
            <button type="submit" style="background: var(--asr-green); color: white; border: none; padding: 0 1.25rem; border-radius: var(--radius-md); font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: opacity 0.2s; height: 38px;" onmouseover="this.style.opacity='0.9'" onmouseout="this.style.opacity='1'">
                <i class="ph ph-funnel"></i> Filter Data
            </button>
        </div>
    </form>
</div>

    <!-- Data Table Card -->
    <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: var(--radius-lg); padding: 1.5rem;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table datatable" style="width: 100%;">
                    <thead>
                        <tr>
                            <th class="dt-no">NO</th>
                            <th class="ps-4">Tanggal & Jam</th>
                            <th>Pengguna</th>
                            <th>Tipe Aktivitas</th>
                            <th>Lokasi (GH / Rak)</th>
                            <th>Catatan & Detail</th>
                            <th class="pe-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $index => $log)
                        <tr>
                            <td style="text-align: center;">{{ $index + 1 }}</td>
                            <td class="ps-4">
                                <div class="fw-semibold">{{ $log->created_at->translatedFormat('d M Y') }}</div>
                                <div class="small text-muted">{{ $log->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        <i class="ph ph-user small"></i>
                                    </div>
                                    <span class="fw-medium">{{ $log->user->name ?? 'Sistem' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($log->action_type == 'panen')
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="fas fa-leaf me-1"></i> Panen</span>
                                @elseif($log->action_type == 'rusak')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1"><i class="fas fa-times-circle me-1"></i> Rusak</span>
                                @elseif($log->action_type == 'penyemprotan')
                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1"><i class="fas fa-spray-can me-1"></i> Penyemprotan</span>
                                @elseif($log->action_type == 'kuras_tandon')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1"><i class="fas fa-water me-1"></i> Kuras Tandon</span>
                                @elseif($log->action_type == 'isi_ab_mix')
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1"><i class="ph ph-flask me-1"></i> Nutrisi AB Mix</span>
                                @elseif($log->action_type == 'pindah_tanam')
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1"><i class="ph ph-plant me-1"></i> Pindah Tanam</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">{{ str_replace('_', ' ', strtoupper($log->action_type)) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->loggable_type == 'App\Models\Rack')
                                    <div class="fw-semibold">{{ optional(optional($log->loggable)->greenhouse)->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">Rak: {{ optional($log->loggable)->name ?? 'N/A' }}</div>
                                @elseif($log->loggable_type == 'App\Models\Greenhouse')
                                    <div class="fw-semibold">{{ optional($log->loggable)->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">Seluruh GH</div>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium text-dark">{{ $log->notes }}</div>
                                @if($log->details)
                                    @php $details = json_decode($log->details, true); @endphp
                                    @if(is_array($details))
                                        <div class="small text-muted mt-1">
                                            @foreach($details as $key => $val)
                                                <span class="d-inline-block me-2 border rounded px-1">{{ ucfirst($key) }}: {{ is_array($val) ? json_encode($val) : $val }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="pe-4 text-center">
                                                                <button type="button" class="btn btn-sm btn-outline-primary me-1" title="Edit Log" onclick="openEditModal({{ $log->id }}, '{{ addslashes($log->notes) }}')" style="padding: 0.25rem 0.5rem; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="ph ph-pencil-simple" style="font-size: 1.1rem; color: #0ea5e9;"></i>
                                </button>
                                <form action="{{ route('hydroponics.maintenance-logs.destroy', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus log ini? Perhatian: Menghapus log ini tidak akan merubah status lubang di rak secara otomatis (hanya menghapus riwayatnya saja).');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Log">
                                        <i class="ph ph-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div class="modal-overlay" id="editModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background: white; width: 100%; max-width: 500px; border-radius: 12px; padding: 1.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e5e7eb; padding-bottom: 1rem; margin-bottom: 1rem;">
            <h3 style="margin: 0; font-size: 1.25rem; font-weight: 600; color: #111827;">Edit Laporan Pemeliharaan</h3>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer;">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div style="margin-bottom: 1.25rem;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #374151; margin-bottom: 0.5rem;">Catatan & Detail</label>
                <textarea name="notes" id="editNotes" rows="4" class="form-control" style="width: 100%; padding: 0.625rem; border: 1px solid #d1d5db; border-radius: 6px;"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeEditModal()" class="btn btn-light" style="padding: 0.5rem 1rem; border-radius: 6px; border: 1px solid #d1d5db;">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem; border-radius: 6px; background: var(--asr-green); color: white; border: none;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, notes) {
        document.getElementById('editForm').action = '/hydroponics/maintenance-logs/' + id;
        document.getElementById('editNotes').value = notes;
        document.getElementById('editModal').style.display = 'flex';
    }
    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>
@endsection




