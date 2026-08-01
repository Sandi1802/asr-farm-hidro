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

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem;">
        @foreach($greenhouses as $gh)
        <div class="card" style="border: 1px solid var(--border-color); display: flex; flex-direction: column; justify-content: space-between; position: relative;">
            <div>
                <div class="flex-between" style="margin-bottom: 1rem; align-items: flex-start;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="icon-box green">
                            <i class="ph ph-house-line"></i>
                        </div>
                        <div>
                            <h3 style="font-weight: 700; font-size: 1.1rem; color: var(--text-main);">{{ $gh->name }}</h3>
                            <span style="font-size: 0.85rem; color: var(--text-muted);">{{ $gh->racks_count }} Rak Aktif</span>
                        </div>
                    </div>
                    <span class="badge {{ $gh->status == 'aktif' ? 'badge-success' : 'badge-warning' }}">{{ ucfirst($gh->status) }}</span>
                </div>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.4;">
                    {{ $gh->description ?: 'Tidak ada deskripsi.' }}
                </p>

                @php
                    $thirtyDaysAgo = now()->subDays(30);
                    $allHoles = $gh->racks->flatMap->rows->flatMap->holes;
                    $cntKosong  = $allHoles->where('status', 'kosong')->count();
                    $cntDitanamTotal = $allHoles->where('status', 'ditanam')->count();
                    $cntReady   = $allHoles->where('status', 'ditanam')->filter(fn($h) => $h->planted_at && \Carbon\Carbon::parse($h->planted_at) <= $thirtyDaysAgo)->count();
                    $cntDitanam = max(0, $cntDitanamTotal - $cntReady);
                    $cntPanen   = $allHoles->where('status', 'panen')->count();
                    $cntRusak   = $allHoles->where('status', 'rusak')->count();
                @endphp

                {{-- AKUMULASI DARI SELURUH RAK INI --}}
                <div style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 10px; padding: 0.875rem; margin-bottom: 1.25rem;">
                    <div style="font-size: 0.725rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.625rem; display: flex; justify-content: space-between; align-items: center;">
                        <span><i class="ph ph-chart-pie"></i> Akumulasi Seluruh Rak</span>
                        <span style="color: var(--text-main); font-size: 0.75rem;">{{ number_format($allHoles->count(), 0, ',', '.') }} Lubang</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.4rem; font-size: 0.78rem;">
                        <div style="background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;" title="Lubang Tidak Ditanam">
                            <span style="color: var(--text-muted); font-weight: 500; display: flex; align-items: center; gap: 0.3rem;">
                                <div style="width: 7px; height: 7px; border-radius: 50%; background: #94a3b8;"></div> Tidak Ditanam
                            </span>
                            <strong style="color: var(--text-main);">{{ number_format($cntKosong, 0, ',', '.') }}</strong>
                        </div>

                        <div style="background: rgba(22, 163, 74, 0.1); border: 1px solid rgba(22, 163, 74, 0.2); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;" title="Sedang Ditanam">
                            <span style="color: #16a34a; font-weight: 500; display: flex; align-items: center; gap: 0.3rem;">
                                <div style="width: 7px; height: 7px; border-radius: 50%; background: #16a34a;"></div> Ditanam
                            </span>
                            <strong style="color: #16a34a;">{{ number_format($cntDitanam, 0, ',', '.') }}</strong>
                        </div>

                        <div style="background: rgba(234, 88, 12, 0.1); border: 1px solid rgba(234, 88, 12, 0.2); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;" title="Siap Dipanen (>=30 Hari)">
                            <span style="color: #ea580c; font-weight: 600; display: flex; align-items: center; gap: 0.3rem;">
                                <div style="width: 7px; height: 7px; border-radius: 50%; background: #ea580c;"></div> Siap Panen
                            </span>
                            <strong style="color: #ea580c;">{{ number_format($cntReady, 0, ',', '.') }}</strong>
                        </div>

                        <div style="background: rgba(37, 99, 235, 0.1); border: 1px solid rgba(37, 99, 235, 0.2); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center;" title="Sudah Dipanen">
                            <span style="color: #2563eb; font-weight: 500; display: flex; align-items: center; gap: 0.3rem;">
                                <div style="width: 7px; height: 7px; border-radius: 50%; background: #2563eb;"></div> Dipanen
                            </span>
                            <strong style="color: #2563eb;">{{ number_format($cntPanen, 0, ',', '.') }}</strong>
                        </div>

                        <div style="background: rgba(220, 38, 38, 0.1); border: 1px solid rgba(220, 38, 38, 0.2); border-radius: 6px; padding: 0.35rem 0.5rem; display: flex; justify-content: space-between; align-items: center; grid-column: span 2;" title="Total Kerusakan / Lubang Rusak">
                            <span style="color: #dc2626; font-weight: 500; display: flex; align-items: center; gap: 0.3rem;">
                                <div style="width: 7px; height: 7px; border-radius: 50%; background: #dc2626;"></div> Kerusakan (Rusak)
                            </span>
                            <strong style="color: #dc2626;">{{ number_format($cntRusak, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                <a href="{{ route('hydroponics.greenhouses.show', $gh->id) }}" style="text-decoration: none; color: var(--asr-green); font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 0.375rem;">
                    Kelola Rak & Lubang <i class="ph ph-arrow-right"></i>
                </a>

                <div style="display: flex; gap: 0.375rem; align-items: center;">
                    <a href="{{ route('hydroponics.greenhouses.print-single-gh-qr', $gh->id) }}" target="_blank"
                        style="padding: 0.375rem 0.625rem; background: var(--asr-green-light); color: var(--asr-green-dark); border: 1px solid var(--asr-green); border-radius: 6px; font-size: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600;"
                        title="Cetak QR Code Green House Ini">
                        <i class="ph ph-qr-code"></i> QR GH
                    </a>
                    @if(Auth::user()->isAgriAdmin())
                    <button onclick="openEditGHModal({{ $gh->id }}, '{{ addslashes($gh->name) }}', '{{ addslashes($gh->description ?? '') }}', '{{ $gh->status }}')"
                        style="padding: 0.375rem 0.625rem; background: var(--bg-color); color: var(--text-main); border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.8rem; cursor: pointer;"
                        title="Edit Green House">
                        <i class="ph ph-pencil-simple"></i> Edit
                    </button>
                    <form action="{{ route('hydroponics.greenhouses.destroy', $gh->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus {{ addslashes($gh->name) }} beserta seluruh rak & lubang di dalamnya?')" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 0.375rem 0.625rem; background: rgba(220, 38, 38, 0.1); color: #dc2626; border: 1px solid rgba(220, 38, 38, 0.2); border-radius: 6px; font-size: 0.8rem; cursor: pointer;"
                            title="Hapus Green House">
                            <i class="ph ph-trash"></i> Hapus
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
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
