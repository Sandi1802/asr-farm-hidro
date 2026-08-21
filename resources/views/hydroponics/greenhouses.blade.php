@extends('layouts.app')
@section('title', 'Manajemen Green House')
@section('content')
<div class="card">
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-main);">Daftar Green House</h2>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.25rem;">Kelola area & unit bangunan Green House</p>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('hydroponics.greenhouses.print-all-gh-qr') }}" target="_blank" class="btn btn-outline" style="border-color: var(--asr-green); color: var(--asr-green); text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.5rem 1rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem;">
                <i class="ph ph-qr-code"></i> Cetak QR Semua GH
            </a>
            @if(Auth::user()->isAgriAdmin())
            <button class="btn btn-primary" onclick="document.getElementById('addGHModal').style.display='flex'">
                <i class="ph ph-plus"></i> Tambah GH
            </button>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 1rem; background: var(--asr-green-light); color: var(--asr-green-dark); border-radius: 8px; margin-bottom: 1rem; font-weight: 500;">
            <i class="ph ph-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card" style="padding: 1.5rem; overflow-x: auto; border: 1px solid var(--border-color);">
        <table class="datatable" style="width: 100%; border-collapse: collapse; min-width: 1000px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Green House</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: center;">Rak</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase;">Keterisian</th>
                    <th style="padding: 1rem; font-weight: 700; color: #475569; font-size: 0.85rem; text-transform: uppercase; text-align: center;">Kosong</th>
                    <th style="padding: 1rem; font-weight: 700; color: #16a34a; font-size: 0.85rem; text-transform: uppercase; text-align: center;">Ditanam</th>
                    <th style="padding: 1rem; font-weight: 700; color: #ea580c; font-size: 0.85rem; text-transform: uppercase; text-align: center;">Siap Panen</th>
                    <th style="padding: 1rem; font-weight: 700; color: #2563eb; font-size: 0.85rem; text-transform: uppercase; text-align: center;">Panen</th>
                    <th style="padding: 1rem; font-weight: 700; color: #dc2626; font-size: 0.85rem; text-transform: uppercase; text-align: center;">Rusak</th>
                    <th style="padding: 1rem; font-weight: 700; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($greenhouses as $gh)
                @php
                    $thirtyDaysAgo = now()->subDays(30);
                    $allHoles = $gh->racks->flatMap->rows->flatMap->holes;
                    $cntKosong  = $allHoles->where('status', 'kosong')->count();
                    $cntDitanamTotal = $allHoles->where('status', 'ditanam')->count();
                    $cntReady   = $allHoles->where('status', 'ditanam')->filter(function($h) use ($plantTypeMap, $defaultDays) {
                        if (!$h->planted_at) return false;
                        $days = isset($plantTypeMap[$h->plant_name]) ? $plantTypeMap[$h->plant_name] : $defaultDays;
                        return \Carbon\Carbon::parse($h->planted_at)->addDays($days)->lte(now());
                    })->count();
                    $cntDitanam = max(0, $cntDitanamTotal - $cntReady);
                    $cntPanen   = $allHoles->where('status', 'panen')->count();
                    $cntRusak   = $allHoles->where('status', 'rusak')->count();
                    
                    $total = $allHoles->count() ?: 1;
                    $pct = round((($cntDitanamTotal) / $total) * 100);
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; vertical-align: middle;">
                        <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                            <div class="icon-box green" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="ph ph-house-line" style="font-size: 1.1rem;"></i>
                            </div>
                            <div>
                                <a href="{{ route('hydroponics.greenhouses.show', $gh->id) }}" style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                                    {{ $gh->name }}
                                </a>
                                <div style="margin-top: 0.25rem;">
                                    <span class="badge {{ $gh->status == 'aktif' ? 'badge-success' : 'badge-warning' }}" style="font-size: 0.65rem; padding: 0.15rem 0.4rem;">{{ ucfirst($gh->status) }}</span>
                                </div>
                            </div>
                        </div>
                    </td>
                    
                    <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span style="font-weight: 700; color: var(--text-main); font-size: 1rem;">{{ $gh->racks_count }}</span>
                    </td>

                    <td style="padding: 1rem; vertical-align: middle;">
                        <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-main);">{{ $pct }}% <span style="font-weight: 500; color: var(--text-muted);">({{ number_format($allHoles->count(), 0, ',', '.') }} Lubang)</span></span>
                            <div style="width: 100px; height: 6px; background: #e2e8f0; border-radius: 10px; overflow: hidden;">
                                <div style="width: {{ $pct }}%; height: 100%; background: #16a34a; border-radius: 10px;"></div>
                            </div>
                        </div>
                    </td>

                    <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span style="background: #f1f5f9; color: #475569; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">{{ number_format($cntKosong, 0, ',', '.') }}</span>
                    </td>
                    
                    <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span style="background: #dcfce7; color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">{{ number_format($cntDitanam, 0, ',', '.') }}</span>
                    </td>

                    <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span style="background: #ffedd5; color: #ea580c; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">{{ number_format($cntReady, 0, ',', '.') }}</span>
                    </td>

                    <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span style="background: #dbeafe; color: #2563eb; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">{{ number_format($cntPanen, 0, ',', '.') }}</span>
                    </td>

                    <td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span style="background: #fee2e2; color: #dc2626; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">{{ number_format($cntRusak, 0, ',', '.') }}</span>
                    </td>

                    <td style="padding: 1rem; vertical-align: middle; text-align: right;">
                        <div style="display: flex; gap: 0.35rem; justify-content: flex-end; flex-wrap: wrap;">
                            <a href="{{ route('hydroponics.greenhouses.show', $gh->id) }}" class="btn btn-outline" style="padding: 0.35rem 0.6rem; font-size: 0.75rem;">
                                Kelola Rak
                            </a>
                            <a href="{{ route('hydroponics.greenhouses.print-single-gh-qr', $gh->id) }}" target="_blank"
                                style="padding: 0.35rem 0.5rem; background: var(--asr-green-light); color: var(--asr-green-dark); border: 1px solid var(--asr-green); border-radius: 6px; cursor: pointer;" title="Cetak QR GH">
                                <i class="ph ph-qr-code" style="font-size: 0.9rem;"></i>
                            </a>
                            @if(Auth::user()->isAgriAdmin())
                            <button onclick="openEditGHModal({{ $gh->id }}, '{{ addslashes($gh->name) }}', '{{ addslashes($gh->description ?? '') }}', '{{ $gh->status }}')"
                                style="padding: 0.35rem 0.5rem; background: white; color: #374151; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer;" title="Edit GH">
                                <i class="ph ph-pencil-simple" style="font-size: 0.9rem;"></i>
                            </button>
                            <button type="button" 
                                    onclick="confirmAction('Hapus Green House?', 'Apakah Anda yakin ingin menghapus {{ addslashes($gh->name) }} beserta seluruh rak & lubang di dalamnya?', '{{ route('hydroponics.greenhouses.destroy', $gh->id) }}', 'DELETE')"
                                    style="padding: 0.35rem 0.5rem; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; cursor: pointer;" title="Hapus GH">
                                <i class="ph ph-trash" style="font-size: 0.9rem;"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($greenhouses->count() === 0)
        <div style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.9rem;">
            Belum ada Green House yang ditambahkan.
        </div>
        @endif
    </div>
</div>

@if(Auth::user()->isAgriAdmin())
<!-- Modal Tambah GH -->
<div id="addGHModal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 999;">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
        <div class="flex-between" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Tambah Green House Baru</h3>
            <i class="ph ph-x" style="cursor: pointer; font-size: 1.25rem; color: #6b7280;" onclick="document.getElementById('addGHModal').style.display='none'"></i>
        </div>
        <form action="{{ route('hydroponics.greenhouses.store') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Nama Green House</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Green House E" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Deskripsi (Opsional)</label>
                <textarea name="description" class="form-control" placeholder="Lokasi, jenis sistem hidroponik..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;" rows="3"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('addGHModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit GH -->
<div id="editGHModal" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 999;">
    <div style="background: white; padding: 2rem; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
        <div class="flex-between" style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.25rem; font-weight: 700;">Edit Green House</h3>
            <i class="ph ph-x" style="cursor: pointer; font-size: 1.25rem; color: #6b7280;" onclick="document.getElementById('editGHModal').style.display='none'"></i>
        </div>
        <form id="editGHForm" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Nama Green House</label>
                <input type="text" name="name" id="editGHName" class="form-control" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
            </div>
            <div class="form-group" style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Status</label>
                <select name="status" id="editGHStatus" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;">
                    <option value="aktif">Aktif</option>
                    <option value="non-aktif">Non-Aktif</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.875rem;">Deskripsi</label>
                <textarea name="description" id="editGHDesc" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;" rows="3"></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('editGHModal').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditGHModal(id, name, desc, status) {
    document.getElementById('editGHForm').action = '/hydroponics/greenhouses/' + id + '/update';
    document.getElementById('editGHName').value = name;
    document.getElementById('editGHDesc').value = desc;
    document.getElementById('editGHStatus').value = status;
    document.getElementById('editGHModal').style.display = 'flex';
}
</script>
@endif
@endsection
