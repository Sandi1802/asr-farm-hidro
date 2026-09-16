<?php
$file = "routes/api.php";
$content = file_get_contents($file);
$search = "// Maintenance Logs";
$replace = "// Daily Tasks API\n    Route::get('/daily-tasks', [\App\Http\Controllers\DailyTaskController::class, 'apiIndex']);\n    Route::post('/daily-tasks', [\App\Http\Controllers\DailyTaskController::class, 'apiStore']);\n    Route::post('/daily-tasks/{id}/complete', [\App\Http\Controllers\DailyTaskController::class, 'apiToggleComplete']);\n    Route::post('/daily-tasks/{id}/note', [\App\Http\Controllers\DailyTaskController::class, 'apiUpdateNote']);\n    Route::delete('/daily-tasks/{id}', [\App\Http\Controllers\DailyTaskController::class, 'apiDelete']);\n\n        " . $search;
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

