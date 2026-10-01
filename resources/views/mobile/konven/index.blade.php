@extends('mobile.konven.layout')

@section('title', 'Daftar Lahan')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h2 style="font-size: 1.25rem; font-weight: 700;">Pilih Lahan</h2>
    <p style="color: var(--text-muted); font-size: 0.9rem;">Pilih area lahan konvensional</p>
</div>

@forelse($lahans as $lahan)
    <a href="{{ route('m.konven.lahan', $lahan->id) }}" class="card card-clickable">
        <div class="list-item">
            <div>
                <div class="list-item-title">{{ $lahan->nama }}</div>
                <div class="list-item-subtitle">{{ $lahan->kodes_count ?? 0 }} Kode Area</div>
            </div>
            <i class="ph ph-caret-right" style="font-size: 1.25rem; color: var(--text-muted);"></i>
        </div>
    </a>
@empty
    <div class="card" style="text-align: center; padding: 2rem 1rem;">
        <i class="ph ph-map-pin" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
        <p style="color: var(--text-muted);">Belum ada data Lahan.</p>
    </div>
@endforelse
@endsection
