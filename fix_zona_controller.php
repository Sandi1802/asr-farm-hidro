<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$old = <<<'EOF'
    public function zonaIndex($kode_id)
    {
        $kode  = KonvenKodeV2::with('posisi.lahan', 'lahan')->findOrFail($kode_id);
        $zonas = KonvenZonaV2::where('kode_id', $kode_id)
                              ->withCount('bedengans')
                              ->orderBy('nama')
                              ->get();

        return view('konvensional.v2.zona', compact('kode', 'zonas'));
    }
EOF;

$new = <<<'EOF'
    public function zonaIndex($kode_id)
    {
        $kode  = KonvenKodeV2::with('posisi.lahan', 'lahan')->findOrFail($kode_id);
        $zonas = KonvenZonaV2::where('kode_id', $kode_id)
                              ->withCount(['bedengans', 'lubangTanams as total_lubang', 'lubangTanams as terisi' => function ($q) {
                                  $q->where('status', 'ditanam');
                              }])
                              ->orderBy('nama')
                              ->get();

        return view('konvensional.v2.zona', compact('kode', 'zonas'));
    }
EOF;

$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Controller updated.\n";
