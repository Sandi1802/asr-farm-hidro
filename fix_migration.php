<?php
$f = 'database/migrations/2026_09_29_152000_create_konven_tables_new.php';
$c = file_get_contents($f);

// Wrap Schema::create with if (!Schema::hasTable)
$c = preg_replace_callback("/Schema::create\('([^']+)', function \(Blueprint \\\$table\) \{(.*?)\}\);/s", function ($m) {
    return "if (!Schema::hasTable('{$m[1]}')) {\n            Schema::create('{$m[1]}', function (Blueprint \$table) {{$m[2]}});\n        }";
}, $c);

file_put_contents($f, $c);
echo "Migration fixed.\n";
