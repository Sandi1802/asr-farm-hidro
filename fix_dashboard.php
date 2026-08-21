<?php
$hydroponicPath = resource_path('views/hydroponics/dashboard.blade.php');
$konvensionalPath = resource_path('views/konvensional/dashboard.blade.php');

$hydroContent = file_get_contents($hydroponicPath);
$konvContent = file_get_contents($konvensionalPath);

// Extract the calendar style block from hydroponics dashboard
// It's the one containing .cal-grid
preg_match('/<style>.*?\.cal-grid.*?<\/style>/s', $hydroContent, $matches);
if (!empty($matches)) {
    $calendarStyle = $matches[0];
    // Strip <style> and </style> from it
    $calendarStyle = str_replace(['<style>', '</style>'], '', $calendarStyle);
    
    // Inject it into konvensional's existing <style> block
    if (strpos($konvContent, $calendarStyle) === false) {
        $konvContent = str_replace('</style>', "\n" . $calendarStyle . "\n</style>", $konvContent);
    }
}

// Clean up duplicate responsive-grid-cal
$konvContent = preg_replace('/<div class="responsive-grid-cal">\s*{{-- CALENDAR \+ DAILY SCHEDULE \(2-col\) AT TOP --}}\s*<div class="responsive-grid-cal">/', '{{-- CALENDAR + DAILY SCHEDULE (2-col) AT TOP --}}'."\n".'<div class="responsive-grid-cal">', $konvContent);

file_put_contents($konvensionalPath, $konvContent);
echo "Fixed successfully\n";
