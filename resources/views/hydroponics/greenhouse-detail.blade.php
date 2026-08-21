@extends('layouts.app')
@section('title', 'Detail Green House')
@section('content')
<div class="card">
    <div class="flex-between" style="margin-bottom: 1.5rem; align-items: flex-start;">
        <div>
            <a href="{{ route('hydroponics.greenhouses') }}" style="color: var(--text-muted); text-decoration: none; font-size: 0.9rem;"><i class="ph ph-arrow-left"></i> Kembali ke Daftar GH</a>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main); margin-top: 0.5rem;">{{ $greenhouse->name }}</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">{{ $greenhouse->description ?: 'Tidak ada deskripsi' }}</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <a href="{{ route('hydroponics.greenhouses.print-single-gh-qr', $greenhouse->id) }}" target="_blank" class="btn btn-outline" style="color: var(--asr-green); border-color: var(--asr-green); text-decoration: none;">
                <i class="ph ph-qr-code"></i> Cetak QR GH Ini
            </a>
            @if(Auth::user()->isAgriAdmin())
            @if($greenhouse->racks->count() > 0)
            <a href="{{ route('hydroponics.greenhouses.print-qr', $greenhouse->id) }}" target="_blank" class="btn btn-outline" style="color:#111827; border-color:#d1d5db; text-decoration: none;">
                <i class="ph ph-qr-code"></i> Cetak Semua QR Rak
            </a>
            <button class="btn btn-outline" style="color:#dc2626; border-color:#f87171;" 
                onclick="confirmAction('Hapus Semua Rak?', 'Anda yakin ingin menghapus SEMUA rak beserta baris dan lubang di Greenhouse ini secara permanen?', '{{ route('hydroponics.racks.destroyAll', $greenhouse->id) }}', 'DELETE')">
                <i class="ph ph-trash"></i> Hapus Semua Rak
            </button>
            @endif
            <button class="btn btn-primary" onclick="document.getElementById('addRackModal').style.display='flex'">
                <i class="ph ph-plus"></i> Tambah Rak
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: var(--asr-green-light); color: var(--asr-green-dark); border-radius: 8px; margin-bottom: 1rem; font-weight: 500;">
            <i class="ph ph-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #3b82f6;">
            <div style="background: #eff6ff; color: #3b82f6; padding: 0.75rem; border-radius: 8px; font-size: 1.5rem;">
                <i class="ph ph-circles-four"></i>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.25rem;">Total Lubang</p>
                <h3 style="font-size: 1.5rem; font-weight: 700;">{{ number_format($totalHoles, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #16a34a;">
            <div style="background: #dcfce7; color: #16a34a; padding: 0.75rem; border-radius: 8px; font-size: 1.5rem;">
                <i class="ph ph-basket"></i>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.25rem;">Total Panen</p>
                <h3 style="font-size: 1.5rem; font-weight: 700;">{{ number_format($harvestedHoles, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #eab308;">
            <div style="background: #fef9c3; color: #eab308; padding: 0.75rem; border-radius: 8px; font-size: 1.5rem;">
                <i class="ph ph-leaf"></i>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.25rem;">Siap Panen</p>
                <h3 style="font-size: 1.5rem; font-weight: 700;">{{ number_format($readyToHarvestCount, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; border-left: 4px solid #dc2626;">
            <div style="background: #fee2e2; color: #dc2626; padding: 0.75rem; border-radius: 8px; font-size: 1.5rem;">
                <i class="ph ph-warning-circle"></i>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.25rem;">Tanaman Rusak</p>
                <h3 style="font-size: 1.5rem; font-weight: 700;">{{ number_format($damagedHoles, 0, ',', '.') }}</h3>
            </div>
        </div>

        {{-- Last Sprayed Card --}}
        <div class="card" style="padding: 1rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; border-left: 4px solid #7c3aed;">
            <div style="background: #ede9fe; color: #7c3aed; padding: 0.75rem; border-radius: 8px; font-size: 1.5rem;">
                <i class="ph ph-spray-bottle"></i>
            </div>
            <div style="flex: 1; min-width: 120px;">
                <p style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; margin-bottom: 0.25rem;">Semprot Hama</p>
                @if($greenhouse->last_sprayed_at)
                    @php
                        $daysSinceSpray = $greenhouse->last_sprayed_at->diffInDays(now());
                    @endphp
                    <h3 style="font-size: 1rem; font-weight: 700; color: {{ $daysSinceSpray > 7 ? '#dc2626' : '#7c3aed' }};">
                        {{ $greenhouse->last_sprayed_at->translatedFormat('d M Y') }}
                    </h3>
                    <span style="font-size: 0.78rem; color: {{ $daysSinceSpray > 7 ? '#dc2626' : '#6b7280' }}; font-weight: 600;">
                        {{ $daysSinceSpray === 0 ? 'Hari ini' : $daysSinceSpray . ' hr lalu' }}
                        {{ $daysSinceSpray > 7 ? ' ⚠️ Lama!' : '' }}
                    </span>
                @else
                    <h3 style="font-size: 0.95rem; font-weight: 600; color: #9ca3af;">Belum ada</h3>
                @endif
            </div>
            @if(Auth::user()->isAgriAdmin())
            <form method="POST" action="{{ route('hydroponics.greenhouses.spray', $greenhouse->id) }}" style="margin: 0; flex-grow: 1;">
                @csrf
                <button type="submit" style="width: 100%; background: #7c3aed; border: none; color: white; border-radius: 8px; padding: 0.5rem 0.875rem; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.35rem; white-space: nowrap;">
                    <i class="ph ph-spray-bottle"></i> Catat
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="card" style="padding: 1.5rem; overflow-x: auto; border: 1px solid var(--border-color);">
        <table class="datatable" style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nama Rak</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Nutrisi</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Kuras Terakhir</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Status Lubang</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Keterisian</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($greenhouse->racks as $rack)
                @php
                    $total = $rack->total_holes ?: 1;
                    $pct = round((($rack->planted_holes ?? 0) / $total) * 100);
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; vertical-align: middle;">
                        <a href="{{ route('hydroponics.racks.show', $rack->id) }}" style="text-decoration: none; display: flex; align-items: center; gap: 0.5rem; color: var(--text-main); font-weight: 700;">
                            <i class="ph ph-squares-four" style="font-size: 1.25rem; color: var(--asr-green);"></i>
                            {{ $rack->name }}
                        </a>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle;">
                        <div style="display: flex; gap: 0.5rem; font-size: 0.85rem;">
                            <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;"><strong style="color: #475569;">PPM:</strong> <span style="color: #15803d; font-weight: 700;">{{ $rack->ppm_level ?? '-' }}</span></span>
                            <span style="background: #f1f5f9; padding: 0.2rem 0.5rem; border-radius: 4px;"><strong style="color: #475569;">pH:</strong> <span style="color: #1d4ed8; font-weight: 700;">{{ $rack->ph_level ?? '-' }}</span></span>
                        </div>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle; font-size: 0.85rem; color: var(--text-main); font-weight: 500;">
                        @if($rack->last_drained_at)
                            {{ \Carbon\Carbon::parse($rack->last_drained_at)->translatedFormat('d M Y') }}
                        @else
                            <span style="color: #9ca3af;">Belum pernah</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; vertical-align: middle;">
                        <div style="display: flex; gap: 0.4rem; flex-wrap: wrap;">
                            <span style="background: #dcfce7; color: #16a34a; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;" title="Ditanam"><i class="ph ph-plant"></i> {{ number_format($rack->planted_holes ?? 0, 0, ',', '.') }}</span>
                            <span style="background: #f1f5f9; color: #475569; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;" title="Kosong"><i class="ph ph-circle"></i> {{ number_format($rack->empty_holes ?? 0, 0, ',', '.') }}</span>
                            @if(($rack->harvested_holes ?? 0) > 0)
                            <span style="background: #dbeafe; color: #2563eb; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;" title="Panen"><i class="ph ph-basket"></i> {{ $rack->harvested_holes }}</span>
                            @endif
                            @if(($rack->damaged_holes ?? 0) > 0)
                            <span style="background: #fee2e2; color: #dc2626; padding: 0.15rem 0.4rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;" title="Rusak"><i class="ph ph-warning"></i> {{ $rack->damaged_holes }}</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle;">
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 60px; height: 6px; background: #e2e8f0; border-radius: 10px; overflow: hidden;">
                                <div style="width: {{ $pct }}%; height: 100%; background: #16a34a; border-radius: 10px;"></div>
                            </div>
                            <span style="font-size: 0.8rem; font-weight: 700; color: #64748b;">{{ $pct }}%</span>
                        </div>
                    </td>
                    <td style="padding: 1rem; vertical-align: middle; text-align: right;">
                        <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                            <a href="{{ route('hydroponics.racks.show', $rack->id) }}" class="btn btn-outline" style="padding: 0.35rem 0.6rem; font-size: 0.75rem;">
                                Detail
                            </a>
                            @if(Auth::user()->isAgriAdmin())
                            <button type="button" 
                                    onclick="confirmAction('Kuras Air Rak?', 'Catat pengurasan air untuk {{ addslashes($rack->name) }}?', '{{ route('hydroponics.racks.drain', $rack->id) }}', 'POST')"
                                    style="padding: 0.35rem 0.6rem; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 6px; font-size: 0.75rem; font-weight: 600; cursor: pointer;" title="Catat Kuras">
                                <i class="ph ph-drop"></i>
                            </button>
                            <a href="{{ route('hydroponics.racks.print-qr', $rack->id) }}" target="_blank"
                                style="padding: 0.35rem 0.5rem; background: var(--asr-green-light); color: var(--asr-green-dark); border: 1px solid var(--asr-green); border-radius: 6px; cursor: pointer;" title="Cetak QR">
                                <i class="ph ph-qr-code" style="font-size: 0.9rem;"></i>
                            </a>
                            <button onclick="openEditRackModal({{ $rack->id }}, '{{ addslashes($rack->name) }}', '{{ $rack->status }}', {{ $rack->rows->count() }})"
                                style="padding: 0.35rem 0.5rem; background: white; color: #374151; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;" title="Edit Rak">
                                <i class="ph ph-pencil-simple" style="font-size: 0.9rem;"></i>
                            </button>
                            <button type="button" 
                                    onclick="confirmAction('Hapus Rak?', 'Hapus {{ addslashes($rack->name) }} beserta baris & lubang di dalamnya?', '{{ route('hydroponics.racks.destroy', $rack->id) }}', 'DELETE')"
                                    style="padding: 0.35rem 0.5rem; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer;" title="Hapus Rak">
                                <i class="ph ph-trash" style="font-size: 0.9rem;"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($greenhouse->racks->count() === 0)
        <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.9rem;">
            Belum ada rak di Green House ini.
        </div>
        @endif
    </div>
</div>

<!-- Maintenance Logs History -->
@php
    $ghMaintenanceLogs = \App\Models\MaintenanceLog::with('user')
        ->where('loggable_type', \App\Models\Greenhouse::class)
        ->where('loggable_id', $greenhouse->id)
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
@endphp
<div class="card bg-white shadow-sm border-0 mb-4 rounded-4 mt-4" style="background: white; border-radius: 12px; border: 1px solid var(--border-color); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
    <div class="card-header bg-white border-0 py-3" style="padding: 1.5rem 1.5rem 0 1.5rem;">
        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 1rem;"><i class="ph ph-journal-text" style="margin-right: 0.5rem;"></i>Riwayat Perawatan & Checklist (10 Terakhir)</h3>
    </div>
    <div class="card-body p-0">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
                    <tr>
                        <th style="padding: 1rem 1.5rem; text-align: left; font-weight: 600;">Tanggal</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Petugas</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Pekerjaan (Scanner)</th>
                        <th style="padding: 1rem; text-align: left; font-weight: 600;">Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ghMaintenanceLogs as $log)
                    @php
                        $details = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                        $jobs = [];
                        if(is_array($details)) {
                            if($details['swept'] ?? false) $jobs[] = 'Sapu Lantai';
                            if($details['sprayed'] ?? false) $jobs[] = 'Semprot Hama';
                        }
                    @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 1rem 1.5rem; color: #64748b;">{{ $log->created_at->format('d M Y, H:i') }}</td>
                        <td style="padding: 1rem; font-weight: 600; color: #334155;">{{ $log->user->name ?? 'Unknown' }}</td>
                        <td style="padding: 1rem;">
                            @if(count($jobs) > 0)
                                <ul style="margin: 0; padding-left: 1rem; color: #64748b;">
                                    @foreach($jobs as $job)
                                        <li>{{ $job }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td style="padding: 1rem; color: #64748b;">{{ $log->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding: 2rem; text-align: center; color: #94a3b8;">Belum ada riwayat perawatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(Auth::user()->isAgriAdmin())
<!-- Modal Tambah Rak -->
<div id="addRackModal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 999;">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
        <div class="flex-between" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Tambah Rak Baru</h3>
            <i class="ph ph-x" style="cursor: pointer; font-size: 1.25rem; color: #6b7280;" onclick="document.getElementById('addRackModal').style.display='none'"></i>
        </div>
        <div style="padding: 1rem; background: var(--bg-color); border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; color: var(--text-muted);">
            <i class="ph ph-info"></i> Tentukan jumlah baris dan lubang per baris untuk rak yang akan dibuat. Sistem akan meng-generate struktur rak secara otomatis.
        </div>
        <form action="{{ route('hydroponics.racks.store', $greenhouse->id) }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Jumlah Rak yang Akan Dibuat</label>
                <input type="number" name="jumlah_rak" min="1" value="1" class="form-control" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                <small style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem; display: block;">Nama rak akan dilanjutkan otomatis dari urutan rak yang sudah ada (misal: Rak 11, Rak 12)</small>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Jumlah Baris</label>
                    <input type="number" name="num_rows" min="1" value="8" class="form-control" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
                <div class="form-group">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Lubang per Baris</label>
                    <input type="number" name="num_holes" min="1" value="51" class="form-control" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                </div>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addRackModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Generate Rak</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Rak -->
<div id="editRackModal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 999;">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
        <div class="flex-between" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Edit Rak</h3>
            <i class="ph ph-x" style="cursor: pointer; font-size: 1.25rem; color: #6b7280;" onclick="document.getElementById('editRackModal').style.display='none'"></i>
        </div>
        <form id="editRackForm" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Nama Rak</label>
                <input type="text" name="name" id="editRackName" class="form-control" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Jumlah Baris</label>
                <input type="number" name="num_rows" id="editRackNumRows" class="form-control" min="1" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Status</label>
                <select name="status" id="editRackStatus" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                    <option value="aktif">Aktif</option>
                    <option value="non-aktif">Non-Aktif</option>
                </select>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editRackModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditRackModal(id, name, status, numRows) {
    document.getElementById('editRackForm').action = '/hydroponics/racks/' + id + '/update';
    document.getElementById('editRackName').value = name;
    document.getElementById('editRackStatus').value = status;
    document.getElementById('editRackNumRows').value = numRows;
    document.getElementById('editRackModal').style.display = 'flex';
}
</script>
@endif
@endsection
