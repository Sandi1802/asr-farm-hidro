<?php
$file = "routes/web.php";
$content = file_get_contents($file);
$routes = "
    // Paprika Routes
    Route::prefix('paprika')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\PaprikaController::class, 'dashboard'])->name('paprika.dashboard');
        Route::get('/greenhouses', [\App\Http\Controllers\PaprikaController::class, 'greenhouses'])->name('paprika.greenhouses');
        Route::get('/greenhouses/{id}', [\App\Http\Controllers\PaprikaController::class, 'greenhouseDetail'])->name('paprika.greenhouses.detail');
        Route::post('/greenhouses/bulk-update', [\App\Http\Controllers\PaprikaController::class, 'bulkUpdatePlants'])->name('paprika.greenhouses.bulk-update');
        Route::get('/pemupukan', [\App\Http\Controllers\PaprikaController::class, 'pemupukan'])->name('paprika.pemupukan');
        Route::post('/pemupukan', [\App\Http\Controllers\PaprikaController::class, 'storePemupukan'])->name('paprika.pemupukan.store');
        Route::get('/penyemprotan', [\App\Http\Controllers\PaprikaController::class, 'penyemprotan'])->name('paprika.penyemprotan');
        Route::post('/penyemprotan', [\App\Http\Controllers\PaprikaController::class, 'storePenyemprotan'])->name('paprika.penyemprotan.store');
    });
";

$search = "Route::post('/notifications/read', [\App\Http\Controllers\HydroponicController::class, 'markNotificationsRead']);";
$replace = $search . "\n" . $routes;
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

