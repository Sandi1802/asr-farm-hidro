<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$old = <<<'EOF'
    public function tanamMassal(Request $r, $bedengan_id)
    {
        $r->validate([
            'plant_name'           => 'required|string|max:100',
            'planted_at'           => 'required|date',
            'estimated_harvest_at' => 'nullable|date',
        ]);

        $bedengan = KonvenBedenganV2::findOrFail($bedengan_id);
        $lubangKosong = KonvenLubangTanamV2::where('bedengan_id', $bedengan_id)
            ->where('status', 'kosong')
            ->get();

        $updated = 0;
        DB::transaction(function () use ($lubangKosong, $r, &$updated) {
            foreach ($lubangKosong as $lubang) {
                $lubang->update([
                    'status'               => 'ditanam',
                    'plant_name'           => $r->plant_name,
                    'planted_at'           => $r->planted_at,
                    'estimated_harvest_at' => $r->estimated_harvest_at,
                ]);
EOF;

$new = <<<'EOF'
    public function tanamMassal(Request $r, $bedengan_id)
    {
        $r->validate([
            'plant_name_1'         => 'required|string|max:100',
            'plant_name_2'         => 'nullable|string|max:100',
            'planted_at'           => 'required|date',
            'estimated_harvest_at' => 'nullable|date',
        ]);

        $combinedPlantName = $r->plant_name_1;
        if ($r->filled('plant_name_2')) {
            $combinedPlantName .= ', ' . $r->plant_name_2;
        }

        $bedengan = KonvenBedenganV2::findOrFail($bedengan_id);
        $lubangKosong = KonvenLubangTanamV2::where('bedengan_id', $bedengan_id)
            ->where('status', 'kosong')
            ->get();

        $updated = 0;
        DB::transaction(function () use ($lubangKosong, $r, $combinedPlantName, &$updated) {
            foreach ($lubangKosong as $lubang) {
                $lubang->update([
                    'status'               => 'ditanam',
                    'plant_name'           => $combinedPlantName,
                    'planted_at'           => $r->planted_at,
                    'estimated_harvest_at' => $r->estimated_harvest_at,
                ]);
EOF;

$c = str_replace($old, $new, $c);

// Also we need to fix the log!
$oldLog = <<<'EOF'
                KonvenTanamLogV2::create([
                    'lubang_id'   => $lubang->id,
                    'action_type' => 'tanam',
                    'plant_name'  => $r->plant_name,
                    'created_by'  => \Illuminate\Support\Facades\Auth::id(),
                ]);
EOF;

$newLog = <<<'EOF'
                KonvenTanamLogV2::create([
                    'lubang_id'   => $lubang->id,
                    'action_type' => 'tanam',
                    'plant_name'  => $combinedPlantName,
                    'created_by'  => \Illuminate\Support\Facades\Auth::id(),
                ]);
EOF;

$c = str_replace($oldLog, $newLog, $c);

file_put_contents($f, $c);
echo "Controller updated.\n";
