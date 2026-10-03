<?php
$file = 'resources/views/konvensional/dashboard.blade.php';
$content = file_get_contents($file);

$target = '{{-- GRAFIK TOP TANAMAN --}}';
$replacement = "@endif\n\n        {{-- GRAFIK TOP TANAMAN --}}";

if (!str_contains($content, '@endif'."\n\n".'        {{-- GRAFIK TOP TANAMAN --}}')) {
    $content = str_replace($target, $replacement, $content);
    file_put_contents($file, $content);
}
