<?php
$f = 'app/Http/Controllers/KonvensionalController.php';
$c = file_get_contents($f);

// 1. Fix dashboard()
$pattern1 = '/public function dashboard\(\).*?\/\/\s+4\.\s+Kalender/s';
preg_match($pattern1, $c, $matches);

if(isset($matches[0])) {
    $oldDashboardStart = $matches[0];
    
    $newDashboardStart = <<<'EOF'
public function dashboard()
    {
        // 1. Kapasitas & Aset (V2)
        $totalLahan = \App\Models\KonvenLahanV2::count();
        $totalBedengan = \App\Models\KonvenBedenganV2::count();
        $totalTitik = \App\Models\KonvenLubangTanamV2::count();
        $idleHolesCount = \App\Models\KonvenLubangTanamV2::where('status', 'kosong')
            ->where('updated_at', '<=', now()->subDays(5))
            ->count();

        $titikKosong = \App\Models\KonvenLubangTanamV2::where('status', 'kosong')->count();

        // 2. Status Produksi (V2)
        $titikTerisi = \App\Models\KonvenLubangTanamV2::where('status', 'ditanam')->count();
        
        $totalJenisBibit = \App\Models\BibitKonvensional::count(); // Master lama
        $rataPanenBibit = \App\Models\BibitKonvensional::avg('estimasi_panen_hari') ?? 0;

        $siapPanen = \App\Models\KonvenLubangTanamV2::where('status', 'ditanam')
            ->whereNotNull('estimated_harvest_at')
            ->whereDate('estimated_harvest_at', '<=', now())
            ->count();

        $panenBulanIni = \App\Models\KonvenLubangTanamV2::where('status', 'panen')
            ->whereMonth('harvested_at', now()->month)
            ->whereYear('harvested_at', now()->year)
            ->count();

        // 3. Perawatan & Kendala (V2)
        $gagalPanen = \App\Models\KonvenLubangTanamV2::where('status', 'rusak')->count();

        $pemupukanBulanIni = \App\Models\Pemupukan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $penyemprotanBulanIni = \App\Models\Penyemprotan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        // 4. Kalender
EOF;

    $c = str_replace($oldDashboardStart, $newDashboardStart, $c);
    echo "Dashboard updated to V2.\n";
}

// 2. Fix getDashboardPeriodStats()
$pattern2 = '/\$panenBulanIni = .*?\]\);/s';
preg_match($pattern2, $c, $matches2);

if (isset($matches2[0])) {
    $oldStats = $matches2[0];
    
    $newStats = <<<'EOF'
$panenBulanIni = \App\Models\KonvenLubangTanamV2::where('status', 'panen')->whereBetween('harvested_at', [$start, $end])->count();
        $gagalPanen = \App\Models\KonvenLubangTanamV2::where('status', 'rusak')->whereBetween('updated_at', [$start, $end])->count();
        $pemupukanCount = \App\Models\Pemupukan::whereBetween('tanggal', [$start, $end])->count();
        $penyemprotanCount = \App\Models\Penyemprotan::whereBetween('tanggal', [$start, $end])->count();
        $titikDitanam = \App\Models\KonvenLubangTanamV2::where('status', 'ditanam')->whereBetween('planted_at', [$start, $end])->count();

        return response()->json([
            'period_label' => $periodLabel,
            'panen' => $panenBulanIni,
            'gagal' => $gagalPanen,
            'pemupukan' => $pemupukanCount,
            'penyemprotan' => $penyemprotanCount,
            'ditanam' => $titikDitanam,
        ]);
EOF;

    $c = str_replace($oldStats, $newStats, $c);
    echo "Dashboard period stats updated to V2.\n";
}

file_put_contents($f, $c);

