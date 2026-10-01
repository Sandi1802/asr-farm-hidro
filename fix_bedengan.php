<?php
$f = 'resources/views/konvensional/v2/bedengan.blade.php';
$c = file_get_contents($f);

$oldModalRegex = '/<div style="margin-bottom:1rem;">\s*<label.*?Nomor\s*Bedengan.*?>.*?<\/label>.*?<input\s*type="number"\s*name="nomor".*?<\/div>.*?<div style="margin-bottom:1rem;">\s*<label.*?Nama\s*Display.*?>.*?<\/label>.*?<input\s*type="text"\s*name="nama_display".*?<\/div>/s';

$newModal = <<<'EOF'
<div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Bedengan (Dibuat Sekaligus) *</label>
                <input type="number" name="jumlah_bedengan" required min="1" max="500" value="1"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                <small style="color:var(--text-muted); font-size:0.75rem;">Nomor bedengan akan otomatis meneruskan nomor terakhir.</small>
            </div>
EOF;

$c = preg_replace($oldModalRegex, $newModal, $c, 1);
file_put_contents($f, $c);
echo "View bedengan updated.\n";
