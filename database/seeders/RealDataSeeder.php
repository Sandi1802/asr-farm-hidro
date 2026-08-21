<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Greenhouse;
use App\Models\Rack;
use App\Models\Row;
use App\Models\Hole;
use App\Models\Inventory;
use Illuminate\Support\Facades\Schema;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Wipe old structure
        Schema::disableForeignKeyConstraints();
        Hole::truncate();
        Row::truncate();
        Rack::truncate();
        Greenhouse::truncate();
        Inventory::truncate();
        DB::table('activities')->truncate();
        Schema::enableForeignKeyConstraints();

        // 2. Insert Inventories (using bulk insert for speed)
        $inventories = [
            // GH 1
            ['name' => 'Pompa Air', 'type' => 'Peralatan', 'quantity' => 20, 'unit' => 'unit', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Selang Pengisian Air', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Sapu', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Pel', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Spons', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Baju Kerja', 'type' => 'Perlengkapan', 'quantity' => 2, 'unit' => 'buah', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Masker', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box/pcs', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Evo Plusmed', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box/pcs', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Gelas Ukur 500 ml', 'type' => 'Alat Ukur', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Gelas Ukur 5000 ml', 'type' => 'Alat Ukur', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Pompa AB Mix', 'type' => 'Peralatan', 'quantity' => 3, 'unit' => 'unit', 'description' => 'Lokasi: GH 1'],
            ['name' => 'Sepatu', 'type' => 'Perlengkapan', 'quantity' => 3, 'unit' => 'pasang', 'description' => 'Lokasi: GH 1'],

            // GH 2
            ['name' => 'Pompa Air', 'type' => 'Peralatan', 'quantity' => 10, 'unit' => 'unit', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Selang Pengisian Air', 'type' => 'Peralatan', 'quantity' => 2, 'unit' => 'buah', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Baju Kerja', 'type' => 'Perlengkapan', 'quantity' => 2, 'unit' => 'buah', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Masker', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Evo', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Handscoon', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Hand Sanitizer', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Sepatu Boot', 'type' => 'Perlengkapan', 'quantity' => 2, 'unit' => 'pasang', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Gelas Ukur 500 ml', 'type' => 'Alat Ukur', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 2'],
            ['name' => 'Pompa AB Mix', 'type' => 'Peralatan', 'quantity' => 2, 'unit' => 'unit', 'description' => 'Lokasi: GH 2'],

            // GH 3
            ['name' => 'Pompa Air', 'type' => 'Peralatan', 'quantity' => 10, 'unit' => 'unit', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Sapu', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Ember', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Baju Kerja', 'type' => 'Perlengkapan', 'quantity' => 2, 'unit' => 'buah', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Masker', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Evo', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Handscoon', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Hand Sanitizer', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Sepatu', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'pasang', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Plastik', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'stok', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Gelas Ukur 500 ml', 'type' => 'Alat Ukur', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Gelas Ukur 5000 ml', 'type' => 'Alat Ukur', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 3'],
            ['name' => 'Pompa AB Mix', 'type' => 'Peralatan', 'quantity' => 2, 'unit' => 'unit', 'description' => 'Lokasi: GH 3'],

            // GH 4
            ['name' => 'Pompa Air', 'type' => 'Peralatan', 'quantity' => 10, 'unit' => 'unit', 'description' => 'Lokasi: GH 4 (Perbaikan 22 Juli)'],
            ['name' => 'Selang Air', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Bedog', 'type' => 'Peralatan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Baju Kerja', 'type' => 'Perlengkapan', 'quantity' => 2, 'unit' => 'buah', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Masker', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Evo', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Handscoon', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'box', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Hand Sanitizer', 'type' => 'Perlengkapan', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Alat Ukur', 'type' => 'Alat Ukur', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Gelas Ukur 500 ml', 'type' => 'Alat Ukur', 'quantity' => 1, 'unit' => 'buah', 'description' => 'Lokasi: GH 4'],
            ['name' => 'Pompa AB Mix', 'type' => 'Peralatan', 'quantity' => 2, 'unit' => 'unit', 'description' => 'Lokasi: GH 4'],
        ];

        $invData = [];
        foreach ($inventories as $inv) {
            $invData[] = [
                'name' => $inv['name'],
                'type' => $inv['type'],
                'quantity' => $inv['quantity'],
                'unit' => $inv['unit'],
                'description' => $inv['description'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        Inventory::insert($invData);

        // GH 1
        $gh1 = Greenhouse::create(['name' => 'GH 1', 'description' => 'GH 1 - 21 Rak']);
        for ($i = 1; $i <= 21; $i++) {
            $rackName = ($i == 21) ? 'Rak 21 (Penyemaian)' : "Rak $i";
            $rack = Rack::create(['greenhouse_id' => $gh1->id, 'name' => $rackName, 'ppm_level' => 1000, 'ph_level' => 6.0]);
            
            $rowCount = ($i == 1) ? 10 : 7;
            for ($r = 1; $r <= $rowCount; $r++) {
                $row = Row::create(['rack_id' => $rack->id, 'name' => "Baris $r"]);
                
                $holesData = [];
                for ($h = 1; $h <= 50; $h++) {
                    $holesData[] = [
                        'row_id' => $row->id,
                        'name' => "L" . str_pad($h, 2, '0', STR_PAD_LEFT),
                        'status' => 'kosong',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                Hole::insert($holesData);
            }
        }

        // GH 2
        $gh2 = Greenhouse::create(['name' => 'GH 2', 'description' => 'GH 2 - 10 Rak']);
        for ($i = 1; $i <= 10; $i++) {
            $rack = Rack::create(['greenhouse_id' => $gh2->id, 'name' => "Rak $i", 'ppm_level' => 1000, 'ph_level' => 6.0]);
            for ($r = 1; $r <= 7; $r++) {
                $row = Row::create(['rack_id' => $rack->id, 'name' => "Baris $r"]);
                
                $holesData = [];
                for ($h = 1; $h <= 50; $h++) {
                    $holesData[] = [
                        'row_id' => $row->id,
                        'name' => "L" . str_pad($h, 2, '0', STR_PAD_LEFT),
                        'status' => 'kosong',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                Hole::insert($holesData);
            }
        }

        // GH 3
        $gh3 = Greenhouse::create(['name' => 'GH 3', 'description' => 'GH 3 - 10 Rak']);
        for ($i = 1; $i <= 10; $i++) {
            $rack = Rack::create(['greenhouse_id' => $gh3->id, 'name' => "Rak $i", 'ppm_level' => 1000, 'ph_level' => 6.0]);
            for ($r = 1; $r <= 8; $r++) {
                $row = Row::create(['rack_id' => $rack->id, 'name' => "Baris $r"]);
                
                $holesData = [];
                for ($h = 1; $h <= 51; $h++) {
                    $holesData[] = [
                        'row_id' => $row->id,
                        'name' => "L" . str_pad($h, 2, '0', STR_PAD_LEFT),
                        'status' => 'kosong',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                Hole::insert($holesData);
            }
        }

        // GH 4
        $gh4 = Greenhouse::create(['name' => 'GH 4', 'description' => 'GH 4 - 10 Rak (Pompa perbaikan 22 Juli)']);
        for ($i = 1; $i <= 10; $i++) {
            $rack = Rack::create(['greenhouse_id' => $gh4->id, 'name' => "Rak $i", 'ppm_level' => 1000, 'ph_level' => 6.0]);
            for ($r = 1; $r <= 8; $r++) {
                $row = Row::create(['rack_id' => $rack->id, 'name' => "Baris $r"]);
                
                $holesData = [];
                for ($h = 1; $h <= 51; $h++) {
                    $holesData[] = [
                        'row_id' => $row->id,
                        'name' => "L" . str_pad($h, 2, '0', STR_PAD_LEFT),
                        'status' => 'kosong',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                Hole::insert($holesData);
            }
        }
    }
}
