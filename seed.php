<?php
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Keep ID 1 to prevent session break
$u = User::find(1);
if($u) {
    $u->update([
        'name' => 'Sandi Pranata',
        'email' => 'sandi@asrfarm.com',
        'username' => 'sandi',
        'role_agri' => 'it_admin',
        'password' => Hash::make('asrfarm123')
    ]);
} else {
    User::create([
        'id' => 1,
        'name' => 'Sandi Pranata',
        'email' => 'sandi@asrfarm.com',
        'username' => 'sandi',
        'role_agri' => 'it_admin',
        'role' => 'super_admin',
        'password' => Hash::make('asrfarm123')
    ]);
}

try { User::where('id', '!=', 1)->delete(); } catch(\Exception $e) { echo $e->getMessage(); }

$users = [
    ['Arif', 'arif@asrfarm.com', 'arif', 'packing'],
    ['Dede', 'dede@asrfarm.com', 'dede', 'produksi_gh'],
    ['Pak Romy', 'romy@asrfarm.com', 'romy', 'produksi'],
    ['Pak Yaya', 'yaya@asrfarm.com', 'yaya', 'produksi_konven'],
    ['Bu Gita', 'gita@asrfarm.com', 'gita', 'atasan']
];

foreach ($users as $usr) {
    try {
        User::create([
            'name' => $usr[0],
            'email' => $usr[1],
            'username' => $usr[2],
            'role_agri' => $usr[3],
            'role' => 'viewer',
            'password' => Hash::make('asrfarm123')
        ]);
    } catch(\Exception $e) { echo $e->getMessage(); }
}

echo "Users created successfully\n";
