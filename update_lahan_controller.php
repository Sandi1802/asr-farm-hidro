<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$old = <<<'EOF'
    public function lahanIndex()
    {
        $lahans = KonvenLahanV2::with(['kodes' => fn($q) => $q->withCount('zonas')])
                               ->withCount('kodes')
                               ->orderByRaw('LENGTH(nama), nama')
                               ->get();
        return view('konvensional.v2.lahan', compact('lahans'));
    }
EOF;

$new = <<<'EOF'
    public function lahanIndex()
    {
        $lahans = KonvenLahanV2::with(['kodes' => fn($q) => $q->withCount('zonas')])
                               ->withCount('kodes')
                               ->orderByRaw('LENGTH(nama), nama')
                               ->get();

        foreach ($lahans as $lahan) {
            $stats = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
                ->join('konven_bedengans', 'konven_lubang_tanams.bedengan_id', '=', 'konven_bedengans.id')
                ->join('konven_zonas', 'konven_bedengans.zona_id', '=', 'konven_zonas.id')
                ->join('konven_kodes', 'konven_zonas.kode_id', '=', 'konven_kodes.id')
                ->where('konven_kodes.lahan_id', $lahan->id)
                ->select(
                    \Illuminate\Support\Facades\DB::raw('COUNT(konven_lubang_tanams.id) as total_lubang'),
                    \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN konven_lubang_tanams.status = \'ditanam\' THEN 1 ELSE 0 END) as terisi')
                )->first();

            $lahan->total_lubang = $stats->total_lubang ?? 0;
            $lahan->terisi = $stats->terisi ?? 0;
        }

        return view('konvensional.v2.lahan', compact('lahans'));
    }
EOF;

$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Controller updated.\n";
