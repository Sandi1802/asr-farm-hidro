<?php
$f = 'routes/web.php';
$c = file_get_contents($f);

$old = "Route::get('/zona/{zona_id}/bedengan', [\App\Http\Controllers\KonvenV2Controller::class, 'bedenganIndex'])->name('konven.v2.bedengan');";
$new = $old . "\n    Route::post('/zona/{zona_id}/tanam-massal', [\App\Http\Controllers\KonvenV2Controller::class, 'tanamMassalZona'])->name('konven.v2.zona.tanam.massal');";

$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Route added.\n";
