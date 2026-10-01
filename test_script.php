<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$req = new \Illuminate\Http\Request();
$req->merge(['month' => 10, 'year' => 2026]);
$ctrl = new \App\Http\Controllers\HydroponicController();
echo json_encode($ctrl->getSummaryCardsData($req)->getData());
