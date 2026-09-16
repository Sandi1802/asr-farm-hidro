<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PaprikaGreenhouse;
use App\Models\PaprikaPlant;

class InitPaprikaData extends Command
{
    protected $signature = 'paprika:init';
    protected $description = 'Initialize 3 Paprika Greenhouses and 1000 plants per greenhouse.';

    public function handle()
    {
        if (PaprikaGreenhouse::count() > 0) {
            $this->info('Paprika data already initialized.');
            return;
        }

        $this->info('Initializing Paprika Data...');

        for ($i = 1; $i <= 3; $i++) {
            $gh = PaprikaGreenhouse::create([
                'name' => "GH Paprika $i",
                'capacity' => 1000
            ]);

            $plantsData = [];
            for ($j = 1; $j <= 1000; $j++) {
                $code = "P$i-" . str_pad($j, 4, '0', STR_PAD_LEFT);
                $plantsData[] = [
                    'paprika_greenhouse_id' => $gh->id,
                    'code' => $code,
                    'status' => 'kosong',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Chunk insert to avoid memory issues and speed up
            foreach (array_chunk($plantsData, 500) as $chunk) {
                PaprikaPlant::insert($chunk);
            }

            $this->info("Created GH Paprika $i with 1000 plants.");
        }

        $this->info('Paprika data initialization complete!');
    }
}
