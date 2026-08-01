<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hole;
use App\Models\Activity;
use Carbon\Carbon;

class ExtraDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Ready to harvest (Selada, growth=40 days, planted 45 days ago)
        $holes = Hole::where('status', 'kosong')->take(20)->get();
        foreach($holes as $hole) {
            $hole->update([
                'status' => 'ditanam',
                'plant_name' => 'Selada',
                'planted_at' => Carbon::now()->subDays(45)
            ]);
            Activity::create([
                'hole_id' => $hole->id,
                'user_id' => 1,
                'type' => 'tanam',
                'description' => 'Selada ditanam di ' . $hole->name,
                'created_at' => Carbon::now()->subDays(45)
            ]);
        }

        // 2. Already Harvested this month
        $holes = Hole::where('status', 'kosong')->take(30)->get();
        foreach($holes as $hole) {
            $hole->update([
                'status' => 'panen',
                'plant_name' => 'Kangkung',
                'planted_at' => Carbon::now()->subDays(25),
            ]);
            Activity::create([
                'hole_id' => $hole->id,
                'user_id' => 1,
                'type' => 'panen',
                'description' => 'Panen Kangkung di ' . $hole->name,
                'created_at' => Carbon::now()->subDays(2) 
            ]);
        }

        // 3. Gagal Panen (Rusak)
        $holes = Hole::where('status', 'kosong')->take(10)->get();
        foreach($holes as $hole) {
            $hole->update([
                'status' => 'rusak',
                'plant_name' => 'Bayam',
                'planted_at' => Carbon::now()->subDays(10),
            ]);
            Activity::create([
                'hole_id' => $hole->id,
                'user_id' => 1,
                'type' => 'rusak',
                'description' => 'Gagal panen bayam di ' . $hole->name . ' (Kena Hama)',
                'created_at' => Carbon::now()->subDays(1)
            ]);
        }
        $this->command->info('✅ Extra data injected!');
    }
}
