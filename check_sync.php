<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$emps = App\Models\Employee::with("user")->get();
foreach ($emps as $e) {
    echo "Emp: {$e->name}, Email: {$e->email}, User: " . ($e->user ? $e->user->username : "NULL") . "\n";
}

