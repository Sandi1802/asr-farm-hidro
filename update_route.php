<?php
$file = "D:\ASR GROUP\ASR-APPS\ASR_GREEN_WEB\routes\api.php";
$content = file_get_contents($file);
$content = str_replace("Route::post(\"/racks/{id}/damage\", [RackApiController::class, \"damage\"]);", "Route::post(\"/racks/{id}/damage\", [RackApiController::class, \"damage\"]);\n        Route::post(\"/racks/{id}/update-age\", [RackApiController::class, \"updateAge\"]);", $content);
file_put_contents($file, $content);
echo "Route added";

