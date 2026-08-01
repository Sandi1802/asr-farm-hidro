<?php

require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PlantType;

$plants = PlantType::where('growth_days', '>', 0)->get();

echo "Memproses {$plants->count()} jenis tanaman...\n";

foreach ($plants as $p) {
    $t  = $p->growth_days;
    $s  = max(1, (int) round($t * 0.20));
    $ta = max(1, (int) round($t * 0.30));
    $r  = max(1, (int) round($t * 0.25));
    $d  = $t - $s - $ta - $r;
    if ($d < 1) $d = 1;

    $p->update([
        'semai_days'  => $s,
        'tanam_days'  => $ta,
        'remaja_days' => $r,
        'dewasa_days' => $d,
    ]);

    echo "✓ {$p->name} — Semai:{$s} Tanam:{$ta} Remaja:{$r} Dewasa:{$d} (Total:{$t})\n";
}

echo "\nSelesai! Semua data tanaman sudah diperbarui.\n";
