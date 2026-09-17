<?php
require __DIR__."/vendor/autoload.php";
$app = require_once __DIR__."/bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$template = [
    "opening" => [
        "Pintu/akses kebun aman",
        "Area umum tidak banjir/genangan",
        "Pompa utama menyala normal",
        "Aliran air semua jalur aktif",
        "Volume tandon cukup",
        "pH larutan dicatat",
        "EC/PPM larutan dicatat",
        "Alat ukur siap pakai",
        "Tanaman layu/abnormal dicek",
        "Tanda hama dicek",
        "Area semai dicek",
        "Order hari ini dicek",
        "Stok kemasan dicek",
        "Area packing bersih",
        "Briefing singkat dilakukan"
    ],
    "siang" => [
        "Tanaman tidak stres panas berlebihan",
        "Aliran air tetap rata",
        "Area kerja tetap bersih",
        "Panen sesuai order",
        "Sortasi dilakukan",
        "Packing tidak telat",
        "Komunikasi pelanggan aktif"
    ],
    "closing" => [
        "Sisa panen ditangani",
        "Area packing dibersihkan",
        "Sampah organik dibuang/diolah",
        "Pompa dan aliran akhir dicek",
        "Volume tandon cukup untuk malam",
        "Panel listrik/stop kontak aman",
        "Alat dikembalikan",
        "Alat ukur dibersihkan",
        "Stok kritis dicatat",
        "Kas kecil/struk hari ini aman",
        "Foto closing diambil",
        "Pintu/gembok ditutup",
        "Serah terima shift dibuat"
    ]
];

if (App\Models\DailyTaskTemplate::count() == 0) {
    foreach ($template as $shift => $tasks) {
        foreach ($tasks as $taskName) {
            App\Models\DailyTaskTemplate::create([
                "shift" => $shift,
                "task_name" => $taskName
            ]);
        }
    }
    echo "Inserted templates.\n";
} else {
    echo "Templates already exist.\n";
}

