<?php
$file = 'app/Http/Controllers/HydroponicController.php';
$content = file_get_contents($file);

$oldStr = '->whereRaw("EXTRACT(YEAR FROM (holes.planted_at + (COALESCE(plant_types.growth_days, ?) * INTERVAL \'1 day\'))) * 100 + EXTRACT(MONTH FROM (holes.planted_at + (COALESCE(plant_types.growth_days, ?) * INTERVAL \'1 day\'))) <= ?", [$defaultDays, $defaultDays, $year * 100 + $month])';
$newStr = '->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, ?) * INTERVAL \'1 day\')", [$defaultDays])';

$content = str_replace($oldStr, $newStr, $content);
file_put_contents($file, $content);
echo "Replaced successfully.\n";
