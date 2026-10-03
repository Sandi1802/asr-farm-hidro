@extends('layouts.app')

@section('content')
<div class="dashboard-header">
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h1 style="font-size: 1.5rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.5rem;">Daftar Pot di Baris: {{ $baris->nama_baris }} (GH: {{ $baris->gh->nama_gh }})</h1>
            <a href="{{ route('paprika.v2.baris', $baris->gh_id) }}" style="color: #007bff; text-decoration: none;">&larr; Kembali ke Daftar Baris</a>
        </div>
        <button onclick="document.getElementById('tanamMassalModal').showModal()" class="btn-primary" style="padding: 0.5rem 1rem; border-radius: 8px; background: #28a745; color: white; border: none; cursor: pointer;">
            <i class="fas fa-seedling"></i> Tanam Massal
        </button>
    </div>
</div>

<div class="content-body" style="padding: 20px; background: white; border-radius: 10px; margin-top: 20px;">
    @if(session('success'))
        <div style="padding: 10px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;">{{ session('success') }}</div>
    @endif
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
        @foreach($pots as $pot)
        <div style="border: 1px solid #eee; border-radius: 8px; padding: 15px; background: #f9f9f9;">
            <h4 style="margin-top:0;">Pot #{{ $pot->nomor_pot }}</h4>
            <p style="margin: 5px 0;">Status: 
                <span style="padding: 3px 8px; border-radius: 12px; font-size: 0.8rem; 
                    background: {{ $pot->status == 'kosong' ? '#ccc' : ($pot->status == 'ditanam' ? '#28a745' : ($pot->status == 'panen' ? '#ffc107' : '#dc3545')) }}; color: {{ $pot->status == 'kosong' ? '#000' : '#fff' }};">
                    {{ ucfirst($pot->status) }}
                </span>
            </p>
            @if($pot->status == 'ditanam')
                <p style="margin: 5px 0; font-size: 0.9rem;">Tanaman: {{ $pot->plant_name }}</p>
                <p style="margin: 5px 0; font-size: 0.9rem;">Ditanam: {{ \Carbon\Carbon::parse($pot->planted_at)->format('d M Y') }}</p>
                <p style="margin: 5px 0; font-size: 0.9rem;">Est Panen: {{ \Carbon\Carbon::parse($pot->estimated_harvest_at)->format('d M Y') }}</p>
                <div style="margin-top: 10px; display: flex; gap: 5px; flex-wrap: wrap;">
                    <form action="{{ route('paprika.v2.pot.action', $pot->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action_type" value="panen">
                        <button type="submit" style="padding: 5px 10px; background: #ffc107; border: none; border-radius: 3px; cursor: pointer;">Panen</button>
                    </form>
                    <form action="{{ route('paprika.v2.pot.action', $pot->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action_type" value="pupuk">
                        <button type="submit" style="padding: 5px 10px; background: #17a2b8; color: white; border: none; border-radius: 3px; cursor: pointer;">Pupuk</button>
                    </form>
                    <form action="{{ route('paprika.v2.pot.action', $pot->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="action_type" value="rusak">
                        <button type="submit" style="padding: 5px 10px; background: #dc3545; color: white; border: none; border-radius: 3px; cursor: pointer;">Rusak</button>
                    </form>
                </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<dialog id="tanamMassalModal" style="border: none; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; margin: auto;">
    <h3 style="margin-top:0;">Tanam Massal di Baris Ini</h3>
    <form action="{{ route('paprika.v2.tanam.massal', $baris->id) }}" method="POST">
        @csrf
        <div style="margin-bottom: 15px;">
            <label>Nama Tanaman</label>
            <input type="text" name="plant_name" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; margin-top: 5px;" placeholder="Cth: Paprika Merah">
        </div>
        <div style="margin-bottom: 15px;">
            <label>Estimasi Panen</label>
            <input type="date" name="estimated_harvest_at" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; margin-top: 5px;">
        </div>
        <div style="text-align: right;">
            <button type="button" onclick="document.getElementById('tanamMassalModal').close()" style="padding: 8px 15px; border: none; background: #ccc; border-radius: 5px; cursor: pointer;">Batal</button>
            <button type="submit" style="padding: 8px 15px; border: none; background: #28a745; color: white; border-radius: 5px; cursor: pointer; margin-left: 10px;">Tanam</button>
        </div>
    </form>
</dialog>
@endsection
