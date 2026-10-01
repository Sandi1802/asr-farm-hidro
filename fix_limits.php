<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

// Increase validation limits
$c = str_replace("'jumlah_zona'     => 'required|integer|min:1|max:20'", "'jumlah_zona'     => 'required|integer|min:1|max:500'", $c);
$c = str_replace("'jumlah_bedengan' => 'required|integer|min:0|max:50'", "'jumlah_bedengan' => 'required|integer|min:0|max:500'", $c);
$c = str_replace("'jumlah_lubang'   => 'required|integer|min:0|max:500'", "'jumlah_lubang'   => 'required|integer|min:0|max:2000'", $c);

file_put_contents($f, $c);

$f2 = 'resources/views/konvensional/v2/zona.blade.php';
$c2 = file_get_contents($f2);

// Update HTML inputs
$c2 = str_replace('name="jumlah_zona" required min="1" max="20"', 'name="jumlah_zona" required min="1" max="500"', $c2);
$c2 = str_replace('name="jumlah_bedengan" required min="0" max="50"', 'name="jumlah_bedengan" required min="0" max="500"', $c2);
$c2 = str_replace('name="jumlah_lubang" required min="0" max="500"', 'name="jumlah_lubang" required min="0" max="2000"', $c2);

file_put_contents($f2, $c2);

echo "Limits increased.\n";
