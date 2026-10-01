<?php
$f = 'app/Http/Controllers/HydroponicController.php';
$c = file_get_contents($f);

$old = <<<'EOF'
                    $minAge = \Carbon\Carbon::parse($item->max_planted)->diffInDays(now()); // max planted_at = youngest
                    $maxAge = \Carbon\Carbon::parse($item->min_planted)->diffInDays(now()); // min planted_at = oldest
EOF;
$new = <<<'EOF'
                    $minAge = \Carbon\Carbon::parse($item->max_planted)->startOfDay()->diffInDays(now()->startOfDay()); // max planted_at = youngest
                    $maxAge = \Carbon\Carbon::parse($item->min_planted)->startOfDay()->diffInDays(now()->startOfDay()); // min planted_at = oldest
EOF;
$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Fixed diffInDays.\n";
