<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$c = new \App\Http\Controllers\HydroponicController();
$req = new \Illuminate\Http\Request(['month' => 9, 'year' => 2026]);
try {
    $res = $c->getSummaryCardsData($req);
    $data = $res->getData(true);
    echo "Success: " . json_encode($data);
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n" . $e->getTraceAsString();
}
