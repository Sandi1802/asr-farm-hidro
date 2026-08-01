@extends('layouts.app')

@section('title', 'Detail Pengadaan')

@section('content')
@php
    $checklists = [
        'status_hps'        => 'RFP',
        'status_rks'        => 'SPH',
        'status_topup'      => 'HPS',
        'status_pr'         => 'RKS',
        'status_rfq'        => 'TOP UP/GESER',
        'status_nego'       => 'PR',
        'status_po'         => 'RFQ',
        'status_top'        => 'AANWIJZING',
        'status_submit_dok' => 'NEGOSIASI',
        'status_gr_ses'     => 'PO/KONTRAK',
    ];
    $completedCount = collect($checklists)->keys()->filter(fn($key) => $procurement->$key)->count();
    $totalSteps = count($checklists);
    $progress = $procurement->progress_percentage;
@endphp

<style>
    .stepper-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        overflow-x: auto;
        position: relative;
        padding: 0.5rem 0 1rem;
    }
    .stepper-item {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        min-width: 80px;
        z-index: 1;
    }
    .stepper-item::after {
        content: '';
        position: absolute;
        top: 20px;
        left: 50%;
        width: 100%;
        height: 4px;
        background-color: var(--border-color);
        z-index: -1;
        transition: background 0.3s;
    }
    .stepper-item:last-child::after { display: none; }
    .stepper-item.completed::after { background-color: #4ADE80; }
    .stepper-circle {
        width: 40px; height: 40px;
        border-radius: 50%;
        background-color: var(--border-color);
        display: flex; align-items: center; justify-content: center;
        color: white; margin-bottom: 0.75rem;
        transition: all 0.3s;
        font-size: 1rem;
    }
    .stepper-item.completed .stepper-circle { background-color: #4ADE80; }
    .stepper-item.active .stepper-circle {
        background-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
    }
    .stepper-label {
        font-size: 0.7rem; font-weight: 600;
        text-align: center; color: var(--text-muted); line-height: 1.3;
    }
    .stepper-item.completed .stepper-label,
    .stepper-item.active .stepper-label { color: var(--text-main); }

    .form-label {
        display: block; font-size: 0.72rem; font-weight: 700;
        color: var(--text-muted); margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .form-control {
        width: 100%; padding: 0.65rem 0.9rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background: transparent; color: var(--text-main);
        font-family: inherit; font-size: 0.875rem; box-sizing: border-box;
    }
    .form-control:focus { outline: none; border-color: var(--asr-green); }
    .custom-checkbox {
        display: flex; align-items: center; gap: 0.5rem; cursor: pointer;
    }
    .custom-checkbox input {
        width: 18px; height: 18px; accent-color: var(--asr-green); cursor: pointer; flex-shrink: 0;
    }
    .custom-checkbox label { font-size: 0.875rem; color: var(--text-main); cursor: pointer; }
</style>

@if(session('success'))
<div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; border-radius: 8px; padding: 0.75rem 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
    <i class="ph ph-check-circle" style="font-size: 1.25rem;"></i> {{ session('success') }}
</div>
@endif

<!-- Stepper Progress -->
<div class="card" style="margin-bottom: 1.5rem;">
    <div class="flex-between" style="margin-bottom: 1rem;">
        <h3 style="font-size: 1rem; font-weight: 600;">Progress Tahapan Pengadaan</h3>
        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <div style="width: 140px; height: 8px; background: var(--border-color); border-radius: 10px; overflow: hidden;">
                <div style="height: 100%; width: {{ $progress }}%; background: {{ $progress >= 80 ? '#22c55e' : ($progress >= 50 ? '#f59e0b' : 'var(--asr-green)') }}; border-radius: 10px; transition: width 0.5s;"></div>
            </div>
            <span style="font-weight: 700; font-size: 1rem; color: var(--asr-green);">{{ $progress }}%</span>
        </div>
    </div>

    <div class="stepper-container">
        @php $stepIndex = 0; @endphp
        @foreach($checklists as $key => $label)
        @php
            $isCompleted = (bool)$procurement->$key;
            $isActive    = !$isCompleted && $stepIndex === $completedCount;
            $stepIndex++;
        @endphp
        <div class="stepper-item {{ $isCompleted ? 'completed' : ($isActive ? 'active' : '') }}">
            <div class="stepper-circle">
                @if($isCompleted)
                    <i class="ph ph-check" style="font-size: 1.1rem;"></i>
                @elseif($isActive)
                    <i class="ph ph-spinner-gap" style="font-size: 1.1rem;"></i>
                @else
                    <i class="ph ph-circle" style="font-size: 0.7rem;"></i>
                @endif
            </div>
            <div class="stepper-label">{{ $label }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- Edit Form -->
<div class="card">
    <div class="flex-between" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.4rem;">
                <a href="/procurement" style="color: var(--text-muted); text-decoration: none;">Pengadaan</a>
                &nbsp;›&nbsp; <span style="color: var(--asr-green); font-weight: 600;">Edit Pengadaan</span>
            </div>
            <h2 style="font-size: 1.4rem; font-weight: 700;">Detail Pengadaan</h2>
            <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                ID Proyek: {{ $procurement->project_id }}
            </div>
        </div>
    </div>

    <form action="/procurement/{{ $procurement->id }}" method="POST">
        @csrf

        <!-- Informasi Pengadaan -->
        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--asr-green);">
                <i class="ph ph-list-dashes"></i> Informasi Pengadaan
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div>
                    <label class="form-label">Project ID</label>
                    <input type="text" name="project_id" class="form-control" value="{{ $procurement->project_id }}">
                </div>
                <div>
                    <label class="form-label">Nama Proyek</label>
                    <input type="text" name="project_name" class="form-control" value="{{ $procurement->project_name }}">
                </div>
                <div style="grid-column: span 2;">
                    <label class="form-label">Procurement Name</label>
                    <input type="text" name="procurement_name" class="form-control" value="{{ $procurement->procurement_name }}">
                </div>
                <div>
                    <label class="form-label">Vendor Name</label>
                    <input type="text" name="vendor_name" class="form-control" value="{{ $procurement->vendor_name }}">
                </div>
                <div>
                    <label class="form-label">PR Number</label>
                    <input type="text" name="pr_number" class="form-control" value="{{ $procurement->pr_number }}">
                </div>
                <div>
                    <label class="form-label">PO Number</label>
                    <input type="text" name="po_number" class="form-control" value="{{ $procurement->po_number }}">
                </div>
                <div>
                    <label class="form-label">PO Value</label>
                    <input type="number" name="po_value" class="form-control" value="{{ $procurement->po_value }}">
                </div>
                <div>
                    <label class="form-label">Currency</label>
                    <select name="currency" class="form-control">
                        <option value="IDR" {{ $procurement->currency == 'IDR' ? 'selected' : '' }}>IDR</option>
                        <option value="USD" {{ $procurement->currency == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ $procurement->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ $procurement->due_date }}">
                </div>
                <div>
                    <label class="form-label">WBS</label>
                    <input type="text" name="wbs" class="form-control" value="{{ $procurement->wbs }}">
                </div>
            </div>
        </div>

        <!-- Checklist Tahapan -->
        <div style="margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--asr-green);">
                <i class="ph ph-check-square-offset"></i> Checklist Penagihan / Tahapan Lanjutan
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; padding: 1.25rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: rgba(0,0,0,0.01); margin-bottom: 1.25rem;">
                @foreach($checklists as $key => $label)
                <div class="custom-checkbox">
                    <input type="checkbox" id="{{ $key }}" name="{{ $key }}" {{ $procurement->$key ? 'checked' : '' }}>
                    <label for="{{ $key }}">{{ $label }}</label>
                </div>
                @endforeach
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div>
                    <label class="form-label">No SPP</label>
                    <input type="text" name="no_spp" class="form-control" placeholder="Masukkan nomor SPP..." value="{{ $procurement->no_spp }}">
                </div>
                <div>
                    <label class="form-label">Nilai Ket Transfer</label>
                    <input type="text" name="nilai_ket_transfer" class="form-control" placeholder="Keterangan transfer..." value="{{ $procurement->nilai_ket_transfer }}">
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <a href="/procurement" class="btn btn-outline" style="padding: 0.7rem 1.5rem; font-weight: 600;">Batalkan</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background-color: var(--asr-green);">
                <i class="ph ph-floppy-disk"></i> Simpan & Perbarui Data
            </button>
        </div>
    </form>
</div>

<!-- Log Perubahan Terakhir -->
<div class="card" style="margin-top: 1.5rem;">
    <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
        <i class="ph ph-clock-counter-clockwise"></i> LOG PERUBAHAN TERAKHIR
    </div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem;">
        <div>
            <div style="font-size: 0.8rem; font-weight: 600; color: var(--asr-green); margin-bottom: 0.3rem;">Hari ini, {{ now()->format('H:i') }} WIB</div>
            <div style="font-size: 0.85rem; color: var(--text-main);">Data diakses dan ditampilkan.</div>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.3rem;">{{ \Carbon\Carbon::parse($procurement->updated_at)->format('d M Y') }}</div>
            <div style="font-size: 0.85rem; color: var(--text-main);">Data terakhir diperbarui oleh sistem.</div>
        </div>
        <div>
            <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.3rem;">{{ \Carbon\Carbon::parse($procurement->created_at)->format('d M Y') }}</div>
            <div style="font-size: 0.85rem; color: var(--text-main);">Proyek diinisialisasi dalam sistem pengadaan.</div>
        </div>
    </div>
</div>
@endsection
