<?php
$f = 'resources/views/konvensional/dashboard.blade.php';
$c = file_get_contents($f);

$c = preg_replace('/<script src="https:\/\/cdn\.jsdelivr\.net\/npm\/chart\.js">\s*(if \(typeof initCalendar)/s', "<script src=\"https://cdn.jsdelivr.net/npm/chart.js\"></script>\n<script>\n    $1", $c);

file_put_contents($f, $c);
echo "Script tag regex fixed.\n";
