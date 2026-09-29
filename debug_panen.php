<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$t_month=9;
$t_year=2026;
$today = now()->format('Y-m-d');
$yesterday = now()->subDay()->format('Y-m-d');

echo "TODAY: $today\n";
echo "YESTERDAY: $yesterday\n";

$holeHarvested = \App\Models\Hole::whereNotNull('plant_name')
    ->whereNotNull('harvested_at')
    ->whereMonth('harvested_at', $t_month)
    ->whereYear('harvested_at', $t_year)
    ->get(['id', 'plant_name', 'harvested_at']);
    
echo "HOLES COUNT: " . $holeHarvested->count() . "\n";
foreach($holeHarvested as $h) {
    if (strpos($h->plant_name, 'Pakcoy') !== false) {
        echo "HOLE PAKCOY: ID {$h->id}, Date: {$h->harvested_at}\n";
    }
}

$logs = \App\Models\MaintenanceLog::where('action_type', 'panen')
    ->whereMonth('created_at', $t_month)
    ->whereYear('created_at', $t_year)
    ->get(['id', 'details', 'created_at']);
    
echo "LOGS COUNT: " . $logs->count() . "\n";
foreach($logs as $l) {
    $det = json_decode($l->details);
    if (($det->plant_name ?? '') === 'Pakcoy') {
        echo "LOG PAKCOY: ID {$l->id}, Qty: " . ($det->jumlah ?? 0) . ", Date: {$l->created_at}\n";
    }
}
