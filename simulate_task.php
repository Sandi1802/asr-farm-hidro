<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

auth()->loginUsingId(1); // Login as user 1

$request = Illuminate\Http\Request::create("/hydroponics/daily-tasks-api", "POST", [
    "task_name" => "Tugas dari CLI",
    "notes" => "Testing",
    "date" => date("Y-m-d"),
    "shift" => "pr",
    "is_pr" => true
]);

$controller = new App\Http\Controllers\DailyTaskController();
try {
    $response = $controller->apiStore($request);
    echo "Success! Response: " . $response->getContent() . "\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Validation Error: \n";
    print_r($e->errors());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

