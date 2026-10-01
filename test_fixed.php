<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = DB::table('holes')
        ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
        ->where('holes.status', 'ditanam')
        ->whereNotNull('holes.planted_at')
        ->whereRaw("holes.planted_at <= NOW() - ((COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')")
        ->count();
    echo "Count: " . $count . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
