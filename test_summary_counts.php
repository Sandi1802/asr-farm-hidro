<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$c = new \App\Http\Controllers\HydroponicController();
$req = new \Illuminate\Http\Request();
$res = $c->getSummaryCardsData($req);
$data = $res->getData(true);
echo "siap_panen: " . $data['siap_panen'] . "\n";
echo "sudah_panen: " . $data['sudah_panen'] . "\n";
echo "gagal_panen: " . $data['gagal_panen'] . "\n";
