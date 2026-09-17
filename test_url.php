<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$e = App\Models\Employee::where("name", "DEDE")->orWhere("name", "Dede")->first();
if ($e && $e->avatar) {
    echo "Avatar path: " . $e->avatar . PHP_EOL;
    echo "URL: " . asset("storage/" . $e->avatar) . PHP_EOL;
    $fullPath = storage_path("app/public/" . $e->avatar);
    echo "File exists: " . (file_exists($fullPath) ? "YES" : "NO") . PHP_EOL;
    echo "Full path: " . $fullPath . PHP_EOL;
}

