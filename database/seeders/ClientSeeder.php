<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $clients = [
            ['Restaurant', 'Industri F&B'],
            ['Hotel & Resort', 'Sektor Hospitality'],
            ['Supermarket', 'Modern Retail'],
            ['Grocery Store', 'Minimarket'],
            ['Kantin & Instansi', 'Institusi'],
            ['Fresh Market', 'Pasar Segar']
        ];
        foreach ($clients as $i => $c) {
            \App\Models\Client::create(['name' => $c[0], 'description' => $c[1], 'order' => $i]);
        }
    }
}
