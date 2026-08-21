<?php
$hydroponicPath = resource_path('views/hydroponics/dashboard.blade.php');
$konvensionalPath = resource_path('views/konvensional/dashboard.blade.php');

$hydroContent = file_get_contents($hydroponicPath);
$konvContent = file_get_contents($konvensionalPath);

// Extract the <style> block from hydroponics dashboard
preg_match('/<style>.*?<\/style>/s', $hydroContent, $matches);
if (!empty($matches)) {
    $styleBlock = $matches[0];
    
    // Check if it already has a style block, if not prepend it to the content section
    if (strpos($konvContent, '<style>') === false) {
        $konvContent = str_replace('@section(\'content\')', "@section('content')\n\n" . $styleBlock, $konvContent);
        file_put_contents($konvensionalPath, $konvContent);
        echo "Style block copied successfully.\n";
    } else {
        echo "Style block already exists.\n";
    }
} else {
    echo "Could not find style block in hydroponics dashboard.\n";
}
