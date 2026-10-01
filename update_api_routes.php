<?php
$f = 'routes/api.php';
$c = file_get_contents($f);

$old = "Route::get('/konvensional/dashboard', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'dashboard']);";

$new = <<<'EOF'
    Route::get('/konvensional/dashboard', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'dashboard']);
    Route::get('/konvensional/lahan/{id}', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'detailLahan']);
    Route::get('/konvensional/kode/{id}', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'detailKode']);
    Route::get('/konvensional/zona/{id}', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'detailZona']);
    Route::get('/konvensional/bedengan/{id}', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'detailBedengan']);
    
    // Write Endpoints
    Route::post('/konvensional/bedengan/{id}/tanam', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'tanam']);
    Route::post('/konvensional/bedengan/{id}/panen', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'panen']);
    Route::post('/konvensional/bedengan/{id}/perawatan', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'perawatan']);
    Route::post('/konvensional/bedengan/{id}/rusak', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'laporRusak']);
EOF;

$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Routes updated.\n";
