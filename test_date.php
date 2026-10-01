<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count1 = DB::table('holes')
        ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
        ->where('holes.status', 'ditanam')
        ->whereNotNull('holes.planted_at')
        ->whereRaw("holes.planted_at <= NOW() - ((COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')")
        ->count();
    
    $count2 = DB::table('holes')
        ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
        ->where('holes.status', 'ditanam')
        ->whereNotNull('holes.planted_at')
        ->whereRaw("DATE(holes.planted_at + ((COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')) <= CURRENT_DATE")
        ->count();

    echo "Count NOW: " . $count1 . "\n";
    echo "Count DATE: " . $count2 . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
