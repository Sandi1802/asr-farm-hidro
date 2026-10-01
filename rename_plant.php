<?php
$f='resources/views/hydroponics/damage-notes.blade.php';
$c=file_get_contents($f);
$c=str_replace('Catatan Kerusakan', 'Kerusakan Tanaman', $c);
$c=str_replace('Daftar Kerusakan Tanaman', 'Daftar Kerusakan Tanaman', $c);
file_put_contents($f, $c);
