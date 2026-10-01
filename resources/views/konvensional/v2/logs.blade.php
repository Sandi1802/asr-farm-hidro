@extends('layouts.app')

@section('title', 'Laporan Pemeliharaan Konvensional')

@section('content')
<div class="container-fluid" style="padding: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 2rem;">
        <h1 style="margin:0; font-weight:700; color:var(--text-main);">Laporan Pemeliharaan & Aktivitas Harian (Logs)</h1>
        
        <form method="GET" action="{{ route('konven.v2.logs') }}" style="display:flex; gap:0.5rem; align-items:center;">
            <input type="date" name="start_date" value="{{ $dateStart }}" class="form-control" style="border-radius:8px; padding:0.5rem; border:1px solid var(--border-color);">
            <span style="color:var(--text-muted);">-</span>
            <input type="date" name="end_date" value="{{ $dateEnd }}" class="form-control" style="border-radius:8px; padding:0.5rem; border:1px solid var(--border-color);">
            <button type="submit" style="padding:0.5rem 1rem; background:var(--asr-green); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600;"><i class="ph ph-funnel"></i> Filter</button>
        </form>
    </div>

    <div style="background:white; border-radius:12px; border:1px solid var(--border-color); overflow:hidden;">
        <div style="overflow-x:auto;">
            <table class="table datatable" style="width:100%; border-collapse:collapse; text-align:left; min-width:800px;">
                <thead style="background:var(--asr-green); color:white;">
                    <tr>
                        <th style="padding:1rem;">Waktu</th>
                        <th style="padding:1rem;">Pelaku</th>
                        <th style="padding:1rem;">Tindakan</th>
                        <th style="padding:1rem;">Lokasi Lahan</th>
                        <th style="padding:1rem;">Keterangan / Detail</th>
                        <th style="padding:1rem;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allLogs as $log)
                    <tr style="border-bottom:1px solid var(--border-color);">
                        <td style="padding:1rem; white-space:nowrap; color:var(--text-muted);">{{ \Carbon\Carbon::parse($log['created_at'])->format('d M Y, H:i') }}</td>
                        <td style="padding:1rem; font-weight:600;">
                            <div style="display:flex; align-items:center; gap:0.5rem;">
                                <div style="width:28px; height:28px; border-radius:50%; background:var(--asr-green-light); color:var(--asr-green); display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8rem;">
                                    {{ substr($log['actor'], 0, 1) }}
                                </div>
                                {{ $log['actor'] }}
                            </div>
                        </td>
                        <td style="padding:1rem;">
                            @if(str_contains(strtolower($log['action']), 'tanam'))
                                <span style="background:#dcfce7; color:#166534; padding:0.25rem 0.6rem; border-radius:50px; font-size:0.8rem; font-weight:600;">🌱 {{ $log['action'] }}</span>
                            @elseif(str_contains(strtolower($log['action']), 'panen'))
                                <span style="background:#fef3c7; color:#b45309; padding:0.25rem 0.6rem; border-radius:50px; font-size:0.8rem; font-weight:600;">🌾 {{ $log['action'] }}</span>
                            @elseif(str_contains(strtolower($log['action']), 'pupuk'))
                                <span style="background:#dbeafe; color:#1e40af; padding:0.25rem 0.6rem; border-radius:50px; font-size:0.8rem; font-weight:600;">💧 {{ $log['action'] }}</span>
                            @elseif(str_contains(strtolower($log['action']), 'semprot'))
                                <span style="background:#e0e7ff; color:#3730a3; padding:0.25rem 0.6rem; border-radius:50px; font-size:0.8rem; font-weight:600;">💨 {{ $log['action'] }}</span>
                            @else
                                <span style="background:#fee2e2; color:#b91c1c; padding:0.25rem 0.6rem; border-radius:50px; font-size:0.8rem; font-weight:600;">🚨 {{ $log['action'] }}</span>
                            @endif
                        </td>
                        <td style="padding:1rem; font-size:0.85rem; color:var(--text-muted);">{{ $log['lokasi'] }}</td>
                        <td style="padding:1rem; font-size:0.85rem; color:var(--text-main);">{{ $log['detail'] }}</td>
                        <td style="padding:1rem;">
                            <form method="POST" action="{{ route('konven.v2.logs.destroy', $log['id']) }}" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?');" style="margin:0;">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:#fee2e2; border:1px solid #fca5a5; color:#dc2626; padding:0.4rem 0.6rem; border-radius:6px; cursor:pointer;" title="Hapus Log">
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
@endsection
