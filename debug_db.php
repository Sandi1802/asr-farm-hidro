<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// List all konven tables
$tables = \DB::select("SELECT tablename FROM pg_tables WHERE schemaname='public' AND tablename LIKE 'konven%' ORDER BY tablename");
echo "Tables:\n";
foreach ($tables as $t) {
    $count = \DB::table($t->tablename)->count();
    echo "  {$t->tablename}: $count rows\n";
}

echo "\nLahanV2 data:\n";
$lahans = \DB::table('konven_lahans')->get();
foreach ($lahans as $l) {
    echo "  id={$l->id} nama={$l->nama}\n";
}

echo "\nLubang sample:\n";
$lubang = \DB::table('konven_lubang_tanams')->limit(5)->get();
foreach ($lubang as $l) {
    echo "  id={$l->id} status={$l->status} plant=".($l->plant_name ?? 'NULL')."\n";
}
