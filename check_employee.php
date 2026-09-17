<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$e = App\Models\Employee::first();
echo "Kolom: " . implode(", ", array_keys($e->toArray())) . PHP_EOL;
echo "Avatar: " . $e->avatar . PHP_EOL;

