<?php
$f = 'D:/ASR GROUP/ASR-APPS/ASR_GREEN_WEB/routes/api.php';
$c = file_get_contents($f);

$find = 'Route::post(\'/racks/{id}/update-age\', [RackApiController::class, \'updateAge\']);';
$rep = '// Route::post(\'/racks/{id}/update-age\', [RackApiController::class, \'updateAge\']);';
$c = str_replace($find, $rep, $c);

$find2 = 'Route::post(\'/racks/{id}/damage\', [RackApiController::class, \'damage\']);';
$rep2 = "Route::post('/racks/{id}/damage', [RackApiController::class, 'damage']);\n});\nRoute::post('/racks/{id}/update-age', [\App\Http\Controllers\Api\RackApiController::class, 'updateAge']);\nRoute::middleware('api.role:produksi,produksi_gh')->group(function () {\n";
$c = str_replace($find2, $rep2, $c);

file_put_contents($f, $c);
echo "Done";
