<?php
$f = 'routes/api.php';
$c = file_get_contents($f);

if (!str_contains($c, "master-tanaman', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'masterTanaman']")) {
    $c = str_replace(
        "Route::get('/konvensional/lahan/{id}'",
        "Route::get('/konvensional/master-tanaman', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'masterTanaman']);\n    Route::get('/konvensional/lahan/{id}'",
        $c
    );
    file_put_contents($f, $c);
}
echo "Added route.\n";
