<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);
$old = "\$totalDamage = \App\Models\AssetDamageNote::where('status','!=','resolved')->count();";
$new = <<<'EOF'
try {
            $totalDamage = \App\Models\AssetDamageNote::where('status','!=','resolved')->count();
        } catch (\Exception $e) {
            $totalDamage = 0; // Fallback jika belum di-migrate
        }
EOF;
$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Added try-catch to dashboard.\n";
