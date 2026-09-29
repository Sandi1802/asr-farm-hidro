<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$c = new \App\Http\Controllers\HydroponicController();
$req = new \Illuminate\Http\Request();
$res = $c->getSummaryCardsData($req);
print_r($res->getData(true));
