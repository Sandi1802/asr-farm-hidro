<?php
$f = 'resources/views/konvensional/v2/lahan.blade.php';
$c = file_get_contents($f);

$c = str_replace(
    '<th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Jumlah Kode</th>',
    '<th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Jumlah Bedengan</th>',
    $c
);

$c = str_replace(
    '<td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ $lahan->kodes_count }} kode</td>',
    '<td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ $lahan->total_bedengan }} bedengan</td>',
    $c
);

file_put_contents($f, $c);
echo "View updated.\n";
