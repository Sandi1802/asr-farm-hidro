<?php
$users = App\Models\User::all();
foreach($users as $user) {
    $user->password = bcrypt('password');
    $user->save();
    echo $user->name . ' (' . $user->role_agri . ') : ' . $user->email . " - password\n";
}
echo "All passwords reset to 'password'\n";
