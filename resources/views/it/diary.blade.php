@extends('layouts.app')

@section('content')
<div class="content-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-start;">
    <div>
        <h2 style="font-weight: 700; color: var(--text-main);">Diari Sistem (Log Aktivitas Login)</h2>
        <p style="color: var(--text-muted);">Riwayat akses ke dalam sistem oleh semua pengguna. (Hanya terlihat oleh IT Admin)</p>
    </div>
    @if($logs->count() > 0)
    <button type="button" onclick="confirmAction('Hapus Semua Log', 'Apakah Anda yakin ingin menghapus semua log aktivitas? Tindakan ini tidak dapat dibatalkan.', '{{ route('it.diary.delete-all') }}', 'DELETE')" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: var(--radius-md); font-weight: 500; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: background 0.2s;" onmouseover="this.style.background='#c82333'" onmouseout="this.style.background='#dc3545'">
        <i class="ph ph-trash"></i> Hapus Semua Log
    </button>
    @endif
</div>

<div class="card" style="background: var(--bg-card); border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: var(--shadow-sm);">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); text-align: left;">
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 600;">No</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 600;">Tanggal & Waktu</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 600;">Pengguna</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 600;">Divisi / Role</th>
                    <th style="padding: 1rem; color: var(--text-muted); font-weight: 600;">Detail Perangkat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                <tr style="border-bottom: 1px solid var(--border-color); transition: background 0.2s;" onmouseover="this.style.background='var(--bg-hover)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem; color: var(--text-main);">{{ $logs->firstItem() + $index }}</td>
                    <td style="padding: 1rem; color: var(--text-main);">
                        <div style="font-weight: 500;">{{ $log->created_at->format('d M Y') }}</div>
                        <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $log->created_at->format('H:i:s') }}</div>
                    </td>
                    <td style="padding: 1rem; color: var(--text-main);">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary-light); color: var(--primary-color); display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                {{ strtoupper(substr($log->user->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600;">{{ $log->user->name ?? 'User Dihapus' }}</div>
                                <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $log->user->username ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem;">
                        <span style="background: var(--bg-hover); color: var(--text-main); padding: 0.25rem 0.75rem; border-radius: 99px; font-size: 0.85rem; font-weight: 500; border: 1px solid var(--border-color);">
                            {{ ucwords(str_replace('_', ' ', $log->user->role_agri ?? '-')) }}
                        </span>
                    </td>
                    <td style="padding: 1rem; color: var(--text-muted); font-size: 0.85rem; max-width: 250px;">
                        <div style="display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="ph ph-laptop" style="font-size: 1.2rem; color: var(--primary-color);"></i>
                            <div>
                                <div style="color: var(--text-main); font-weight: 500;">IP: {{ $log->ip_address }}</div>
                                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px;" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                        <i class="ph ph-book-open-text" style="font-size: 3rem; color: var(--border-color); margin-bottom: 1rem; display: block;"></i>
                        Belum ada riwayat login.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 1.5rem;">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
</div>

@push('scripts')
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2500,
            customClass: {
                popup: 'asr-swal-popup'
            }
        });
    });
</script>
@endif
@endpush
@endsection
