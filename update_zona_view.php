<?php
$f = 'resources/views/konvensional/v2/zona.blade.php';
$c = file_get_contents($f);

// 1. Add Tanam Button
$oldButtons = <<<EOF
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route('konven.v2.bedengan', \$zona->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-rows"></i> Kelola Bedengan</a>
EOF;

$newButtons = <<<EOF
                    <div style="display:flex; gap:0.5rem;">
                        <button onclick="openTanamMassalZona({{ \$zona->id }}, '{{ addslashes(\$zona->nama) }}', {{ \$zona->total_lubang - \$zona->terisi }})" style="padding:0.4rem 0.75rem; background:#0284c7; color:white; border:none; border-radius:6px; cursor:pointer; font-size:0.75rem; font-weight:600;"><i class="ph ph-plant"></i> Tanam</button>
                        <a href="{{ route('konven.v2.bedengan', \$zona->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-rows"></i> Bedengan</a>
EOF;

$c = str_replace($oldButtons, $newButtons, $c);

// 2. Add Modal & Script at the end before @endsection
$modalHtml = <<<EOF

{{-- Modal Tanam Massal Zona --}}
<div id="modalTanamZona" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:12px; width:100%; max-width:500px; padding:1.5rem; margin:1rem;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
            <h3 style="margin:0; font-size:1.1rem; color:var(--text-main); font-weight:700;"><i class="ph ph-plant"></i> Tanam di Zona <span id="tanamZonaName"></span></h3>
            <button onclick="document.getElementById('modalTanamZona').style.display='none'" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:var(--text-muted);">&times;</button>
        </div>
        <form method="POST" id="formTanamZona">
            @csrf
            
            <div style="margin-bottom:1rem; padding:0.75rem; background:#e0f2fe; color:#0369a1; border-radius:8px; font-size:0.85rem; font-weight:500;">
                Tersedia <span id="sisaKosongZona">0</span> titik tanam kosong di Zona ini.
            </div>

            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanaman Utama *</label>
                <select name="plant_name_1" required style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                    <option value="" disabled selected>- Pilih Tanaman Utama -</option>
                    @foreach(\App\Models\KonvenTanaman::orderBy('nama')->get() as \$t)
                        <option value="{{ \$t->nama_lengkap }}">{{ \$t->nama_lengkap }} ({{ \$t->lama_hari_ke_panen }} Hari)</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanaman Tumpang Sari (Opsional)</label>
                <select name="plant_name_2" style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                    <option value="">- Tidak Ada Tambahan -</option>
                    @foreach(\App\Models\KonvenTanaman::orderBy('nama')->get() as \$t)
                        <option value="{{ \$t->nama_lengkap }}">{{ \$t->nama_lengkap }} ({{ \$t->lama_hari_ke_panen }} Hari)</option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Tanaman *</label>
                <input type="number" name="jumlah_tanaman" id="jumlahTanamZona" required min="1" placeholder="Berapa tanaman?"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                <small style="color:var(--text-muted); font-size:0.75rem;">Akan didistribusikan secara acak ke lubang kosong di bedengan-bedengan zona ini.</small>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; margin-bottom:1.5rem;">
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanggal Tanam *</label>
                    <input type="date" name="planted_at" required value="{{ date('Y-m-d') }}"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Estimasi Panen</label>
                    <input type="date" name="estimated_harvest_at"
                           style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTanamZona').style.display='none'" style="padding:0.6rem 1rem; background:#f1f5f9; color:var(--text-muted); border:none; border-radius:8px; cursor:pointer; font-weight:600;">Batal</button>
                <button type="submit" style="padding:0.6rem 1.5rem; background:var(--asr-green); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openTanamMassalZona(id, nama, sisaKosong) {
    if (sisaKosong <= 0) {
        alert('Tidak ada lubang kosong di zona ini!');
        return;
    }
    document.getElementById('formTanamZona').action = '/konvensional/v2/zona/' + id + '/tanam-massal';
    document.getElementById('tanamZonaName').innerText = nama;
    document.getElementById('sisaKosongZona').innerText = sisaKosong;
    document.getElementById('jumlahTanamZona').max = sisaKosong;
    document.getElementById('jumlahTanamZona').value = sisaKosong; // default isi semua yg kosong
    document.getElementById('modalTanamZona').style.display = 'flex';
}
</script>
EOF;

$c = preg_replace('/@endsection\s*$/', $modalHtml . "\n@endsection", $c);

file_put_contents($f, $c);
echo "zona.blade.php updated.\n";
