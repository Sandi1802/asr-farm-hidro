<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$oldStore = <<<'EOF'
    public function zonaStore(Request $r, $kode_id)
    {
        KonvenKodeV2::findOrFail($kode_id);
        $r->validate(['nama' => 'required|string|max:100']);

        KonvenZonaV2::create(['kode_id' => $kode_id, 'nama' => $r->nama]);
        return back()->with('success', 'Zona berhasil ditambahkan.');
    }
EOF;

$newStore = <<<'EOF'
    public function zonaStore(Request $r, $kode_id)
    {
        KonvenKodeV2::findOrFail($kode_id);
        $r->validate([
            'prefix_nama'     => 'required|string|max:50',
            'jumlah_zona'     => 'required|integer|min:1|max:20',
            'jumlah_bedengan' => 'required|integer|min:0|max:50',
            'jumlah_lubang'   => 'required|integer|min:0|max:500',
        ]);

        DB::transaction(function () use ($r, $kode_id) {
            $currentZonesCount = \App\Models\KonvenZonaV2::where('kode_id', $kode_id)->count();
            $jz = (int) $r->jumlah_zona;
            $jb = (int) $r->jumlah_bedengan;
            $jl = (int) $r->jumlah_lubang;
            $prefix = trim($r->prefix_nama);

            for ($z = 1; $z <= $jz; $z++) {
                $nomorZona = $currentZonesCount + $z;
                // If only 1 zone and prefix is something like "Utara", we might just name it "Utara". 
                // But appending the number is safer to avoid duplicates.
                $namaZona = $jz == 1 && $prefix != 'Zona' && !preg_match('/[0-9]$/', $prefix) 
                            ? $prefix 
                            : $prefix . ' ' . $nomorZona;

                $zona = \App\Models\KonvenZonaV2::create([
                    'kode_id' => $kode_id,
                    'nama'    => $namaZona,
                ]);

                if ($jb > 0) {
                    for ($b = 1; $b <= $jb; $b++) {
                        $bedengan = \App\Models\KonvenBedenganV2::create([
                            'zona_id'               => $zona->id,
                            'nomor'                 => $b,
                            'nama_display'          => null,
                            'jumlah_lubang_rencana' => $jl,
                        ]);

                        if ($jl > 0) {
                            $rows = [];
                            $now = now();
                            for ($i = 1; $i <= $jl; $i++) {
                                $rows[] = [
                                    'bedengan_id'  => $bedengan->id,
                                    'nomor_lubang' => $i,
                                    'status'       => 'kosong',
                                    'created_at'   => $now,
                                    'updated_at'   => $now,
                                ];
                            }
                            // Insert holes in bulk for performance
                            \App\Models\KonvenLubangTanamV2::insert($rows);
                        }
                    }
                }
            }
        });

        return back()->with('success', $r->jumlah_zona . ' Zona berhasil ditambahkan beserta struktur bedengan & lubang.');
    }
EOF;

$c = str_replace($oldStore, $newStore, $c);
file_put_contents($f, $c);

// Update view zona.blade.php modal
$v = 'resources/views/konvensional/v2/zona.blade.php';
$vc = file_get_contents($v);

$oldModal = <<<'EOF'
          <form action="{{ route('konven.v2.zona.store', $kode->id) }}" method="POST">
              @csrf
              <div style="margin-bottom:1.25rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Zona *</label>
                  <input type="text" name="nama" required placeholder="Contoh: Zona 1, Zona 2"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
              <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                  <button type="button" onclick="document.getElementById('modalTambahZona').style.display='none'"
                          style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                  <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan</button>
              </div>
          </form>
EOF;

$newModal = <<<'EOF'
          <form action="{{ route('konven.v2.zona.store', $kode->id) }}" method="POST">
              @csrf
              <div style="margin-bottom:1rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Zona yang Dibuat *</label>
                  <input type="number" name="jumlah_zona" required min="1" max="20" value="1"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
              <div style="margin-bottom:1rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Prefix Nama Zona *</label>
                  <input type="text" name="prefix_nama" required value="Zona" placeholder="Contoh: Zona"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
                  <small style="color:var(--text-muted); font-size:0.75rem;">Sistem akan menambahkan angka di belakangnya (Misal: Zona 1, Zona 2).</small>
              </div>
              <div style="margin-bottom:1rem; padding-top:1rem; border-top:1px dashed var(--border-color);">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Bedengan per Zona</label>
                  <input type="number" name="jumlah_bedengan" required min="0" max="50" value="5"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
              <div style="margin-bottom:1.5rem;">
                  <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Jumlah Lubang per Bedengan</label>
                  <input type="number" name="jumlah_lubang" required min="0" max="500" value="50"
                         style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
              </div>
              <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                  <button type="button" onclick="document.getElementById('modalTambahZona').style.display='none'"
                          style="padding:0.65rem 1.2rem; border:1px solid var(--border-color); background:white; border-radius:8px; cursor:pointer;">Batal</button>
                  <button type="submit" style="padding:0.65rem 1.2rem; border:none; background:var(--asr-green); color:white; border-radius:8px; cursor:pointer; font-weight:600;">Simpan & Generate</button>
              </div>
          </form>
EOF;

$vc = str_replace($oldModal, $newModal, $vc);
file_put_contents($v, $vc);

echo "Zona modal and controller modified for GRAND BULK GENERATION.\n";
