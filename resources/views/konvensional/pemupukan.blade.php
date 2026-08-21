@extends('layouts.app')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
    <div>
        <h1 style="margin: 0; font-size: 1.5rem; color: var(--text-color);">Riwayat Pemupukan</h1>
        <p style="margin: 0.25rem 0 0; color: var(--text-muted);">Jadwal dan catatan pemupukan tanaman konvensional</p>
    </div>
    <button onclick="document.getElementById('modalTambah').style.display='flex'" class="btn" style="background: var(--asr-green); color: white; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer;">
        <i class="ph ph-plus"></i> Tambah Catatan
    </button>
</div>

@if(session('success'))
<div style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
    {{ session('success') }}
</div>
@endif

<div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;">
    <table class="table datatable" style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead style="background: #f8fafc; border-bottom: 1px solid var(--border-color);">
            <tr>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Tanggal</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Lokasi Lahan & Bedengan</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Nama Pupuk</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Dosis</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem;">Pekerja</th>
                <th style="padding: 1rem; color: var(--text-muted); font-weight: 500; font-size: 0.875rem; text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pemupukan as $p)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem;">
                    {{ date('d M Y', strtotime($p->tanggal)) }}
                </td>
                <td style="padding: 1rem;">
                    <strong>{{ $p->lahan->nama_lahan }}</strong>
                    @if($p->bedengan)
                        <br><span style="font-size: 0.8rem; color: var(--text-muted);">{{ $p->bedengan->nama_bedengan }}</span>
                    @endif
                </td>
                <td style="padding: 1rem; font-weight: 500;">
                    {{ $p->nama_pupuk }}
                </td>
                <td style="padding: 1rem;">
                    {{ $p->dosis }}
                </td>
                <td style="padding: 1rem;">
                    {{ $p->nama_pekerja ?? '-' }}
                </td>
                <td style="padding: 1rem; text-align: right; display: flex; justify-content: flex-end; gap: 0.5rem;">
                    <form action="{{ route('konvensional.pemupukan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus riwayat ini?');" style="margin: 0; display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: #fee2e2; color: #991b1b; border: none; padding: 0.4rem 0.5rem; border-radius: 6px; cursor: pointer; font-size: 0.9rem;">
                            <i class="ph ph-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah -->
<div id="modalTambah" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 500px; padding: 1.5rem; margin: 1rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="margin: 0;">Catat Pemupukan Baru</h3>
            <button onclick="document.getElementById('modalTambah').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;"><i class="ph ph-x"></i></button>
        </div>
        
        <form action="{{ route('konvensional.pemupukan') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Lahan *</label>
                <select name="lahan_id" id="selectLahan" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" onchange="updateBedenganDropdown()">
                    <option value="">-- Pilih Lahan --</option>
                    @foreach($lahans as $lahan)
                        <option value="{{ $lahan->id }}">{{ $lahan->nama_lahan }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Bedengan (Opsional)</label>
                <select name="bedengan_id" id="selectBedengan" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;">
                    <option value="">-- Semua Bedengan / Keseluruhan Lahan --</option>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Nama Pupuk *</label>
                <input type="text" name="nama_pupuk" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Contoh: NPK Mutiara">
            </div>
            
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Dosis *</label>
                <input type="text" name="dosis" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Contoh: 100 gram/lubang atau 5 kg/bedengan">
            </div>
            
            <div style="margin-bottom: 1rem; display: flex; gap: 1rem;">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Tanggal *</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-color);">Pekerja</label>
                    <input type="text" name="nama_pekerja" style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 6px;" placeholder="Opsional">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                <button type="button" onclick="document.getElementById('modalTambah').style.display='none'" style="padding: 0.75rem 1.5rem; border: 1px solid var(--border-color); background: white; border-radius: 6px; cursor: pointer;">Batal</button>
                <button type="submit" style="padding: 0.75rem 1.5rem; border: none; background: var(--asr-green); color: white; border-radius: 6px; cursor: pointer;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const dataLahan = @json($lahans);
    
    function updateBedenganDropdown() {
        const lahanId = document.getElementById('selectLahan').value;
        const selectBedengan = document.getElementById('selectBedengan');
        
        // Reset
        selectBedengan.innerHTML = '<option value="">-- Semua Bedengan / Keseluruhan Lahan --</option>';
        
        if(lahanId) {
            const lahan = dataLahan.find(l => l.id == lahanId);
            if(lahan && lahan.bedengan) {
                lahan.bedengan.forEach(bedengan => {
                    const opt = document.createElement('option');
                    opt.value = bedengan.id;
                    opt.textContent = bedengan.nama_bedengan;
                    selectBedengan.appendChild(opt);
                });
            }
        }
    }
</script>
@endsection
