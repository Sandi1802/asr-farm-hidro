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
            [
                'name' => 'Kepala Produksi',
                'email' => 'kepala_produksi@asrfarm.com',
                'role' => 'admin',
                'role_agri' => 'kepala_produksi',
            ],
            [
                'name' => 'Atasan',
                'email' => 'atasan@asrfarm.com',
                'role' => 'admin',
                'role_agri' => 'atasan',
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@asrfarm.com',
                'role' => 'admin',
                'role_agri' => 'staff',
            ],
            [
                'name' => 'Kepala Greenhouse',
                'email' => 'kepala_greenhouse@asrfarm.com',
                'role' => 'admin',
                'role_agri' => 'kepala_greenhouse',
            ],
            [
                'name' => 'Kepala Konven',
                'email' => 'kepala_konven@asrfarm.com',
                'role' => 'admin',
                'role_agri' => 'kepala_konven',
            ],
        ];

        foreach ($accounts as $account) {
            User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'role' => $account['role'],
                    'role_agri' => $account['role_agri'] ?? 'pegawai',
                    'password' => Hash::make('ASRFarm@2026'),
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->call(HydroponicSeeder::class);
    }
}