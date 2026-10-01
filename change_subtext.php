<?php
$f = 'resources/views/konvensional/v2/zona.blade.php';
$c = file_get_contents($f);

$c = str_replace('Kelola zona dalam kode {{ $kode->kode }}', 'Kelola zona di lahan {{ $kode->lahan ? $kode->lahan->nama : ($kode->posisi ? $kode->posisi->lahan->nama : $kode->kode) }}', $c);

file_put_contents($f, $c);
echo "Subtext updated.\n";
