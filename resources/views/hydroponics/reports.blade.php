@extends('layouts.app')
@section('title', 'Laporan & Grafik')
@section('content')
<div class="overview-grid">
    <div class="col-span-4">
        <div class="card" style="text-align: center;">
            <div class="icon-box" style="background: var(--asr-green-light); color: var(--asr-green); margin: 0 auto 1rem;">
                <i class="ph ph-plant"></i>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($plantedCount, 0, ',', '.') }}</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Total Aktivitas Tanam</p>
        </div>
    </div>
    
    <div class="col-span-4">
        <div class="card" style="text-align: center;">
            <div class="icon-box" style="background: #eff6ff; color: #3b82f6; margin: 0 auto 1rem;">
                <i class="ph ph-basket"></i>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($harvestedCount, 0, ',', '.') }}</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Total Aktivitas Panen</p>
        </div>
    </div>

    <div class="col-span-4">
        <div class="card" style="text-align: center;">
            <div class="icon-box" style="background: #fef2f2; color: #ef4444; margin: 0 auto 1rem;">
                <i class="ph ph-warning"></i>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">{{ number_format($damagedHoles, 0, ',', '.') }}</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Lubang Status Rusak</p>
        </div>
    </div>

    <div class="col-span-12">
        <div class="card">
            <h3 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;">Log Aktivitas Terbaru</h3>
            <div style="overflow-x: auto;">
                <table class="table datatable" style="width: 100%; text-align: left; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <th style="padding: 1rem 0;">Waktu</th>
                            <th style="padding: 1rem 0;">User</th>
                            <th style="padding: 1rem 0;">Aktivitas</th>
                            <th style="padding: 1rem 0;">Lokasi</th>
                            <th style="padding: 1rem 0;">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentActivities as $activity)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 1rem 0;">{{ $activity->created_at->format('d M Y H:i') }}</td>
                            <td style="padding: 1rem 0;">{{ $activity->user->name ?? 'User' }}</td>
                            <td style="padding: 1rem 0;">
                                @if(in_array($activity->type, ['tanam', 'ditanam']))
                                    <span class="badge" style="background: var(--asr-green-light); color: var(--asr-green-dark);">Tanam</span>
                                @elseif($activity->type == 'panen')
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8;">Panen</span>
                                @else
                                    <span class="badge" style="background: #fef2f2; color: #b91c1c;">Rusak</span>
                                @endif
                            </td>
                            <td style="padding: 1rem 0;">
                                @if(isset($activity->location_base) && $activity->hole_count > 1)
                                    {{ $activity->location_base }}
                                @else
                                    {{ $activity->hole->row->rack->greenhouse->name ?? '-' }} > 
                                    {{ $activity->hole->row->rack->name ?? '-' }} > 
                                    {{ $activity->hole->name ?? '-' }}
                                @endif
                            </td>
                            <td style="padding: 1rem 0;">
                                {{ $activity->description ?: '-' }}
                                @if(isset($activity->hole_count) && $activity->hole_count > 1)
                                    <br><small style="color:var(--text-muted);">{{ $activity->hole_count }} lubang dalam rak ini</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="padding: 2rem 0; text-align: center; color: var(--text-muted);">Belum ada log aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
