<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

// Implement redirect if only 1 kode
$c = preg_replace(
    "/public function kodeByLahan\(\\\$lahan_id\)\s*\{\s*\\\$lahan = KonvenLahanV2::findOrFail\(\\\$lahan_id\);\s*\\\$kodes = KonvenKodeV2::where\('lahan_id', \\\$lahan_id\)\s*->withCount\('zonas'\)\s*->orderByRaw\('LENGTH\(kode\), kode'\)\s*->get\(\);/s",
    "public function kodeByLahan(\$lahan_id)\n    {\n        \$lahan = KonvenLahanV2::findOrFail(\$lahan_id);\n        \$kodes = KonvenKodeV2::where('lahan_id', \$lahan_id)\n                             ->withCount('zonas')\n                             ->orderByRaw('LENGTH(kode), kode')\n                             ->get();\n\n        if (\$kodes->count() === 1 && !request()->has('manage')) {\n            return redirect()->route('konven.v2.zona', \$kodes->first()->id);\n        }",
    $c
);

file_put_contents($f, $c);

// Update breadcrumb in zona.blade.php
$fz = 'resources/views/konvensional/v2/zona.blade.php';
$cz = file_get_contents($fz);
$cz = str_replace(
    "route('konven.v2.kode.bylahan', \$kode->lahan->id)",
    "route('konven.v2.kode.bylahan', \$kode->lahan->id) . '?manage=1'",
    $cz
);
file_put_contents($fz, $cz);

echo "Bypass applied.\n";
