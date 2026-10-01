<?php
$f = 'routes/api.php';
$c = file_get_contents($f);

if (!str_contains($c, "zona/{id}/tanam'")) {
    $c = str_replace(
        "Route::post('/konvensional/bedengan/{id}/tanam'",
        "Route::post('/konvensional/zona/{id}/tanam', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'tanamZona']);\n    Route::post('/konvensional/bedengan/{id}/tanam'",
        $c
    );
    file_put_contents($f, $c);
}
echo "Added route.\n";
