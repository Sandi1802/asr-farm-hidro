<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$method = <<<EOF

    public function tanamMassalZona(Request \$r, \$zona_id)
    {
        \$r->validate([
            'plant_name_1'         => 'required|string|max:100',
            'plant_name_2'         => 'nullable|string|max:100',
            'planted_at'           => 'required|date',
            'estimated_harvest_at' => 'nullable|date',
            'jumlah_tanaman'       => 'required|integer|min:1',
        ]);

        \$combinedPlantName = \$r->plant_name_1;
        if (\$r->filled('plant_name_2')) {
            \$combinedPlantName .= ', ' . \$r->plant_name_2;
        }

        \$zona = KonvenZonaV2::findOrFail(\$zona_id);
        
        // Ambil lubang kosong di semua bedengan dalam zona ini
        \$lubangKosong = KonvenLubangTanamV2::whereHas('bedengan', function (\$q) use (\$zona_id) {
                \$q->where('zona_id', \$zona_id);
            })
            ->where('status', 'kosong')
            ->orderBy('bedengan_id')
            ->orderBy('nomor_lubang')
            ->limit(\$r->jumlah_tanaman)
            ->get();

        if (\$lubangKosong->isEmpty()) {
            return back()->with('error', 'Tidak ada lubang kosong di zona ini.');
        }

        \$updated = 0;
        \DB::transaction(function () use (\$lubangKosong, \$r, \$combinedPlantName, &\$updated) {
            foreach (\$lubangKosong as \$lubang) {
                \$lubang->update([
                    'status'               => 'ditanam',
                    'plant_name'           => \$combinedPlantName,
                    'planted_at'           => \$r->planted_at,
                    'estimated_harvest_at' => \$r->estimated_harvest_at,
                ]);

                KonvenTanamLogV2::create([
                    'lubang_id'   => \$lubang->id,
                    'action_type' => 'tanam',
                    'plant_name'  => \$combinedPlantName,
                    'created_by'  => \Auth::id(),
                ]);
                \$updated++;
            }
        });

        if (\$updated < \$r->jumlah_tanaman) {
            return back()->with('success', "Tanam massal berhasil sebagian (\$updated lubang) karena jumlah lubang kosong tidak mencukupi.");
        }

        return back()->with('success', "Berhasil menanam \$updated tanaman secara acak (massal) di zona ini.");
    }
EOF;

// Insert before the last closing brace
$c = preg_replace('/}\s*$/', $method . "\n}", $c);
file_put_contents($f, $c);
echo "Method tanamMassalZona added to KonvenV2Controller.\n";
