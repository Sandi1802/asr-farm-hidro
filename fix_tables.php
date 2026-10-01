<?php
$f = 'app/Http/Controllers/KonvensionalController.php';
$c = file_get_contents($f);

// Fix table names
$c = str_replace('konven_lahans_v2', 'konven_lahans', $c);
$c = str_replace('konven_kodes_v2', 'konven_kodes', $c);
$c = str_replace('konven_zonas_v2', 'konven_zonas', $c);
$c = str_replace('konven_bedengans_v2', 'konven_bedengans', $c);
$c = str_replace('konven_lubang_tanams_v2', 'konven_lubang_tanams', $c);

file_put_contents($f, $c);
echo "Table names fixed.\n";
