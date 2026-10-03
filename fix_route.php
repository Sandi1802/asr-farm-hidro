<?php
$file = 'routes/web.php';
$content = file_get_contents($file);
$content = str_replace(
    "Route::get('/logs', [\App\Http\Controllers\KonvenLogController::class, 'index'])->name('konven.v2.logs');",
    "Route::get('/logs', [\App\Http\Controllers\KonvenLogController::class, 'index'])->name('konven.v2.logs');\n            Route::delete('/logs/{id}', [\App\Http\Controllers\KonvenLogController::class, 'destroy'])->name('konven.v2.logs.destroy');",
    $content
);
file_put_contents($file, $content);
