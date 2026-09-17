<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$employees = App\Models\Employee::all();
foreach ($employees as $e) {
    echo "ID: $e->id, Name: $e->name, Email: $e->email\n";
}

