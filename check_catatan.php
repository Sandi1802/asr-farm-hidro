<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

// Check if column exists
$hasColumn = \Schema::hasColumn('racks', 'catatan_lapangan');
echo "Column catatan_lapangan exists: " . ($hasColumn ? 'YES' : 'NO') . PHP_EOL;

// Get some racks with catatan
$racks = \App\Models\Rack::whereNotNull('catatan_lapangan')
    ->where('catatan_lapangan', '!=', '')
    ->take(5)
    ->get(['id', 'name', 'catatan_lapangan']);
    
echo "Racks with catatan_lapangan (" . $racks->count() . "):" . PHP_EOL;
foreach ($racks as $r) {
    echo "  ID:{$r->id} Name:{$r->name} Catatan: {$r->catatan_lapangan}" . PHP_EOL;
}

if ($racks->isEmpty()) {
    echo "No racks have catatan_lapangan yet. Need to input from mobile app first." . PHP_EOL;
}
