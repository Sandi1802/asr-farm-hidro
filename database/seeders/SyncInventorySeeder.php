<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\DB;

class SyncInventorySeeder extends Seeder
{
    public function run()
    {
        // Fix old capitalized types from RealDataSeeder
        DB::statement("UPDATE inventories SET type = LOWER(type)");

        $defaults = [
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
            ['name'=>'pH Down','type'=>'nutrisi','quantity'=>2,'unit'=>'liter','description'=>'Penurun pH air'],
            ['name'=>'Timer Digital','type'=>'perlengkapan','quantity'=>4,'unit'=>'unit','description'=>'Timer pompa otomatis'],
        ];

        foreach ($defaults as $item) {
            $exists = Inventory::where('name', $item['name'])->where('type', $item['type'])->first();
            if (!$exists) {
                $inv = Inventory::create($item);
                InventoryLog::create([
                    'inventory_id' => $inv->id,
                    'type'         => 'in',
                    'quantity'     => $item['quantity'],
                    'description'  => 'Stok default awal',
                    'user_id'      => 1, // Default Admin
                ]);
            }
        }
    }
}
