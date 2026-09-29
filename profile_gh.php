<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$c = new \App\Http\Controllers\HydroponicController();

$start = microtime(true);
$res = $c->greenhouses();
$time = microtime(true) - $start;

echo "Greenhouse Page Time taken: " . $time . " seconds\n";
