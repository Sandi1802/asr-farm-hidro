<?php
$f = 'app/Http/Controllers/Api/KonvensionalApiController.php';
$c = file_get_contents($f);

if (!str_contains($c, 'public function tanamZona')) {
    $method = <<<EOF

    public function tanamZona(Request \$r, \$id)
    {
        \$r->validate([
            'plant_name_1' => 'required|string',
            'plant_name_2' => 'nullable|string',
            'planted_at' => 'required|date',
            'jumlah' => 'required|integer|min:1'
        ]);

        \$combined = \$r->plant_name_1;
        if (\$r->filled('plant_name_2')) {
            \$combined .= ', ' . \$r->plant_name_2;
        }

        \$lubangKosong = \App\Models\KonvenLubangTanamV2::whereHas('bedengan', function(\$q) use (\$id) {
            \$q->where('zona_id', \$id);
        })->where('status', 'kosong')
          ->orderBy('bedengan_id')->orderBy('nomor_lubang')
          ->limit(\$r->jumlah)->get();

        if (\$lubangKosong->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada lubang kosong di zona ini']);
        }

        \$updated = 0;
        \DB::transaction(function() use (\$lubangKosong, \$r, \$combined, &\$updated) {
            foreach (\$lubangKosong as \$lubang) {
                \$lubang->update([
                    'status' => 'ditanam',
                    'plant_name' => \$combined,
                    'planted_at' => \$r->planted_at
                ]);
                \App\Models\KonvenTanamLogV2::create([
                    'lubang_id' => \$lubang->id,
                    'action_type' => 'tanam',
                    'plant_name' => \$combined,
                    'created_by' => auth()->id() ?? 1
                ]);
                \$updated++;
            }
        });

        return response()->json([
            'success' => true,
            'message' => "Berhasil menanam \$updated tanaman di zona ini"
        ]);
    }
EOF;
    
    // insert before closing brace
    $c = preg_replace('/}\s*$/', $method . "\n}", $c);
    file_put_contents($f, $c);
}
echo "Added tanamZona API method.\n";
