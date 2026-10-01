<?php
$file = 'app/Http/Controllers/HydroponicController.php';
$content = file_get_contents($file);

$old1 = "holes.planted_at <= NOW() - ((COALESCE(plant_types.growth_days, ?) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')";
$new1 = "DATE(holes.planted_at + ((COALESCE(plant_types.growth_days, ?) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')) <= CURRENT_DATE";
$content = str_replace($old1, $new1, $content);

$old2 = "holes.planted_at <= NOW() - ((COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')";
$new2 = "DATE(holes.planted_at + ((COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')) <= CURRENT_DATE";
$content = str_replace($old2, $new2, $content);

$old3 = "holes.planted_at > NOW() - ((COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')";
$new3 = "DATE(holes.planted_at + ((COALESCE(plant_types.growth_days, 30) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')) > CURRENT_DATE";
$content = str_replace($old3, $new3, $content);


file_put_contents($file, $content);
echo "Replaced everywhere with DATE() logic.\n";
