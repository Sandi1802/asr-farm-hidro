<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Greenhouse;
use App\Models\Rack;
use App\Models\Row;
use App\Models\Hole;
use App\Models\Inventory;

class HydroponicSeeder extends Seeder
{
    public function run()
    {
        // Create Atasan User
        $atasan = User::updateOrCreate(
            ['email' => 'atasan@asrfarm.com'],
            [
                'name'       => 'Atasan Kebun',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'role_agri'  => 'atasan',
            ]
        );

        // Create Pegawai User
        User::updateOrCreate(
            ['email' => 'pegawai@asrfarm.com'],
            [
                'name'       => 'Pegawai Kebun',
                'password'   => Hash::make('password'),
                'role'       => 'viewer',
                'role_agri'  => 'pegawai',
            ]
        );

        // 4 Green Houses
        $ghNames = ['Green House A', 'Green House B', 'Green House C', 'Green House D'];

        foreach ($ghNames as $ghName) {
            $gh = Greenhouse::create([
                'name'        => $ghName,
                'status'      => 'aktif',
                'description' => 'Greenhouse hidroponik sistem NFT.',
            ]);

            // 10 Racks per GH
            for ($r = 1; $r <= 10; $r++) {
                $rack = Rack::create([
                    'greenhouse_id'    => $gh->id,
                    'name'             => 'Rak ' . $r,
                    'ppm_level'        => rand(800, 1200),
                    'ph_level'         => round(rand(55, 70) / 10, 1),
                    'ppm_ph_updated_at'=> now(),
                    'status'           => 'aktif',
                ]);

                // 8 Rows per Rack
                for ($b = 1; $b <= 8; $b++) {
                    $row = Row::create([
                        'rack_id' => $rack->id,
                        'name'    => 'Baris ' . $b,
                        'status'  => 'aktif',
                    ]);

                    // 51 Holes per Row
                    for ($l = 1; $l <= 51; $l++) {
                        Hole::create([
                            'row_id'     => $row->id,
                            'name'       => 'L' . $l,
                            'plant_name' => null,
                            'status'     => 'kosong',
                        ]);
                    }
                }
            }
        }

        // Inventory Samples
        $inventoryItems = [
            ['name'=>'Bibit Pakcoy','type'=>'bibit','quantity'=>500,'unit'=>'gram','description'=>'Bibit pakcoy hijau'],
            ['name'=>'Bibit Selada','type'=>'bibit','quantity'=>300,'unit'=>'gram','description'=>'Selada keriting'],
            ['name'=>'Bibit Kangkung','type'=>'bibit','quantity'=>200,'unit'=>'gram','description'=>'Kangkung hidroponik'],
            ['name'=>'Bibit Bayam','type'=>'bibit','quantity'=>150,'unit'=>'gram','description'=>'Bayam merah'],
            ['name'=>'Net Pot','type'=>'media_tanam','quantity'=>1000,'unit'=>'buah','description'=>'Net pot 5cm'],
            ['name'=>'Rockwool','type'=>'media_tanam','quantity'=>50,'unit'=>'lembar','description'=>'Rockwool sheet 25x25'],
            ['name'=>'Hidroton','type'=>'media_tanam','quantity'=>25,'unit'=>'kg','description'=>'Clay pebbles media tanam'],
            ['name'=>'AB Mix A','type'=>'nutrisi','quantity'=>15,'unit'=>'kg','description'=>'Nutrisi larutan A'],
            ['name'=>'AB Mix B','type'=>'nutrisi','quantity'=>15,'unit'=>'kg','description'=>'Nutrisi larutan B'],
            ['name'=>'pH Up','type'=>'nutrisi','quantity'=>2,'unit'=>'liter','description'=>'Penambah pH air'],
            ['name'=>'pH Down','type'=>'nutrisi','quantity'=>3,'unit'=>'liter','description'=>'Penurun pH air'],
            ['name'=>'Pestisida Organik','type'=>'obat','quantity'=>5,'unit'=>'liter','description'=>'Neem oil organik'],
            ['name'=>'Fungisida','type'=>'obat','quantity'=>2,'unit'=>'liter','description'=>'Anti jamur tanaman'],
            ['name'=>'TDS Meter','type'=>'peralatan','quantity'=>3,'unit'=>'unit','description'=>'Pengukur PPM digital'],
            ['name'=>'pH Meter','type'=>'peralatan','quantity'=>2,'unit'=>'unit','description'=>'Pengukur pH digital'],
            ['name'=>'Pompa Air','type'=>'peralatan','quantity'=>8,'unit'=>'unit','description'=>'Pompa submersible 25W'],
            ['name'=>'Selang PE 16mm','type'=>'perlengkapan','quantity'=>100,'unit'=>'meter','description'=>'Selang irigasi utama'],
            ['name'=>'Timer Digital','type'=>'perlengkapan','quantity'=>4,'unit'=>'unit','description'=>'Timer pompa otomatis'],
        ];

        foreach ($inventoryItems as $item) {
            Inventory::create($item);
        }

        $this->command->info('✅ Seeder berhasil! 4 GH × 10 Rak × 8 Baris × 51 Lubang = 16.320 titik tanam ter-generate.');
    }
}
