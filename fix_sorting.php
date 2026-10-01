<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

// Fix kodeByLahan
$c = str_replace(
    "->orderBy('kode')",
    "->orderByRaw('LENGTH(kode), kode')",
    $c
);

// Fix zonaIndex
$c = str_replace(
    "->orderBy('nama')",
    "->orderByRaw('LENGTH(nama), nama')",
    $c
);

file_put_contents($f, $c);
echo "Sorting updated.\n";
