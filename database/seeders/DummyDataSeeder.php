<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PlantType;
use App\Models\Semai;
use App\Models\Hole;
use App\Models\Activity;
use App\Models\BandarProduct;
use App\Models\BandarPartner;
use App\Models\BandarTransaction;
use App\Models\DamageNote;
use App\Models\CalendarEvent;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Plant Types
        $plantTypes = [
            ['name' => 'Pakcoy', 'growth_days' => 30],
            ['name' => 'Selada', 'growth_days' => 40],
            ['name' => 'Kangkung', 'growth_days' => 20],
            ['name' => 'Bayam', 'growth_days' => 25],
        ];
        foreach ($plantTypes as $pt) {
            PlantType::firstOrCreate(['name' => $pt['name']], $pt);
        }

        // 2. Semai (Seedlings)
        Semai::create([
            'plant_name' => 'Pakcoy',
            'quantity' => 1000,
            'semai_date' => Carbon::now()->subDays(15),
            'status' => 'aktif'
        ]);
        Semai::create([
            'plant_name' => 'Selada',
            'quantity' => 500,
            'semai_date' => Carbon::now()->subDays(5),
            'status' => 'aktif'
        ]);

        // 3. Plant some Holes
        // We will grab 50 holes and plant Pakcoy, 50 holes for Selada
        $holes = Hole::where('status', 'kosong')->take(100)->get();
        
        // First 50 Pakcoy
        foreach($holes->take(50) as $hole) {
            $hole->update([
                'status' => 'ditanam',
                'plant_name' => 'Pakcoy',
                'planted_at' => Carbon::now()->subDays(15),
            ]);
            Activity::create([
                'hole_id' => $hole->id,
                'user_id' => 1,
                'type' => 'tanam',
                'description' => 'Pakcoy ditanam di ' . $hole->name,
                'created_at' => Carbon::now()->subDays(15),
            ]);
        }

        // Next 50 Selada
        foreach($holes->skip(50)->take(50) as $hole) {
            $hole->update([
                'status' => 'ditanam',
                'plant_name' => 'Selada',
                'planted_at' => Carbon::now()->subDays(35), // Ready to harvest soon (40 days)
            ]);
            Activity::create([
                'hole_id' => $hole->id,
                'user_id' => 1,
                'type' => 'tanam',
                'description' => 'Selada ditanam di ' . $hole->name,
                'created_at' => Carbon::now()->subDays(35),
            ]);
        }

        // 4. Bandar (Pusat Distribusi)
        $partner1 = BandarPartner::create([
            'name' => 'Pak Budi (Petani Lembang)',
            'phone' => '08123456789',
            'address' => 'Lembang, Bandung',
            'type' => 'supplier'
        ]);

        $partner2 = BandarPartner::create([
            'name' => 'Pasar Induk Caringin',
            'phone' => '08987654321',
            'address' => 'Caringin, Bandung',
            'type' => 'buyer'
        ]);

        $bProduct = BandarProduct::firstOrCreate([
            'name' => 'Pakcoy',
        ], [
            'unit' => 'Kg',
            'stock' => 0
        ]);
        
        $bProduct2 = BandarProduct::firstOrCreate([
            'name' => 'Cabai Merah',
        ], [
            'unit' => 'Kg',
            'stock' => 0
        ]);

        // Transactions
        BandarTransaction::create([
            'product_id' => $bProduct->id,
            'partner_id' => $partner1->id,
            'type' => 'in',
            'quantity' => 200,
            'price' => 0,
            'date' => Carbon::now()->subDays(2),
            'notes' => 'Panen dari Pak Budi'
        ]);
        $bProduct->increment('stock', 200);

        BandarTransaction::create([
            'product_id' => $bProduct->id,
            'partner_id' => $partner2->id,
            'type' => 'out',
            'quantity' => 150,
            'price' => 0,
            'date' => Carbon::now()->subDays(1),
            'notes' => 'Dikirim ke Pasar Induk'
        ]);
        $bProduct->decrement('stock', 150);

        BandarTransaction::create([
            'product_id' => $bProduct->id,
            'partner_id' => null,
            'type' => 'wasted',
            'quantity' => 5,
            'price' => 0,
            'date' => Carbon::now(),
            'notes' => 'Busuk di gudang'
        ]);
        $bProduct->decrement('stock', 5);

        // 5. Damage Notes
        $firstHole = Hole::first();
        if ($firstHole) {
            DamageNote::create([
                'hole_id' => $firstHole->id,
                'user_id' => 1,
                'plant_name' => 'Pakcoy',
                'damage_type' => 'hama',
                'description' => 'Diserang kutu kebul',
                'severity' => 'sedang',
                'location' => 'GH A > Rak 1 > L1',
                'status' => 'open',
                'damaged_at' => Carbon::now()
            ]);
        }

        // 6. Calendar Events
        CalendarEvent::create([
            'title' => 'Jadwal Semai Kangkung Massal',
            'event_date' => Carbon::now()->addDays(2),
            'event_time' => '08:00:00',
            'description' => 'Tim kebun berkumpul untuk semai kangkung',
            'color' => '#3b82f6'
        ]);

        $this->command->info('✅ Dummy data tambahan berhasil dimasukkan!');
    }
}
