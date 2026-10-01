<?php
$f = 'routes/web.php';
$c = file_get_contents($f);

$settingsRoutes = <<<'EOF'
            // Settings
            Route::get('/master-data/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
            Route::put('/master-data/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
EOF;

// Insert inside the IT Admin ONLY middleware block
// Find Route::get('/master-data/users',
$c = str_replace(
    "Route::get('/master-data/users'",
    $settingsRoutes . "\n\n            Route::get('/master-data/users'",
    $c
);

file_put_contents($f, $c);
echo "Routes updated.\n";
