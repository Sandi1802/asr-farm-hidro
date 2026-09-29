<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use Carbon\Carbon;

$month = now()->month;
$year = now()->year;
$today = now()->format('Y-m-d');
$yesterday = now()->subDay()->format('Y-m-d');

$harvestedTotals = [];

// 1. From Holes (currently harvested but not yet replanted)
$holeHarvested = \App\Models\Hole::whereNotNull('plant_name')
    ->whereNotNull('harvested_at')
    ->whereMonth('harvested_at', $month)
    ->whereYear('harvested_at', $year)
    ->get(['plant_name', 'harvested_at']);

foreach ($holeHarvested as $hole) {
    $pName = $hole->plant_name;
    if ($pName == 'Tidak Diketahui' || !$pName) continue;
    
    if (!isset($harvestedTotals[$pName])) {
        $harvestedTotals[$pName] = ['today' => 0, 'yesterday' => 0, 'month' => 0];
    }
    
    $harvestedTotals[$pName]['month']++;
    $date = Carbon::parse($hole->harvested_at)->format('Y-m-d');
    if ($date === $today) {
        $harvestedTotals[$pName]['today']++;
    } elseif ($date === $yesterday) {
        $harvestedTotals[$pName]['yesterday']++;
    }
}

// 2. From MaintenanceLog (historical harvest logs)
$allPanenLogs = \App\Models\MaintenanceLog::where('action_type', 'panen')
    ->whereMonth('created_at', $month)
    ->whereYear('created_at', $year)
    ->get(['details', 'created_at']);

foreach ($allPanenLogs as $log) {
    $det = json_decode($log->details);
    $pName = $det->plant_name ?? 'Tidak Diketahui';
    if ($pName == 'Tidak Diketahui' || !$pName) continue;

    $qty = (int) ($det->jumlah ?? 0);
    
    if (!isset($harvestedTotals[$pName])) {
        $harvestedTotals[$pName] = ['today' => 0, 'yesterday' => 0, 'month' => 0];
    }

    $harvestedTotals[$pName]['month'] += $qty;
    $date = Carbon::parse($log->created_at)->format('Y-m-d');
    if ($date === $today) {
        $harvestedTotals[$pName]['today'] += $qty;
    } elseif ($date === $yesterday) {
        $harvestedTotals[$pName]['yesterday'] += $qty;
    }
}

echo "Result:\n";
print_r($harvestedTotals);
