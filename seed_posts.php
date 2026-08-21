<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$posts = [
    [
        'title' => 'Produk Sayuran; Sumber Serat Alami',
        'content' => "Anda bisa memilih berbagai jenis sayuran segar yang dikelola secara hati-hati baik secara hidroponik maupun organik. \n\nSayuran adalah sumber serat alami yang sangat penting untuk pencernaan dan kesehatan tubuh kita secara keseluruhan. Pastikan hidangan keluarga Anda selalu dilengkapi dengan sayuran hijau yang segar dari ASR Farm.",
        'image' => 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Sumber Protein Hewani',
        'content' => "Susu, telor dan daging adalah sumber protein hewani. Kami menyeleksi sumbernya dan bahkan kami merawat peternakan kami sendiri untuk memastikan kualitas yang terbaik.\n\nProtein hewani sangat penting untuk pertumbuhan dan menjaga daya tahan tubuh keluarga Anda setiap hari.",
        'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?q=80&w=600&auto=format&fit=crop',
    ],
    [
        'title' => 'Bahan Pokok Pilihan Terbaik',
        'content' => "Ada bahan-bahan yang umumnya harus tersedia di dapur kita, seperti beras, minyak, dan gula. Kami memastikan pasokan bahan pokok Anda terjaga kualitasnya dan bebas dari pengawet buatan.\n\nMemasak menjadi jauh lebih tenang dan menyenangkan dengan bahan yang berkualitas.",
        'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e8ac?q=80&w=600&auto=format&fit=crop',
    ]
];

foreach($posts as $p) {
    if (!\App\Models\Post::where('title', $p['title'])->exists()) {
        \App\Models\Post::create($p);
    }
}
echo "Seeded posts successfully.\n";
