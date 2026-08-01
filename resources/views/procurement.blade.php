@extends('layouts.app')

@section('title', 'Procurement')

@section('content')

@if(session('success'))
<div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; border-radius: 8px; padding: 0.75rem 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
    <i class="ph ph-check-circle" style="font-size: 1.25rem;"></i> {{ session('success') }}
</div>
@endif

<div class="card">
    <div class="flex-between" style="margin-bottom: 1.5rem;">
        <h2 style="font-size: 1.25rem; font-weight: 600;">Data Procurement</h2>
        <a href="/procurement/create" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <i class="ph ph-plus-circle"></i> Tambah Data
        </a>
    </div>

    <div class="table-responsive">
        <table class="table datatable" style="width: 100%;">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>PROYEK</th>
                    <th>NAMA PENGADAAN</th>
                    <th>NAMA VENDOR</th>
                    <th>NO PR</th>
                    <th>NO PO</th>
                    <th>NILAI PO</th>
                    <th>MATA UANG</th>
                    <th>DUE DATE</th>
                    <th>PROGRESS</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                @forelse($procurements as $index => $item)
                @php $progress = $item->progress_percentage; @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->project_name ?? $item->project_id }}</td>
                    <td>{{ $item->procurement_name }}</td>
                    <td>{{ $item->vendor_name }}</td>
                    <td>{{ $item->pr_number ?? '-' }}</td>
                    <td>{{ $item->po_number ?? '-' }}</td>
                    <td>{{ number_format($item->po_value, 0, ',', '.') }}</td>
                    <td>{{ $item->currency }}</td>
                    <td>{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('d-m-Y') : '-' }}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.5rem; min-width: 120px;">
                            <div style="flex: 1; height: 8px; background: var(--border-color); border-radius: 10px; overflow: hidden;">
                                <div style="height: 100%; width: {{ $progress }}%; background: {{ $progress >= 80 ? '#22c55e' : ($progress >= 50 ? '#f59e0b' : 'var(--asr-green)') }}; border-radius: 10px; transition: width 0.4s;"></div>
                            </div>
                            <span style="font-size: 0.85rem; font-weight: 700; min-width: 35px;">{{ $progress }}%</span>
                        </div>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="/procurement/{{ $item->id }}" class="btn btn-outline" style="padding: 0.25rem 0.5rem; display: inline-flex;" title="View">
                                <i class="ph ph-eye"></i>
                            </a>
                            <a href="/procurement/{{ $item->id }}" class="btn btn-outline" style="padding: 0.25rem 0.5rem; display: inline-flex; color: #2563EB; border-color: #2563EB;" title="Edit">
                                <i class="ph ph-pencil-simple"></i>
                            </a>
                            <form action="/procurement/{{ $item->id }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline" style="padding: 0.25rem 0.5rem; color: #16a34a; border-color: #16a34a;" title="Delete">
                                    <i class="ph ph-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                        <i class="ph ph-folder-dashed" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                        Belum ada data procurement.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
