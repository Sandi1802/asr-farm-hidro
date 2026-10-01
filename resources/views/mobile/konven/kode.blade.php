@extends('mobile.konven.layout')

@section('title', 'Kode ' . $kode->kode)
@section('back_url', route('m.konven.lahan', $kode->lahan_id))

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700;">Daftar Zona</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Zona di dalam Kode {{ $kode->kode }}</p>
</div>

@forelse($zonas as $z)
    <a href="{{ route('m.konven.zona', $z->id) }}" class="card card-clickable">
        <div class="list-item">
            <div>
                <div class="list-item-title">{{ $z->nama }}</div>
                <div class="list-item-subtitle">{{ $z->bedengans_count ?? 0 }} Bedengan</div>
            </div>
            <i class="ph ph-caret-right" style="font-size: 1.25rem; color: var(--text-muted);"></i>
        </div>
    </a>
@empty
    <div class="card" style="text-align: center; padding: 2rem 1rem;">
        <i class="ph ph-grid-four" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <p style="color: var(--text-muted);">Belum ada data Zona.</p>
    </div>
@endforelse
@endsection
