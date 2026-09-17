<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$e = App\Models\Employee::where("name", "DEDE")->orWhere("name", "Dede")->first();
if ($e) {
    echo "ID: " . $e->id . PHP_EOL;
    echo "Avatar DB: " . $e->avatar . PHP_EOL;
}

