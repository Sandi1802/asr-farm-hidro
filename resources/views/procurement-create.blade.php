@extends('layouts.app')

@section('title', 'Tambah Data Pengadaan')

@section('content')
<style>
    .form-label {
        display: block; font-size: 0.72rem; font-weight: 700;
        color: var(--text-muted); margin-bottom: 0.4rem;
        text-transform: uppercase; letter-spacing: 0.05em;
    }
    .form-control {
        width: 100%; padding: 0.65rem 0.9rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        background: transparent; color: var(--text-main);
        font-family: inherit; font-size: 0.875rem; box-sizing: border-box;
    }
    .form-control:focus { outline: none; border-color: var(--asr-green); }
    .form-error { color: #16a34a; font-size: 0.78rem; margin-top: 0.3rem; }
</style>

<div class="card">
    <!-- Header -->
    <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.4rem;">
            <a href="/procurement" style="color: var(--text-muted); text-decoration: none;">Pengadaan</a>
            &nbsp;›&nbsp; <span style="color: var(--asr-green); font-weight: 600;">Tambah Data</span>
        </div>
        <h2 style="font-size: 1.4rem; font-weight: 700;">Tambah Data Pengadaan</h2>
        <div style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Isi formulir berikut untuk menambahkan data pengadaan baru.</div>
    </div>

    @if($errors->any())
    <div style="background: #fee2e2; border: 1px solid #fca5a5; color: #991b1b; border-radius: 8px; padding: 0.75rem 1.25rem; margin-bottom: 1.25rem;">
        <div style="font-weight: 600; margin-bottom: 0.4rem;"><i class="ph ph-warning-circle"></i> Harap perbaiki kesalahan berikut:</div>
        <ul style="margin: 0; padding-left: 1.25rem; font-size: 0.85rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="/procurement" method="POST">
        @csrf

        <!-- Informasi Pengadaan -->
        <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; color: var(--asr-green);">
                <i class="ph ph-list-dashes"></i> Informasi Pengadaan
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div>
                    <label class="form-label">Project ID <span style="color: var(--asr-green);">*</span></label>
                    <input type="text" name="project_id" class="form-control" placeholder="Contoh: PRJ-2024-V2-0045" value="{{ old('project_id') }}">
                    @error('project_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Nama Proyek</label>
                    <input type="text" name="project_name" class="form-control" placeholder="Nama proyek..." value="{{ old('project_name') }}">
                </div>
                <div style="grid-column: span 2;">
                    <label class="form-label">Nama Pengadaan <span style="color: var(--asr-green);">*</span></label>
                    <input type="text" name="procurement_name" class="form-control" placeholder="Nama pengadaan..." value="{{ old('procurement_name') }}">
                    @error('procurement_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">Nama Vendor <span style="color: var(--asr-green);">*</span></label>
                    <input type="text" name="vendor_name" class="form-control" placeholder="Nama vendor..." value="{{ old('vendor_name') }}">
                    @error('vendor_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="form-label">No PR</label>
                    <input type="text" name="pr_number" class="form-control" placeholder="Contoh: PR-88902-2024" value="{{ old('pr_number') }}">
                </div>
                <div>
                    <label class="form-label">No PO</label>
                    <input type="text" name="po_number" class="form-control" placeholder="Contoh: PO-44501239" value="{{ old('po_number') }}">
                </div>
                <div>
                    <label class="form-label">Nilai PO</label>
                    <input type="number" name="po_value" class="form-control" placeholder="0" value="{{ old('po_value') }}">
                </div>
                <div>
                    <label class="form-label">Mata Uang</label>
                    <select name="currency" class="form-control">
                        <option value="IDR" {{ old('currency') == 'IDR' ? 'selected' : '' }}>IDR</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                </div>
                <div>
                    <label class="form-label">WBS</label>
                    <input type="text" name="wbs" class="form-control" placeholder="Contoh: WBS.24.IT.001.02" value="{{ old('wbs') }}">
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div style="display: flex; justify-content: flex-end; gap: 1rem; padding-top: 1rem;">
            <a href="/procurement" class="btn btn-outline" style="padding: 0.7rem 1.5rem; font-weight: 600;">Batalkan</a>
            <button type="submit" class="btn btn-primary" style="padding: 0.7rem 1.5rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem; background-color: var(--asr-green);">
                <i class="ph ph-plus-circle"></i> Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
