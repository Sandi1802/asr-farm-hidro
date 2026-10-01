<?php
$file = 'app/Http/Controllers/HydroponicController.php';
$content = file_get_contents($file);

$content = str_replace(
    "\$readyToHarvestItems = Hole::with(['row.rack.greenhouse'])\n            ->whereIn('id', \$readyIds)\n            ->get()\n            ->groupBy('plant_name');",
    "\$readyToHarvestItems = Hole::with(['row.rack.greenhouse'])\n            ->whereIn('id', \$readyIds)\n            ->get()\n            ->groupBy(function(\$item) { return \$this->normalizePlantName(\$item->plant_name); });",
    $content
);

$content = str_replace(
    "\$groupedHoles = \$readyHoles->groupBy('plant_name');",
    "\$groupedHoles = \$readyHoles->groupBy(function(\$item) { return \$this->normalizePlantName(\$item->plant_name); });",
    $content
);

$content = str_replace(
    "\$readyTypesCount = \$readyHolesHist->whereNotNull('plant_name')->groupBy('plant_name')->count();",
    "\$readyTypesCount = \$readyHolesHist->whereNotNull('plant_name')->groupBy(function(\$item) { return \$this->normalizePlantName(\$item->plant_name); })->count();",
    $content
);

$content = str_replace(
    "\$groupedHoles = \$readyData->groupBy('plant_name');",
    "\$groupedHoles = \$readyData->groupBy(function(\$item) { return \$this->normalizePlantName(\$item->plant_name); });",
    $content
);


file_put_contents($file, $content);
echo "Replaced groupBy.\n";
