<?php
$file = 'app/Http/Controllers/HydroponicController.php';
$content = file_get_contents($file);

$old1 = "COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day'";
$new1 = "(COALESCE(plant_types.growth_days, ?) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day'";
$content = str_replace($old1, $new1, $content);

$old2 = "COALESCE(plant_types.growth_days, 30) * INTERVAL '1 day'";
$new2 = "(COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day'";
$content = str_replace($old2, $new2, $content);

file_put_contents($file, $content);
echo "Replaced everywhere.\n";
