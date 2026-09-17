<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

// Fix erased emails
$empDede = App\Models\Employee::where("name", "DEDE")->first();
if ($empDede) { $empDede->email = "dede@asrfarm.com"; $empDede->save(); }

$empRomy = App\Models\Employee::where("name", "Pak Romy")->first();
if ($empRomy) { $empRomy->email = "romy@asrfarm.com"; $empRomy->save(); }

// Assign usernames to users who do not have one
$users = App\Models\User::all();
foreach ($users as $u) {
    if (empty($u->username)) {
        if (str_contains($u->email, "admin@")) $u->username = "admin";
        elseif (str_contains($u->email, "dede@")) $u->username = "dede";
        elseif (str_contains($u->email, "gita@")) $u->username = "gita";
        elseif (str_contains($u->email, "romy@")) $u->username = "romy";
        else {
            $parts = explode("@", $u->email);
            $u->username = $parts[0];
        }
        $u->save();
        echo "Updated username for {$u->email} to {$u->username}\n";
    }
}

