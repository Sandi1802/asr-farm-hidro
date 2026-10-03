<?php

$file = 'app/Http/Controllers/Api/KonvensionalApiController.php';
$content = file_get_contents($file);
$content = str_replace("\r\n", "\n", $content);

// 1. Fix duplicate plant names in combined for tanamZona
$targetCombined = <<<'PHP'
        $combined = $r->plant_name_1;
        if ($r->filled('plant_name_2')) {
            $combined .= ', ' . $r->plant_name_2;
        }
PHP;

$replacementCombined = <<<'PHP'
        $combined = trim($r->plant_name_1);
        if ($r->filled('plant_name_2') && trim($r->plant_name_2) !== trim($r->plant_name_1)) {
            $combined .= ', ' . trim($r->plant_name_2);
        }
PHP;

$content = str_replace($targetCombined, $replacementCombined, $content);

// There's also `tanam` method in API?
$targetTanam = <<<'PHP'
        $combined = $r->plant_name_1;
        if ($r->filled('plant_name_2')) {
            $combined .= ', ' . $r->plant_name_2;
        }
PHP;

// Both uses same string so it should replace both!

file_put_contents($file, $content);
echo "Fixed KonvensionalApiController.php\n";
