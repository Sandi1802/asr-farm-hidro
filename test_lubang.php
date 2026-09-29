<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$c = new \App\Http\Controllers\HydroponicController();
$req = new \Illuminate\Http\Request();
$data = $c->getSummaryCardsData($req)->getData(true);
echo "lubang_kosong: " . $data['lubang_kosong'] . "\n";
