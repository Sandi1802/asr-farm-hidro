<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PaprikaGh;
use App\Models\PaprikaBaris;
use App\Models\PaprikaPot;
use Carbon\Carbon;

// Check if dummy GH already exists
$gh = PaprikaGh::where('nama_gh', 'GH Paprika Dummy')->first();
if (!$gh) {
    $gh = PaprikaGh::create([
        'nama_gh' => 'GH Paprika Dummy',
        'keterangan' => 'GH ini hanya untuk testing dan review'
    ]);
}

// Create Baris
$barisA = PaprikaBaris::firstOrCreate([
    'gh_id' => $gh->id,
    'nama_baris' => 'Baris A (Testing)'
]);

$barisB = PaprikaBaris::firstOrCreate([
    'gh_id' => $gh->id,
    'nama_baris' => 'Baris B (Testing)'
]);

// Check if pots exist for Baris A
if (PaprikaPot::where('baris_id', $barisA->id)->count() == 0) {
    for ($i = 1; $i <= 5; $i++) {
        PaprikaPot::create([
            'baris_id' => $barisA->id,
            'nomor_pot' => $i,
            'status' => 'ditanam',
            'plant_name' => 'Paprika Merah',
            'planted_at' => Carbon::now()->subDays(10),
            'estimated_harvest_at' => Carbon::now()->addDays(80)
        ]);
    }
    for ($i = 6; $i <= 10; $i++) {
        PaprikaPot::create([
            'baris_id' => $barisA->id,
            'nomor_pot' => $i,
            'status' => 'kosong',
        ]);
    }
}

// Check if pots exist for Baris B
if (PaprikaPot::where('baris_id', $barisB->id)->count() == 0) {
    for ($i = 1; $i <= 3; $i++) {
        PaprikaPot::create([
            'baris_id' => $barisB->id,
            'nomor_pot' => $i,
            'status' => 'panen',
            'plant_name' => 'Paprika Kuning',
            'planted_at' => Carbon::now()->subDays(90),
            'estimated_harvest_at' => Carbon::now()
        ]);
    }
    for ($i = 4; $i <= 8; $i++) {
        PaprikaPot::create([
            'baris_id' => $barisB->id,
            'nomor_pot' => $i,
            'status' => 'ditanam',
            'plant_name' => 'Paprika Kuning',
            'planted_at' => Carbon::now()->subDays(10),
            'estimated_harvest_at' => Carbon::now()->addDays(80)
        ]);
    }
}

echo "Dummy data generated successfully!\n";
