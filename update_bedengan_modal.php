<?php
$f = 'resources/views/konvensional/v2/bedengan.blade.php';
$c = file_get_contents($f);

$oldModal = <<<'EOF'
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Display</label>
                <input type="text" id="editBedenganNama" name="nama_display"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
EOF;

$newModal = <<<'EOF'
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Display</label>
                <input type="text" id="editBedenganNama" name="nama_display"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1.25rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Lubang Tanam</label>
                <input type="number" id="editBedenganJumlah" name="jumlah_lubang" min="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                <small style="color:var(--text-muted); display:block; margin-top:4px;">Ubah angka ini untuk menambah/mengurangi lubang pada bedengan ini.</small>
            </div>
EOF;

$c = str_replace($oldModal, $newModal, $c);

// Also need to pass the jumlah_lubang to JS
$oldJs = "function openEditBedengan(id, nomor, nama) {";
$newJs = "function openEditBedengan(id, nomor, nama, jumlah) {\n    document.getElementById('editBedenganJumlah').value = jumlah;";
$c = str_replace($oldJs, $newJs, $c);

// Also update the button call
$oldBtn = "openEditBedengan({{ \$bedengan->id }}, {{ \$bedengan->nomor }}, '{{ addslashes(\$bedengan->nama_display ?? '') }}')";
$newBtn = "openEditBedengan({{ \$bedengan->id }}, {{ \$bedengan->nomor }}, '{{ addslashes(\$bedengan->nama_display ?? '') }}', {{ \$bedengan->lubangTanams->count() }})";
$c = str_replace($oldBtn, $newBtn, $c);

file_put_contents($f, $c);
echo "View updated.\n";
