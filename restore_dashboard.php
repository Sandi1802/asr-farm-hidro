<?php
$f = 'app/Http/Controllers/KonvensionalController.php';
$c = file_get_contents($f);

// 1. Rewrite chartKeterisian
$chartKeterisianCode = <<<'EOF'
        // Chart Keterisian (V2)
        $chartKeterisian = ['labels' => [], 'terisi' => [], 'kosong' => []];
        $lahans = \App\Models\KonvenLahanV2::all();
        
        foreach ($lahans as $lahan) {
            $chartKeterisian['labels'][] = $lahan->nama;
            
            // Get all hole counts for this lahan
            $terisi = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams_v2')
                ->join('konven_bedengans_v2', 'konven_lubang_tanams_v2.bedengan_id', '=', 'konven_bedengans_v2.id')
                ->join('konven_zonas_v2', 'konven_bedengans_v2.zona_id', '=', 'konven_zonas_v2.id')
                ->join('konven_kodes_v2', 'konven_zonas_v2.kode_id', '=', 'konven_kodes_v2.id')
                ->where('konven_kodes_v2.lahan_id', $lahan->id)
                ->where('konven_lubang_tanams_v2.status', 'ditanam')
                ->count();
                
            $kosong = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams_v2')
                ->join('konven_bedengans_v2', 'konven_lubang_tanams_v2.bedengan_id', '=', 'konven_bedengans_v2.id')
                ->join('konven_zonas_v2', 'konven_bedengans_v2.zona_id', '=', 'konven_zonas_v2.id')
                ->join('konven_kodes_v2', 'konven_zonas_v2.kode_id', '=', 'konven_kodes_v2.id')
                ->where('konven_kodes_v2.lahan_id', $lahan->id)
                ->where('konven_lubang_tanams_v2.status', 'kosong')
                ->count();
                
            $chartKeterisian['terisi'][] = $terisi;
            $chartKeterisian['kosong'][] = $kosong;
        }
EOF;

// 2. Rewrite chartPerawatan (Uses old Pemupukan and Penyemprotan)
$chartPerawatanCode = <<<'EOF'
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
EOF;

// 3. Rewrite calendarJson
$calendarJsonCode = <<<'EOF'
        // Kalender (V2)
        $calendarData = [];

        // Penanaman
        $tanamGroups = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams_v2')
            ->where('status', 'ditanam')
            ->whereNotNull('planted_at')
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(planted_at) as date'), 'plant_name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(planted_at)'), 'plant_name')
            ->get();
            
        foreach ($tanamGroups as $g) {
            $date = $g->date;
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'tanam',
                'gh_name' => 'Penanaman ' . ($g->plant_name ?: 'Tanaman'),
                'hole_count' => $g->count
            ];
        }
        
        // Panen
        $panenGroups = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams_v2')
            ->where('status', 'ditanam')
            ->whereNotNull('estimated_harvest_at')
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(estimated_harvest_at) as date'), 'plant_name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(estimated_harvest_at)'), 'plant_name')
            ->get();
            
        foreach ($panenGroups as $g) {
            $date = $g->date;
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'harvest',
                'gh_name' => 'Estimasi Panen ' . ($g->plant_name ?: 'Tanaman'),
                'hole_count' => $g->count
            ];
        }
        
        // Pemupukan (Old Data)
        $pemupukans = \App\Models\Pemupukan::whereNotNull('tanggal')->get();
        foreach ($pemupukans as $p) {
            $date = \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d');
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'custom',
                'gh_name' => 'Pemupukan ' . ($p->jenis_pupuk ?: ''),
                'hole_count' => 0
            ];
        }
        
        // Penyemprotan (Old Data)
        $penyemprotans = \App\Models\Penyemprotan::whereNotNull('tanggal')->get();
        foreach ($penyemprotans as $p) {
            $date = \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d');
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'custom',
                'gh_name' => 'Penyemprotan ' . ($p->jenis_obat ?: ''),
                'hole_count' => 0
            ];
        }

        $calendarJson = json_encode($calendarData);
EOF;

// We will use regex to replace the mocked ones.
// In KonvensionalController.php:
// $calendarJson = json_encode([]);
// $chartKeterisian = ['labels' => [], 'terisi' => [], 'kosong' => []]; // Empty charts for now
// $chartPerawatan = ['labels' => [], 'pemupukan' => [], 'penyemprotan' => []];

$oldBlock = <<<'EOF'
        // 4. Kalender (Mock for V2 since calendar logic might need full rewrite later, return empty for now)
        $calendarJson = json_encode([]);
        $chartKeterisian = ['labels' => [], 'terisi' => [], 'kosong' => []]; // Empty charts for now
        $chartPerawatan = ['labels' => [], 'pemupukan' => [], 'penyemprotan' => []];
EOF;

$newBlock = $chartKeterisianCode . "\n\n" . $chartPerawatanCode . "\n\n" . $calendarJsonCode;

$c = str_replace($oldBlock, $newBlock, $c);
file_put_contents($f, $c);

echo "Dashboard functionality restored.\n";
