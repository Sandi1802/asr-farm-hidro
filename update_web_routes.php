<?php
$file = "routes/web.php";
$content = file_get_contents($file);
$search = "Route::post('/notifications/read', [\App\Http\Controllers\HydroponicController::class, 'markNotificationsRead']);";
$replace = $search . "\n\n            // Daily Tasks\n            Route::get('/daily-tasks', [\App\Http\Controllers\DailyTaskController::class, 'webIndex'])->name('hydroponics.daily-tasks');";
$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);

