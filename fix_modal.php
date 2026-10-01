<?php
$file = 'app/Http/Controllers/HydroponicController.php';
$content = file_get_contents($file);

$oldFunc = <<<'EOF'
    private function buildSiapPanenHtml($readyHoles)
    {
        // Optimasi: Agregasi langsung via SQL (Jauh lebih cepat dari looping model)
        $readyData = \Illuminate\Support\Facades\DB::table('holes')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->join('greenhouses', 'racks.greenhouse_id', '=', 'greenhouses.id')
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, 30) * INTERVAL '1 day')")
            ->selectRaw("
                holes.plant_name,
                greenhouses.name as gh_name,
                racks.name as rack_name,
                racks.catatan_lapangan,
                MIN(holes.planted_at) as min_planted,
                MAX(holes.planted_at) as max_planted,
                COUNT(holes.id) as hole_count
            ")
            ->groupBy('holes.plant_name', 'greenhouses.name', 'racks.name', 'racks.catatan_lapangan')
            ->get();
EOF;

$newFunc = <<<'EOF'
    private function buildSiapPanenHtml($readyHoles)
    {
        if ($readyHoles->isEmpty()) {
            return '<div style="text-align:center; padding:2rem; color:var(--text-muted);"><i class="ph ph-leaf" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>Belum ada tanaman yang diproyeksikan panen.</div>';
        }

        // Optimasi: Agregasi langsung via SQL, memfilter HANYA id yang sudah dikalkulasi
        $ids = $readyHoles->pluck('id')->toArray();
        $readyData = \Illuminate\Support\Facades\DB::table('holes')
            ->leftJoin('rows', 'holes.row_id', '=', 'rows.id')
            ->leftJoin('racks', 'rows.rack_id', '=', 'racks.id')
            ->leftJoin('greenhouses', 'racks.greenhouse_id', '=', 'greenhouses.id')
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->whereIn('holes.id', $ids)
            ->selectRaw("
                holes.plant_name,
                COALESCE(greenhouses.name, 'GH Unknown') as gh_name,
                COALESCE(racks.name, 'Rak Unknown') as rack_name,
                racks.catatan_lapangan,
                MIN(holes.planted_at) as min_planted,
                MAX(holes.planted_at) as max_planted,
                COUNT(holes.id) as hole_count
            ")
            ->groupBy('holes.plant_name', 'greenhouses.name', 'racks.name', 'racks.catatan_lapangan')
            ->get();
EOF;

$content = str_replace($oldFunc, $newFunc, $content);
file_put_contents($file, $content);
echo "Replaced successfully.\n";
