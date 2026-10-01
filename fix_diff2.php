<?php
$f = 'app/Http/Controllers/HydroponicController.php';
$c = file_get_contents($f);
$c = preg_replace("/\\\$minAge = \\\\Carbon\\\\Carbon::parse\(\\\$item->max_planted\)->diffInDays\(now\(\)\);/s", "\$minAge = \Carbon\Carbon::parse(\$item->max_planted)->startOfDay()->diffInDays(now()->startOfDay());", $c);
$c = preg_replace("/\\\$maxAge = \\\\Carbon\\\\Carbon::parse\(\\\$item->min_planted\)->diffInDays\(now\(\)\);/s", "\$maxAge = \Carbon\Carbon::parse(\$item->min_planted)->startOfDay()->diffInDays(now()->startOfDay());", $c);
file_put_contents($f, $c);
echo "Fixed diffInDays 2.\n";
