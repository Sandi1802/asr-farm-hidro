@extends('layouts.app')
@section('title', 'Pengaturan Umum')

@section('content')
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <h1 style="margin:0; font-size:1.5rem; font-weight:700; color:var(--text-main); display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-gear"></i> Pengaturan Umum
    </h1>
</div>

@if(session('success'))
<div style="background:#dcfce7; color:#166534; padding:0.9rem 1rem; border-radius:8px; margin-bottom:1rem; font-weight:500;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="card" style="padding:1.5rem; border:1px solid var(--border-color); border-radius:12px; background:white;">
    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom:1.5rem;">
            <label style="display:block; margin-bottom:0.5rem; font-weight:600; color:var(--text-main);">Teks Pengumuman Berjalan (Marquee)</label>
            <textarea name="marquee_text" rows="3" required
                      style="width:100%; padding:0.75rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box; font-family:inherit;">{{ $marqueeText }}</textarea>
            
        </div>

        <button type="submit" style="padding:0.75rem 1.5rem; background:var(--asr-green); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600;">
            <i class="ph ph-floppy-disk"></i> Simpan Pengaturan
        </button>
    </form>
</div>
@endsection
