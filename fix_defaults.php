<?php
$f = 'database/migrations/2026_10_01_140246_create_settings_table.php';
$c = file_get_contents($f);

$c = str_replace(
    "'Selamat Datang, {user}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.'",
    "'Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.'",
    $c
);

file_put_contents($f, $c);

// Also SettingController default fallback
$fc = 'app/Http/Controllers/SettingController.php';
$cc = file_get_contents($fc);
$cc = str_replace(
    "'Selamat Datang, {user}! Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.'",
    "'Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.'",
    $cc
);
file_put_contents($fc, $cc);

echo "Migration and Controller updated.\n";
