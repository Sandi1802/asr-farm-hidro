<?php
$f = 'database/migrations/2026_10_01_091121_create_asset_damage_notes_table.php';
$c = file_get_contents($f);

$c = preg_replace_callback("/Schema::create\('([^']+)', function \(Blueprint \\\$table\) \{(.*?)\}\);/s", function ($m) {
    return "if (!Schema::hasTable('{$m[1]}')) {\n            Schema::create('{$m[1]}', function (Blueprint \$table) {{$m[2]}});\n        }";
}, $c);

file_put_contents($f, $c);
echo "Asset migration fixed.\n";
