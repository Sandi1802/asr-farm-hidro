<?php
$f = 'routes/web.php';
$c = file_get_contents($f);

$oldRoute = "Route::get('/lahan', [\App\Http\Controllers\KonvenV2Controller::class, 'lahanIndex'])->name('konven.v2.lahan');";
$newRoute = "Route::get('/logs', [\App\Http\Controllers\KonvenLogController::class, 'index'])->name('konven.v2.logs');\n    Route::get('/lahan', [\App\Http\Controllers\KonvenV2Controller::class, 'lahanIndex'])->name('konven.v2.lahan');";

$c = str_replace($oldRoute, $newRoute, $c);
file_put_contents($f, $c);
echo "web.php updated.\n";
