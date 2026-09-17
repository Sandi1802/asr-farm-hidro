<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$request = Illuminate\Http\Request::create("/hydroponics/master-data/employees/6", "PUT", [
    "nip" => "EMP-0001",
    "name" => "Super Admin",
    "position" => "IT Admin",
    "department" => "Umum",
    "status" => "Active",
    "username" => "sandi.pranata",
    "role_agri" => "it_admin"
]);

$controller = new App\Http\Controllers\MasterDataController();
try {
    $response = $controller->updateEmployee($request, 6);
    echo "Success!\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Validation Error: \n";
    print_r($e->errors());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

