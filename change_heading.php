<?php
$f = 'resources/views/konvensional/v2/zona.blade.php';
$c = file_get_contents($f);

// Change heading
$lahanNameCode = '{{ $kode->lahan ? $kode->lahan->nama : ($kode->posisi ? $kode->posisi->lahan->nama : "Kode " . $kode->kode) }}';
$c = preg_replace('/Zona .* Kode \{\{ \$kode->kode \}\}/u', 'Zona — ' . $lahanNameCode, $c);

// Change title
$c = preg_replace("/@section\('title', 'Zona .* ' \. \\\$kode->kode\)/u", "@section('title', 'Zona — ' . (\$kode->lahan ? \$kode->lahan->nama : (\$kode->posisi ? \$kode->posisi->lahan->nama : \$kode->kode)))", $c);

file_put_contents($f, $c);
echo "Zona updated.\n";

$f = 'resources/views/konvensional/v2/bedengan.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/Bedengan .* Kode \{\{ \$kode->kode \}\}/u', 'Bedengan — ' . $lahanNameCode, $c);
file_put_contents($f, $c);
echo "Bedengan updated.\n";
