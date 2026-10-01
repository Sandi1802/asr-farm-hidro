<?php
$f = 'app/Http/Controllers/KonvenV2Controller.php';
$c = file_get_contents($f);

$oldStore = <<<'EOF'
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
EOF;

$newStore = <<<'EOF'
            $currentZonesCount = \App\Models\KonvenZonaV2::where('kode_id', $kode_id)->count();
            $jz = (int) $r->jumlah_zona;
            $jb = (int) $r->jumlah_bedengan;
            $jl = (int) $r->jumlah_lubang;
            $prefix = trim($r->prefix_nama);

            $suffix = 'A';
            for ($i = 0; $i < $currentZonesCount; $i++) {
                $suffix++;
            }

            for ($z = 1; $z <= $jz; $z++) {
                $namaZona = $jz == 1 && $prefix != 'Zona' && !preg_match('/[a-zA-Z]$/', $prefix) 
                            ? $prefix 
                            : $prefix . ' ' . $suffix;
EOF;

$c = str_replace($oldStore, $newStore, $c);

// Also we need to increment the suffix inside the loop!
$oldLoopEnd = <<<'EOF'
                $zona = \App\Models\KonvenZonaV2::create([
                    'kode_id' => $kode_id,
                    'nama'    => $namaZona,
                ]);

                if ($jb > 0) {
EOF;

$newLoopEnd = <<<'EOF'
                $zona = \App\Models\KonvenZonaV2::create([
                    'kode_id' => $kode_id,
                    'nama'    => $namaZona,
                ]);
                
                $suffix++; // Increment for next zone

                if ($jb > 0) {
EOF;

$c = str_replace($oldLoopEnd, $newLoopEnd, $c);
file_put_contents($f, $c);


// Update zona.blade.php hint text
$v = 'resources/views/konvensional/v2/zona.blade.php';
$vc = file_get_contents($v);

$vc = str_replace(
    'Sistem akan menambahkan angka di belakangnya (Misal: Zona 1, Zona 2)',
    'Sistem akan menambahkan abjad di belakangnya (Misal: Zona A, Zona B)',
    $vc
);
file_put_contents($v, $vc);

echo "Controller and view updated for alphabet suffix.\n";
