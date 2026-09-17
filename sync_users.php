$users = App\Models\User::all();
foreach ($users as $user) {
    $employee = App\Models\Employee::where('email', $user->email)->first();
    if (!$employee) {
        $name = $user->name ?: 'Unknown';
        $email = $user->email;
        $username = $user->username;
        echo "Creating employee for $name ($email)\n";
        App\Models\Employee::create([
            'nip' => 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
            'name' => $name,
            'position' => 'Staff',
            'department' => 'Umum',
            'email' => $email,
            'phone' => null,
            'status' => 'Active'
        ]);
    }
}
