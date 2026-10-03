<?php

$file = 'app/Http/Controllers/KonvenV2Controller.php';
$content = file_get_contents($file);
$content = str_replace("\r\n", "\n", $content);

// 1. Fix bedenganIndex
$targetBedengan = <<<'PHP'
    public function bedenganIndex($zona_id)
    {
        $zona      = KonvenZonaV2::with('kode.posisi.lahan', 'kode.lahan')->findOrFail($zona_id);
        $bedengans = KonvenBedenganV2::where('zona_id', $zona_id)
            ->with(['lubangTanams'])
            ->orderBy('nomor')
            ->get()
            ->map(function ($b) {
                $b->lubang_count  = $b->lubangTanams->count();
                $b->terisi_count  = $b->lubangTanams->whereIn('status', ['ditanam'])->count();
                $b->kosong_count  = $b->lubangTanams->where('status', 'kosong')->count();
                return $b;
            });

        return view('konvensional.v2.bedengan', compact('zona', 'bedengans'));
    }
PHP;

$replacementBedengan = <<<'PHP'
    public function bedenganIndex($zona_id)
    {
        $zona      = KonvenZonaV2::with('kode.posisi.lahan', 'kode.lahan')->findOrFail($zona_id);
        $bedengans = KonvenBedenganV2::where('zona_id', $zona_id)
            ->withCount(['lubangTanams', 'lubangTanams as terisi' => function ($q) {
                $q->whereIn('status', ['ditanam']);
            }, 'lubangTanams as kosong' => function ($q) {
                $q->where('status', 'kosong');
            }])
            ->orderBy('nomor')
            ->get()
            ->map(function ($b) {
                // Ensure variables match blade expectations
                $b->lubang_count  = $b->lubang_tanams_count;
                $b->terisi_count  = $b->terisi;
                $b->kosong_count  = $b->kosong;
                return $b;
            });

        return view('konvensional.v2.bedengan', compact('zona', 'bedengans'));
    }
PHP;

$content = str_replace($targetBedengan, $replacementBedengan, $content);

// 2. Fix duplicate plant names in combinedPlantName for tanamMassalZona
$targetCombinedZona = <<<'PHP'
        $combinedPlantName = $r->plant_name_1;
        if ($r->filled('plant_name_2')) {
            $combinedPlantName .= ', ' . $r->plant_name_2;
        }
PHP;

$replacementCombinedZona = <<<'PHP'
        $combinedPlantName = trim($r->plant_name_1);
        if ($r->filled('plant_name_2') && trim($r->plant_name_2) !== trim($r->plant_name_1)) {
            $combinedPlantName .= ', ' . trim($r->plant_name_2);
        }
PHP;

$content = str_replace($targetCombinedZona, $replacementCombinedZona, $content);

file_put_contents($file, $content);
echo "Fixed KonvenV2Controller.php\n";
