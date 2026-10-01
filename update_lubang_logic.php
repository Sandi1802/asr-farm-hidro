<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$oldFunc = <<<'EOF'
    public function bedenganUpdate(Request $r, $id)
    {
        $r->validate([
            'nomor'        => 'required|integer|min:1',
            'nama_display' => 'nullable|string|max:100',
        ]);

        KonvenBedenganV2::findOrFail($id)->update($r->only('nomor', 'nama_display'));
        return back()->with('success', 'Bedengan berhasil diperbarui.');
    }
EOF;

$newFunc = <<<'EOF'
    public function bedenganUpdate(Request $r, $id)
    {
        $r->validate([
            'nomor'        => 'required|integer|min:1',
            'nama_display' => 'nullable|string|max:100',
            'jumlah_lubang'=> 'nullable|integer|min:1',
        ]);

        $bedengan = KonvenBedenganV2::findOrFail($id);
        $bedengan->update($r->only('nomor', 'nama_display'));
        
        if ($r->filled('jumlah_lubang')) {
            $newTotal = (int) $r->jumlah_lubang;
            $currentHoles = $bedengan->lubangTanams()->count();
            
            if ($newTotal > $currentHoles) {
                // Add new holes
                $holes = [];
                for ($i = $currentHoles + 1; $i <= $newTotal; $i++) {
                    $holes[] = [
                        'bedengan_id' => $bedengan->id,
                        'nomor_lubang' => $i,
                        'status' => 'kosong',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                KonvenLubangTanamV2::insert($holes);
                $bedengan->update(['jumlah_lubang_rencana' => $newTotal]);
            } elseif ($newTotal < $currentHoles) {
                // Delete holes
                $holesToDelete = $bedengan->lubangTanams()->where('nomor_lubang', '>', $newTotal)->get();
                foreach ($holesToDelete as $hole) {
                    if ($hole->status !== 'kosong') {
                        return back()->with('error', "Gagal mengurangi lubang! Lubang nomor {$hole->nomor_lubang} masih memiliki status '{$hole->status}'. Kosongkan terlebih dahulu.");
                    }
                }
                $bedengan->lubangTanams()->where('nomor_lubang', '>', $newTotal)->delete();
                $bedengan->update(['jumlah_lubang_rencana' => $newTotal]);
            }
        }

        return back()->with('success', 'Bedengan berhasil diperbarui.');
    }
EOF;

$c = str_replace($oldFunc, $newFunc, $c);
file_put_contents($f, $c);
echo "Controller updated.\n";
