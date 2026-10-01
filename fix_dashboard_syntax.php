<?php
$f = 'app/Http/Controllers/KonvensionalController.php';
$c = file_get_contents($f);

// Find the start of dashboard()
$startPos = strpos($c, 'public function dashboard()');
// Find the start of lahanIndex()
$endPos = strpos($c, 'public function lahanIndex()');

if ($startPos !== false && $endPos !== false) {
    // The part to replace
    $oldPart = substr($c, $startPos, $endPos - $startPos);
    
    $newPart = <<<'EOF'
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

        // 4. Kalender (Mock for V2 since calendar logic might need full rewrite later, return empty for now)
        $calendarJson = json_encode([]);
        $chartKeterisian = ['labels' => [], 'terisi' => [], 'kosong' => []]; // Empty charts for now
        $chartPerawatan = ['labels' => [], 'pemupukan' => [], 'penyemprotan' => []];

        return view('konvensional.dashboard', compact(
            'totalLahan', 'totalBedengan', 'totalTitik', 'titikKosong', 'idleHolesCount',
            'titikTerisi', 'totalJenisBibit', 'rataPanenBibit', 'siapPanen', 'panenBulanIni',
            'gagalPanen', 'pemupukanBulanIni', 'penyemprotanBulanIni',
            'chartKeterisian', 'chartPerawatan', 'calendarJson'
        ));
    }

    public function getDashboardPeriodStats(Request $request)
    {
        $period = $request->query('period', 'month');
        $now = \Carbon\Carbon::now();

        switch ($period) {
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $periodLabel = 'Tahun ' . $now->year;
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $periodLabel = 'Minggu Ini';
                break;
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $periodLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $periodLabel = $now->translatedFormat('F Y');
                break;
        }

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
    }

EOF;
    
    // We add newline to separate clearly
    $c = str_replace($oldPart, $newPart, $c);
    file_put_contents($f, $c);
    echo "Successfully replaced dashboard and stats methods.\n";
} else {
    echo "Could not find start or end pos.\n";
}
