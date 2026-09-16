@extends('layouts.app')

@section('content')
<style>
/* Styling khusus untuk DataTables menyesuaikan tema */
.dt-buttons .dt-button {
    background: white;
    border: 1px solid var(--border-color);
    color: var(--text-main);
    padding: 0.35rem 0.75rem;
    border-radius: 4px;
    font-size: 0.85rem;
    margin-right: 0.25rem;
    cursor: pointer;
}
.dt-buttons .dt-button:hover {
    background: var(--bg-main);
}
.dataTables_wrapper .dataTables_length select {
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    border: 1px solid var(--border-color);
}
.dataTables_wrapper .dataTables_filter input {
    padding: 0.3rem 0.5rem;
    border-radius: 4px;
    border: 1px solid var(--border-color);
    margin-left: 0.5rem;
}
.dt-buttons-wrapper {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 0.5rem;
}
.dt-controls-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.dt-bottom-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}
.dataTables_wrapper .dataTables_paginate {
    display: flex; border-radius: 4px; overflow: hidden; border: 1px solid var(--border-color);
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    padding: 0.375rem 0.75rem; border: none; border-right: 1px solid var(--border-color);
    cursor: pointer; background: white; color: var(--text-main) !important; text-decoration: none; margin: 0 !important; border-radius: 0 !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:last-child {
    border-right: none;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #0d6efd; color: white !important; font-weight: bold;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled) {
    background: #f8f9fa;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: #f3f4f6; color: #9ca3af !important; cursor: not-allowed;
}
</style>

<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Pemupukan Paprika</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Jadwal dan riwayat pemupukan tanaman paprika.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="openModal('addPemupukanModal')" style="background: var(--asr-green); color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-plus"></i> Tambah Jadwal
        </button>
    </div>
</div>

@if(session('success'))
<div style="background: rgba(34, 197, 94, 0.1); color: #22c55e; padding: 1rem; border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.2); margin-top: 1rem;">
    {{ session('success') }}
</div>
@endif

<div class="card" style="background: var(--card-bg); border-radius: 12px; border: 1px solid var(--border-color); padding: 1.5rem; margin-top: 1.5rem;">
    <div class="table-responsive" style="overflow-x: auto;">
        <table class="table datatable" id="pemupukanTable" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th class="dt-no" style="width: 50px;">NO</th>
                    <th>TANGGAL</th>
                    <th>GREENHOUSE</th>
                    <th>JENIS PUPUK</th>
                    <th>DOSIS</th>
                    <th>PEKERJA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fertilizations as $index => $fert)
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <td style="padding: 1rem; text-align: center; color: var(--text-muted);">{{ $index + 1 }}</td>
                    <td style="padding: 1rem; color: var(--text-main); font-weight: 500;">{{ \Carbon\Carbon::parse($fert->date)->format('d M Y') }}</td>
                    <td style="padding: 1rem; color: var(--text-muted);">{{ $fert->greenhouse ? $fert->greenhouse->name : '-' }}</td>
                    <td style="padding: 1rem; color: var(--text-muted);">{{ $fert->fertilizer_name }}</td>
                    <td style="padding: 1rem; color: var(--text-muted);">{{ $fert->dose }}</td>
                    <td style="padding: 1rem; color: var(--text-muted);">{{ $fert->worker_name }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Pemupukan -->
<div id="addPemupukanModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background-color: var(--card-bg); margin: 10% auto; padding: 2rem; border: 1px solid var(--border-color); width: 100%; max-width: 500px; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: var(--text-main);">Tambah Jadwal Pemupukan</h2>
            <span onclick="closeModal('addPemupukanModal')" style="color: var(--text-muted); font-size: 1.5rem; font-weight: bold; cursor: pointer;">&times;</span>
        </div>
        <form action="/paprika/pemupukan" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 500;">Tanggal</label>
                <input type="date" name="date" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-bg); color: var(--text-main);">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 500;">Greenhouse</label>
                <select name="paprika_greenhouse_id" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-bg); color: var(--text-main);">
                    <option value="">Pilih Greenhouse</option>
                    @foreach($greenhouses as $gh)
                    <option value="{{ $gh->id }}">{{ $gh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 500;">Jenis Pupuk</label>
                <input type="text" name="fertilizer_name" required placeholder="Contoh: NPK" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-bg); color: var(--text-main);">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 500;">Dosis</label>
                <input type="text" name="dose" required placeholder="Contoh: 100ml / tanaman" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-bg); color: var(--text-main);">
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 500;">Nama Pekerja</label>
                <input type="text" name="worker_name" required placeholder="Nama pekerja" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; background: var(--card-bg); color: var(--text-main);">
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" onclick="closeModal('addPemupukanModal')" style="padding: 0.5rem 1rem; border: 1px solid var(--border-color); background: transparent; color: var(--text-main); border-radius: 8px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 0.5rem 1rem; border: none; background: var(--asr-green); color: white; border-radius: 8px; cursor: pointer; font-weight: 500;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>
@endsection
