<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$products = [
    ['name' => 'Brokoli Hijau', 'description' => 'Per 250 gram', 'image' => 'https://images.unsplash.com/photo-1459411621453-7b03977f4bfc?q=80&w=500&auto=format&fit=crop', 'category' => 'Organik', 'sale' => true],
    ['name' => 'Selada Merah', 'description' => 'Per 250 gram', 'image' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?q=80&w=500&auto=format&fit=crop', 'category' => 'Hidroponik', 'sale' => false],
    ['name' => 'Wortel Manis', 'description' => 'Per 500 gram', 'image' => 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?q=80&w=500&auto=format&fit=crop', 'category' => 'Organik', 'sale' => true],
    ['name' => 'Pakcoy Segar', 'description' => 'Per 250 gram', 'image' => 'https://images.unsplash.com/photo-1555243896-771a8005d5dd?q=80&w=500&auto=format&fit=crop', 'category' => 'Hidroponik', 'sale' => false],
    ['name' => 'Tomat Ceri', 'description' => 'Per 250 gram', 'image' => 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?q=80&w=500&auto=format&fit=crop', 'category' => 'Organik', 'sale' => false],
    ['name' => 'Bayam Hijau', 'description' => 'Per 250 gram', 'image' => 'https://images.unsplash.com/photo-1576045057995-568f588f82fb?q=80&w=500&auto=format&fit=crop', 'category' => 'Organik', 'sale' => false],
    ['name' => 'Paprika Merah', 'description' => 'Per 1 buah', 'image' => 'https://images.unsplash.com/photo-1582806206140-5e8d89004947?q=80&w=500&auto=format&fit=crop', 'category' => 'Hidroponik', 'sale' => false]
];

foreach($products as $p) {
    if (!\App\Models\Product::where('name', $p['name'])->exists()) {
        \App\Models\Product::create($p);
    }
}
echo "Seeded successfully.\n";
