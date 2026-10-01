<?php
$f = 'resources/views/konvensional/dashboard.blade.php';
$c = file_get_contents($f);

$c = str_replace(
    "<script src=\"https://cdn.jsdelivr.net/npm/chart.js\">\n    if (typeof initCalendar === 'function') initCalendar();",
    "<script src=\"https://cdn.jsdelivr.net/npm/chart.js\"></script>\n<script>\n    if (typeof initCalendar === 'function') initCalendar();",
    $c
);

file_put_contents($f, $c);
echo "Script tag fixed.\n";
