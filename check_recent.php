<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$emps = App\Models\Employee::all();
foreach ($emps as $e) {
    $u = App\Models\User::where("email", $e->email)->first();
    echo "ID: $e->id | Emp: $e->name | Emp Email: $e->email | User: " . ($u ? $u->username : "NULL") . "\n";
}

