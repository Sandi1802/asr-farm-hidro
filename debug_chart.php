<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate what dashboard does for charts
$allTanam = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
    ->whereNotNull('plant_name')->where('plant_name', '!=', '')
    ->get();

echo "Total rows with plant_name: " . $allTanam->count() . "\n";

$tanamCounts = [];
$panenCounts = [];
foreach ($allTanam as $row) {
    $plants = explode(', ', $row->plant_name);
    foreach ($plants as $p) {
        $p = trim($p);
        if (empty($p)) continue;
        if (!isset($tanamCounts[$p])) $tanamCounts[$p] = 0;
        $tanamCounts[$p]++;
        if ($row->status === 'panen') {
            if (!isset($panenCounts[$p])) $panenCounts[$p] = 0;
            $panenCounts[$p]++;
        }
    }
}

echo "tanamCounts: " . json_encode($tanamCounts) . "\n";
echo "panenCounts: " . json_encode($panenCounts) . "\n";

// Kalender
$tanamGroups = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
    ->where('status', 'ditanam')
    ->whereNotNull('planted_at')
    ->select(\Illuminate\Support\Facades\DB::raw('DATE(planted_at) as date'), 'plant_name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
    ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(planted_at)'), 'plant_name')
    ->get();

echo "Calendar tanam events: " . $tanamGroups->count() . "\n";
foreach ($tanamGroups as $g) {
    echo "  - " . $g->date . " | " . $g->plant_name . " | " . $g->count . "\n";
}

// Chart keterisian
$lahans = \App\Models\KonvenLahanV2::all();
echo "\nLahans: " . $lahans->count() . "\n";
foreach ($lahans as $lahan) {
    $terisi = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
        ->join('konven_bedengans', 'konven_lubang_tanams.bedengan_id', '=', 'konven_bedengans.id')
        ->join('konven_zonas', 'konven_bedengans.zona_id', '=', 'konven_zonas.id')
        ->join('konven_kodes', 'konven_zonas.kode_id', '=', 'konven_kodes.id')
        ->where('konven_kodes.lahan_id', $lahan->id)
        ->where('konven_lubang_tanams.status', 'ditanam')
        ->count();
    echo "  " . $lahan->nama . " => terisi: $terisi\n";
}
