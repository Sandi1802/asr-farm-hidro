<?php
$f = 'resources/views/konvensional/v2/zona.blade.php';
$c = file_get_contents($f);

$oldModalRegex = '/<form action="\{\{ route\(\'konven\.v2\.zona\.store\', \$kode->id\) \}\}" method="POST">.*?<\/form>/s';

$newModal = <<<'EOF'
<form action="{{ route('konven.v2.zona.store', $kode->id) }}" method="POST">
            @csrf
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Zona yang Dibuat *</label>
                <input type="number" name="jumlah_zona" required min="1" max="20" value="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Prefix Nama Zona *</label>
                <input type="text" name="prefix_nama" required value="Zona" placeholder="Contoh: Zona"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                <small style="color:var(--text-muted); font-size:0.75rem;">Sistem akan menambahkan angka di belakangnya (Misal: Zona 1, Zona 2).</small>
            </div>
            <div style="margin-bottom:1rem; padding-top:1rem; border-top:1px dashed var(--border-color);">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Bedengan per Zona</label>
                <input type="number" name="jumlah_bedengan" required min="0" max="50" value="5"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Lubang per Bedengan</label>
                <input type="number" name="jumlah_lubang" required min="0" max="500" value="50"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                <button type="button" onclick="document.getElementById('modalTambahZona').style.display='none'"
                        style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan & Generate</button>
            </div>
        </form>
EOF;

// We only replace the FIRST match (the create form), not the edit form if it matches somehow.
$c = preg_replace($oldModalRegex, $newModal, $c, 1);
file_put_contents($f, $c);

echo "View zona updated.\n";
