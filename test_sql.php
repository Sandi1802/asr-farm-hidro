<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $count = DB::table('holes')
        ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
        ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, 30) * INTERVAL '1 day')")
        ->count();
    echo "Count 1: " . $count . "\n";
} catch (\Exception $e) {
    echo "Error 1: " . $e->getMessage() . "\n";
}

try {
    $count2 = DB::table('holes')
        ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
        ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day')", [30])
        ->count();
    echo "Count 2: " . $count2 . "\n";
} catch (\Exception $e) {
    echo "Error 2: " . $e->getMessage() . "\n";
}
