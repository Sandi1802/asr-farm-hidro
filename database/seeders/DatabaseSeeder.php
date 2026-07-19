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
                'email' => 'superadmin@len.co.id',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@len.co.id',
                'role' => 'admin',
            ],
            [
                'name' => 'Viewer',
                'email' => 'viewer@len.co.id',
                'role' => 'viewer',
            ],
        ];

        foreach ($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'password' => Hash::make('LenBTC@2024'),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}