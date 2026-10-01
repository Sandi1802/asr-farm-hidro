<?php
$file = 'app/Http/Controllers/HydroponicController.php';
$content = file_get_contents($file);

$content = str_replace(
    "\$pName = \$det->plant_name ?? 'Tidak Diketahui';",
    "\$pName = \$this->normalizePlantName(\$det->plant_name ?? 'Tidak Diketahui');",
    $content
);

$content = str_replace(
    "\$pName = \$r->plant_name ?: 'Tidak Diketahui';",
    "\$pName = \$this->normalizePlantName(\$r->plant_name ?: 'Tidak Diketahui');",
    $content
);

// For hole updates (which will save the normalized name to DB!)
$content = str_replace(
    "\$pName = \$request->filled('plant_name') ? \$request->plant_name : \$hole->plant_name;",
    "\$pName = \$this->normalizePlantName(\$request->filled('plant_name') ? \$request->plant_name : \$hole->plant_name);",
    $content
);

file_put_contents($file, $content);
echo "Replaced safely.\n";
