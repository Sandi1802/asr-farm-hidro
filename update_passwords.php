<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use Illuminate\Support\Facades\Hash;
use App\Models\User;

$users = User::all();
$newPassword = Hash::make("T3l3m4t1k4");

foreach($users as $user) {
    $user->password = $newPassword;
    $user->save();
}
echo "Semua password user berhasil diubah menjadi T3l3m4t1k4\n";

