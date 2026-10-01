@extends('mobile.konven.layout')

@section('title', $zona->nama)
@section('back_url', route('m.konven.kode', $zona->kode_id))

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700;">Daftar Bedengan</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Bedengan di dalam {{ $zona->nama }}</p>
</div>

@forelse($bedengans as $b)
    <a href="{{ route('m.konven.bedengan', $b->id) }}" class="card card-clickable" style="display: block; margin-bottom: 1rem;">
        <div class="list-item" style="margin-bottom: 0.75rem;">
            <div>
                <div class="list-item-title">{{ $b->nama_display ?: 'Bedengan ' . $b->nomor }}</div>
                <div class="list-item-subtitle">Total: {{ $b->total_lubang ?? 0 }} Lubang</div>
            </div>
            <i class="ph ph-caret-right" style="font-size: 1.25rem; color: var(--text-muted);"></i>
        </div>
        
        <div class="grid-3" style="gap: 0.5rem; border-top: 1px solid var(--border); padding-top: 0.75rem;">
            <div style="text-align: center;">
                <div style="font-size: 0.75rem; color: var(--text-muted);">Kosong</div>
                <div style="font-weight: 700; color: #374151;">{{ $b->lubang_kosong ?? 0 }}</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 0.75rem; color: var(--text-muted);">Ditanam</div>
                <div style="font-weight: 700; color: var(--primary-dark);">{{ $b->lubang_ditanam ?? 0 }}</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 0.75rem; color: var(--text-muted);">Rusak</div>
                <div style="font-weight: 700; color: var(--danger);">{{ $b->lubang_rusak ?? 0 }}</div>
            </div>
        </div>
    </a>
@empty
    <div class="card" style="text-align: center; padding: 2rem 1rem;">
        <i class="ph ph-rows" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <p style="color: var(--text-muted);">Belum ada data Bedengan.</p>
    </div>
@endforelse
@endsection
