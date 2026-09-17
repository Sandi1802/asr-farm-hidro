<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$e = App\Models\Employee::with("user")->where("id", 6)->first();
echo json_encode($e);

