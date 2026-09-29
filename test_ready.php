<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$defaultDays = 30;
$res = \App\Models\Hole::leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
    ->where('holes.status', 'ditanam')
    ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day')", [$defaultDays])
    ->get(['holes.id', 'holes.planted_at', 'plant_types.growth_days']);
echo $res->toJson();
