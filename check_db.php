<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "V2 Tables:\n";
echo "Lahan: " . \DB::table('konven_lahans')->count() . "\n";
echo "Bedengan: " . \DB::table('konven_bedengans')->count() . "\n";
echo "Lubang: " . \DB::table('konven_lubang_tanams')->count() . "\n";
echo "Ditanam: " . \DB::table('konven_lubang_tanams')->where('status', 'ditanam')->count() . "\n";

echo "\nV1 Tables:\n";
echo "Lahan: " . \DB::table('konven_lahan')->count() . "\n";
echo "Bedengan: " . \DB::table('konven_bedeng')->count() . "\n";
echo "Lubang: " . \DB::table('konven_panen')->count() . "\n";
