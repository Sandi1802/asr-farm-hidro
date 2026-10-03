<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$lahan = \App\Models\KonvenLahanV2::create(['nama' => 'Lahan Test']);
$kode = \App\Models\KonvenKodeV2::create(['lahan_id' => $lahan->id, 'kode' => 'A1', 'nomor_urut' => 1]);
$zona = \App\Models\KonvenZonaV2::create(['kode_id' => $kode->id, 'nama' => 'Zona 1']);
$bedengan = \App\Models\KonvenBedenganV2::create(['zona_id' => $zona->id, 'nomor' => 1, 'jumlah_lubang_rencana' => 10]);

for ($i = 1; $i <= 5; $i++) {
    \App\Models\KonvenLubangTanamV2::create([
        'bedengan_id' => $bedengan->id,
        'nomor_lubang' => $i,
        'status' => 'ditanam',
        'plant_name' => 'Bayam',
        'planted_at' => now(),
        'estimated_harvest_at' => now()->addDays(20)
    ]);
}

echo "Created test data.\n";
