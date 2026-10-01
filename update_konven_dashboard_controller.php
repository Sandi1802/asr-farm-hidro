<?php
$f = 'app/Http/Controllers/KonvensionalController.php';
$c = file_get_contents($f);

$old = <<<'EOF'
        // Chart Perawatan (Old Data)
        $chartPerawatan = ['labels' => [], 'pemupukan' => [], 'penyemprotan' => []];
        for ($i = 3; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $label = $startDate->format('d M') . ' - ' . $endDate->format('d M');
            
            $chartPerawatan['labels'][] = $label;
            $chartPerawatan['pemupukan'][] = \App\Models\Pemupukan::whereBetween('tanggal', [$startDate, $endDate])->count();
            $chartPerawatan['penyemprotan'][] = \App\Models\Penyemprotan::whereBetween('tanggal', [$startDate, $endDate])->count();
        }

        // Kalender (V2)
EOF;

$new = <<<'EOF'
        // Chart Perawatan (Old Data)
        $chartPerawatan = ['labels' => [], 'pemupukan' => [], 'penyemprotan' => []];
        for ($i = 3; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $label = $startDate->format('d M') . ' - ' . $endDate->format('d M');
            
            $chartPerawatan['labels'][] = $label;
            $chartPerawatan['pemupukan'][] = \App\Models\Pemupukan::whereBetween('tanggal', [$startDate, $endDate])->count();
            $chartPerawatan['penyemprotan'][] = \App\Models\Penyemprotan::whereBetween('tanggal', [$startDate, $endDate])->count();
        }

        // Top Tanam & Top Panen (V2 Tumpang Sari Support)
        $allTanam = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
            ->whereNotNull('plant_name')->where('plant_name', '!=', '')
            ->get();
        
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
        
        arsort($tanamCounts);
        $topTanamList = array_slice($tanamCounts, 0, 8);
        $chartTopTanam = [
            'labels' => array_keys($topTanamList),
            'data' => array_values($topTanamList)
        ];

        arsort($panenCounts);
        $topPanenList = array_slice($panenCounts, 0, 8);
        $chartTopPanen = [
            'labels' => array_keys($topPanenList),
            'data' => array_values($topPanenList)
        ];

        // Kalender (V2)
EOF;
$c = str_replace($old, $new, $c);

$oldReturn = "'chartKeterisian', 'chartPerawatan', 'calendarJson'";
$newReturn = "'chartKeterisian', 'chartPerawatan', 'calendarJson', 'chartTopTanam', 'chartTopPanen'";
$c = str_replace($oldReturn, $newReturn, $c);

file_put_contents($f, $c);
echo "Controller updated with graphs.\n";
