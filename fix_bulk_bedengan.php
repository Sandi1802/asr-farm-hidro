<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$oldStore = <<<'EOF'
    public function bedenganStore(Request $r, $zona_id)
    {
        $zona = KonvenZonaV2::findOrFail($zona_id);
        $r->validate([
            'nomor'                => 'required|integer|min:1',
            'nama_display'         => 'nullable|string|max:100',
            'jumlah_lubang_rencana'=> 'required|integer|min:0|max:500',
        ]);

        DB::transaction(function () use ($r, $zona_id) {
            $bedengan = KonvenBedenganV2::create([
                'zona_id'               => $zona_id,
                'nomor'                 => $r->nomor,
                'nama_display'          => $r->nama_display,
                'jumlah_lubang_rencana' => $r->jumlah_lubang_rencana,
            ]);

            // Auto-generate lubang
            $jumlah = (int) $r->jumlah_lubang_rencana;
            if ($jumlah > 0) {
                $rows = [];
                for ($i = 1; $i <= $jumlah; $i++) {
                    $rows[] = [
                        'bedengan_id'  => $bedengan->id,
                        'nomor_lubang' => $i,
                        'status'       => 'kosong',
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
                KonvenLubangTanamV2::insert($rows);
            }
        });

        return back()->with('success', 'Bedengan berhasil ditambahkan beserta lubang tanam.');
    }
EOF;

$newStore = <<<'EOF'
    public function bedenganStore(Request $r, $zona_id)
    {
        $zona = KonvenZonaV2::findOrFail($zona_id);
        $r->validate([
            'jumlah_bedengan'      => 'required|integer|min:1|max:100',
            'jumlah_lubang_rencana'=> 'required|integer|min:0|max:500',
        ]);

        DB::transaction(function () use ($r, $zona_id) {
            $currentMax = KonvenBedenganV2::where('zona_id', $zona_id)->max('nomor') ?? 0;
            $jumlahBedengan = (int) $r->jumlah_bedengan;
            $jumlahLubang = (int) $r->jumlah_lubang_rencana;

            for ($b = 1; $b <= $jumlahBedengan; $b++) {
                $newNomor = $currentMax + $b;
                
                $bedengan = KonvenBedenganV2::create([
                    'zona_id'               => $zona_id,
                    'nomor'                 => $newNomor,
                    'nama_display'          => null,
                    'jumlah_lubang_rencana' => $jumlahLubang,
                ]);

                if ($jumlahLubang > 0) {
                    $rows = [];
                    for ($i = 1; $i <= $jumlahLubang; $i++) {
                        $rows[] = [
                            'bedengan_id'  => $bedengan->id,
                            'nomor_lubang' => $i,
                            'status'       => 'kosong',
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                    KonvenLubangTanamV2::insert($rows);
                }
            }
        });

        return back()->with('success', $r->jumlah_bedengan . ' Bedengan berhasil ditambahkan beserta lubang tanam.');
    }
EOF;

$c = str_replace($oldStore, $newStore, $c);
file_put_contents($f, $c);


// Update View
$v = 'resources/views/konvensional/v2/bedengan.blade.php';
$vc = file_get_contents($v);

$oldModal = <<<'EOF'
              <div style="margin-bottom:1rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nomor Bedengan *</label>
                  <input type="number" name="nomor" required min="1" placeholder="1"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
              <div style="margin-bottom:1rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Display <small style="color:var(--text-muted);">(opsional)</small></label>
                  <input type="text" name="nama_display" placeholder="Contoh: Bedengan Utara"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
              <div style="margin-bottom:1.25rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">
                      Jumlah Lubang Tanam * <small style="color:var(--text-muted);">(otomatis dibuat)</small>
                  </label>
                  <input type="number" name="jumlah_lubang_rencana" required min="0" value="5"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
EOF;

$newModal = <<<'EOF'
              <div style="margin-bottom:1rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Bedengan (Dibuat Sekaligus) *</label>
                  <input type="number" name="jumlah_bedengan" required min="1" max="50" value="1"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                  <small style="color:var(--text-muted); font-size:0.75rem;">Nomor bedengan akan otomatis meneruskan nomor terakhir.</small>
              </div>
              <div style="margin-bottom:1.25rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">
                      Jumlah Lubang Tanam per Bedengan *
                  </label>
                  <input type="number" name="jumlah_lubang_rencana" required min="0" value="5"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
EOF;

$vc = str_replace($oldModal, $newModal, $vc);
file_put_contents($v, $vc);

echo "Bedengan store logic and view modified for bulk generation.\n";
