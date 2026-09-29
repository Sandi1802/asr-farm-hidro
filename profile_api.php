<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$c = new \App\Http\Controllers\HydroponicController();
$req = new \Illuminate\Http\Request(['month' => 9, 'year' => 2026]);

$start = microtime(true);
$res = $c->getSummaryCardsData($req);
$time = microtime(true) - $start;

echo "Time taken: " . $time . " seconds\n";
