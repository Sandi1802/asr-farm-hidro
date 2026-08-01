<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@asrfarm.com',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@asrfarm.com',
                'role' => 'admin',
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@asrfarm.com',
                'role' => 'viewer',
            ],
        ];

        foreach ($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => Hash::make('ASRFarm@2026'),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->call(HydroponicSeeder::class);
    }
}