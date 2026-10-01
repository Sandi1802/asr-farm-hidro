@extends('mobile.konven.layout')

@section('title', $lahan->nama)
@section('back_url', route('m.konven.index'))

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700;">Daftar Kode (Blok)</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Area di dalam {{ $lahan->nama }}</p>
</div>

@forelse($kodes as $k)
    <a href="{{ route('m.konven.kode', $k->id) }}" class="card card-clickable">
        <div class="list-item">
            <div>
                <div class="list-item-title">Kode {{ $k->kode }}</div>
                <div class="list-item-subtitle">{{ $k->zonas_count ?? 0 }} Zona</div>
            </div>
            <i class="ph ph-caret-right" style="font-size: 1.25rem; color: var(--text-muted);"></i>
        </div>
    </a>
@empty
    <div class="card" style="text-align: center; padding: 2rem 1rem;">
        <i class="ph ph-squares-four" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <p style="color: var(--text-muted);">Belum ada data Kode.</p>
    </div>
@endforelse
@endsection
