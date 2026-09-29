<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$c = new \App\Http\Controllers\HydroponicController();
$req = new \Illuminate\Http\Request(['month' => 9, 'year' => 2026]);
$res = $c->getSummaryCardsData($req);
$data = $res->getData(true);
echo "siap_panen: " . $data['siap_panen'] . "\n";
echo "siap_panen_sub: " . $data['siap_panen_sub'] . "\n";
echo "lubang_kosong: " . $data['lubang_kosong'] . "\n";
