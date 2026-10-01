<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);
$c = str_replace("'label' => 'Gagal Panen'", "'label' => 'Rusak Tanaman'", $c);
$c = str_replace("Laporan Gagal Panen (Rusak)", "Laporan Rusak Tanaman", $c);
file_put_contents($f, $c);
echo "Done replacing label.\n";
