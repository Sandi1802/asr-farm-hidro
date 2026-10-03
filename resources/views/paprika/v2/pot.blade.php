@extends('layouts.app')
@section('title', 'Manajemen Pot - Paprika V2')
@section('content')

<div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:0.75rem; display:flex; align-items:center; gap:0.4rem;">
    <a href="{{ route('paprika.v2.index') }}" style="color:var(--text-muted); text-decoration:none;"><i class="ph ph-house-line"></i> Paprika V2</a>
    <i class="ph ph-caret-right"></i>
    <a href="{{ route('paprika.v2.baris', $baris->gh->id) }}" style="color:var(--text-muted); text-decoration:none;">{{ $baris->gh->nama_gh }}</a>
    <i class="ph ph-caret-right"></i>
    <span style="color:var(--text-main); font-weight:600;">{{ $baris->nama_baris }}</span>
</div>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
    <div>
        <h2 style="margin:0; font-size:1.25rem; font-weight:600; color:var(--text-main);">
            <i class="ph ph-pepper"></i> Daftar Pot Paprika
        </h2>
        <p style="color:var(--text-muted); font-size:0.85rem; margin-top:0.25rem;">
            Detail dan manajemen aksi untuk pot di {{ $baris->nama_baris }}
        </p>
    </div>
    <button onclick="document.getElementById('modalTanamMassal').style.display='flex'"
            style="background:#0284c7; color:white; border:none; padding:0.6rem 1.2rem; border-radius:8px; cursor:pointer; font-weight:600; display:flex; align-items:center; gap:0.5rem;">
        <i class="ph ph-plant"></i> Tanam Massal Baris Ini
    </button>
</div>

@if(session('success'))
<div style="background:#dcfce7; color:#166534; padding:0.9rem 1rem; border-radius:8px; margin-bottom:1rem; font-weight:500;">
    <i class="ph ph-check-circle"></i> {{ session('success') }}
</div>
@endif

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
    @forelse($pots as $pot)
        @php
            $bg = '#fff';
            $badgeBg = '#f1f5f9'; $badgeColor = '#64748b'; $icon = 'ph-circle-dashed';
            if($pot->status == 'ditanam') { $badgeBg = '#dcfce7'; $badgeColor = '#16a34a'; $icon = 'ph-seedling'; $bg='#f0fdf4'; }
            if($pot->status == 'panen') { $badgeBg = '#ffedd5'; $badgeColor = '#c2410c'; $icon = 'ph-basket'; }
            if($pot->status == 'rusak') { $badgeBg = '#fee2e2'; $badgeColor = '#dc2626'; $icon = 'ph-warning-circle'; $bg='#fef2f2'; }
        @endphp
        <div style="background:{{ $bg }}; border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; display:flex; flex-direction:column; gap:1rem; position:relative;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <h3 style="margin:0 0 0.25rem 0; font-size:1.1rem; color:var(--text-main);">Pot #{{ $pot->nomor_pot }}</h3>
                    @if($pot->plant_name)
                    <div style="font-size:0.85rem; font-weight:600; color:var(--text-main);">{{ $pot->plant_name }}</div>
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;"><i class="ph ph-calendar"></i> Tanam: {{ \Carbon\Carbon::parse($pot->planted_at)->format('d M Y') }}</div>
                    @else
                    <div style="font-size:0.85rem; color:var(--text-muted);">Belum ada tanaman</div>
                    @endif
                </div>
                <div style="background:{{ $badgeBg }}; color:{{ $badgeColor }}; padding:4px 8px; border-radius:6px; font-size:0.7rem; font-weight:700; display:flex; align-items:center; gap:4px; text-transform:uppercase;">
                    <i class="ph {{ $icon }}"></i> {{ $pot->status }}
                </div>
            </div>

            <div style="margin-top:auto; padding-top:1rem; border-top:1px dashed var(--border-color); display:flex; gap:0.5rem; flex-wrap:wrap;">
                @if($pot->status == 'kosong' || $pot->status == 'panen' || $pot->status == 'rusak')
                <button onclick="openActionModal({{ $pot->id }}, 'tanam')" style="flex:1; padding:0.5rem; background:var(--asr-green); color:white; border:none; border-radius:6px; cursor:pointer; font-size:0.75rem; font-weight:600;"><i class="ph ph-plant"></i> Tanam</button>
                @elseif($pot->status == 'ditanam')
                <button onclick="openActionModal({{ $pot->id }}, 'pupuk')" style="flex:1; padding:0.5rem; background:#0ea5e9; color:white; border:none; border-radius:6px; cursor:pointer; font-size:0.75rem; font-weight:600;"><i class="ph ph-drop"></i> Pupuk/Semprot</button>
                <button onclick="openActionModal({{ $pot->id }}, 'panen')" style="flex:1; padding:0.5rem; background:#f59e0b; color:white; border:none; border-radius:6px; cursor:pointer; font-size:0.75rem; font-weight:600;"><i class="ph ph-basket"></i> Panen</button>
                <button onclick="openActionModal({{ $pot->id }}, 'rusak')" style="padding:0.5rem; background:#ef4444; color:white; border:none; border-radius:6px; cursor:pointer; font-size:0.75rem; font-weight:600;" title="Tandai Rusak"><i class="ph ph-warning"></i></button>
                @endif
                <form action="{{ route('paprika.v2.pot.destroy', $pot->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pot ini?')" style="margin:0;">
                    @csrf @method('DELETE')
                    <button type="submit" style="padding:0.5rem; background:transparent; color:#ef4444; border:1px solid #ef4444; border-radius:6px; cursor:pointer; font-size:0.75rem;" title="Hapus Pot Permanen"><i class="ph ph-trash"></i></button>
                </form>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; padding:3rem; text-align:center; color:var(--text-muted); background:white; border-radius:12px; border:1px solid var(--border-color);">
            Belum ada pot di baris ini. Silakan hapus baris dan buat ulang dengan jumlah pot yang diinginkan.
        </div>
    @endforelse
</div>

{{-- Modal Tanam Massal Baris --}}
<div id="modalTanamMassal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:460px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Tanam Massal di Baris Ini</h3>
            <button type="button" onclick="document.getElementById('modalTanamMassal').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form action="{{ route('paprika.v2.tanam.massal', $baris->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jenis Bibit Paprika *</label>
                <input type="text" name="plant_name" required placeholder="Contoh: Paprika Merah"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Estimasi Panen *</label>
                <input type="date" name="estimated_harvest_at" required
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <p style="font-size:0.8rem; color:var(--text-muted); margin-bottom:1rem;">
                <i class="ph ph-info"></i> Aksi ini akan menanam bibit pada semua pot di baris ini yang berstatus "kosong".
            </p>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTanamMassal').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:#0284c7; color:white; border-radius:8px; cursor:pointer; font-weight:600;">Tanam Massal</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Action Pot --}}
<div id="modalActionPot" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:400px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
            <h3 style="margin:0; font-size:1rem; font-weight:600;">Aksi Pot <span id="actionTitle"></span></h3>
            <button type="button" onclick="document.getElementById('modalActionPot').style.display='none'" style="background:none; border:none; font-size:1.25rem; cursor:pointer;"><i class="ph ph-x"></i></button>
        </div>
        <form id="formActionPot" method="POST">
            @csrf
            <input type="hidden" name="action_type" id="inputTypeAction">
            
            <div id="wrapperDetails" style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Detail (Opsional)</label>
                <textarea name="details" id="inputDetails" rows="2" placeholder="Catatan opsional..."
                          style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;"></textarea>
            </div>
            
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalActionPot').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" id="btnSubmitAction" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Proses</button>
            </div>
        </form>
    </div>
</div>

<script>
function openActionModal(potId, actionType) {
    document.getElementById('inputTypeAction').value = actionType;
    document.getElementById('formActionPot').action = '/paprika/v2/pot/' + potId + '/action';
    
    let title = '';
    let btnColor = 'var(--asr-green)';
    
    if(actionType === 'tanam') { title = 'Tanam'; btnColor = 'var(--asr-green)'; document.getElementById('inputDetails').placeholder = 'Tanam paprika jenis...'; }
    if(actionType === 'panen') { title = 'Panen'; btnColor = '#f59e0b'; document.getElementById('inputDetails').placeholder = 'Panen seberat...'; }
    if(actionType === 'pupuk') { title = 'Perawatan'; btnColor = '#0ea5e9'; document.getElementById('inputDetails').placeholder = 'Pupuk AB Mix...'; }
    if(actionType === 'rusak') { title = 'Tandai Rusak'; btnColor = '#ef4444'; document.getElementById('inputDetails').placeholder = 'Mati karena hama...'; }
    
    document.getElementById('actionTitle').innerText = title;
    document.getElementById('btnSubmitAction').style.background = btnColor;
    
    document.getElementById('modalActionPot').style.display = 'flex';
}
</script>
@endsection