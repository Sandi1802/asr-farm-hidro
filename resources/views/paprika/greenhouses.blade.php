@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Greenhouse Paprika</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">Daftar greenhouse paprika.</p>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 1.5rem;">
    <div class="table-responsive">
        <table class="table datatable" style="width: 100%;">
            <thead>
                <tr>
                    <th class="dt-no">NO</th>
                    <th>Nama Greenhouse</th>
                    <th>Kapasitas</th>
                    <th>Jml Tanaman</th>
                    <th>Ditanam</th>
                    <th>Panen</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($greenhouses as $gh)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td style="font-weight: 600; color: var(--text-main);">{{ $gh->name }}</td>
                    <td>{{ number_format($gh->capacity, 0, ',', '.') }} Tanaman</td>
                    <td>{{ number_format($gh->plants_count, 0, ',', '.') }}</td>
                    <td>{{ number_format($gh->ditanam_count, 0, ',', '.') }}</td>
                    <td>{{ number_format($gh->panen_count, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #16a34a;">Aktif</span>
                    </td>
                    <td>
                        <a href="{{ url('/paprika/greenhouses/' . $gh->id) }}" class="btn btn-outline" style="border-color: var(--asr-green); color: var(--asr-green); text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 500; font-size: 0.85rem;" title="Detail">
                            <i class="ph ph-eye"></i> Detail
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
