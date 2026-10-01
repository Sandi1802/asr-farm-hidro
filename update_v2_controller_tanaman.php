<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

// Restore original
$c = file_get_contents('app/Http/Controllers/KonvenV2Controller.php');
// I need to be careful. Let's just use regex.

$c = preg_replace(
    '/\$lubangStats = \[/', 
    "\$masterTanaman = \App\Models\KonvenTanaman::orderBy('nama')->get();\n        \$lubangStats = [", 
    $c
);

$c = preg_replace(
    "/compact\('bedengan', 'lubangStats'\)/", 
    "compact('bedengan', 'lubangStats', 'masterTanaman')", 
    $c
);

file_put_contents($f, $c);
echo "KonvenV2Controller updated safely.\n";
