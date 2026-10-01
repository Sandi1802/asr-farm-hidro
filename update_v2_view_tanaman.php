<?php
$f = 'resources/views/konvensional/v2/bedengan-detail.blade.php';
$c = file_get_contents($f);

// For Modal Edit Lubang
$oldEditInput = '<input type="text" id="editLubangPlant" name="plant_name" placeholder="Contoh: Pakcoy, Selada"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">';

$newEditSelect = <<<EOF
<select id="editLubangPlant" name="plant_name" style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
    <option value="">- Kosong (Tidak Ditanam) -</option>
    @foreach(\$masterTanaman as \$t)
        <option value="{{ \$t->nama_lengkap }}">{{ \$t->nama_lengkap }} ({{ \$t->lama_hari_ke_panen }} Hari)</option>
    @endforeach
</select>
EOF;

$c = str_replace($oldEditInput, $newEditSelect, $c);

// For Modal Tanam Massal (Tumpang Sari)
$oldTanam1 = '<input type="text" name="plant_name_1" required placeholder="Contoh: Selada"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">';

$newTanam1 = <<<EOF
<select name="plant_name_1" required style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
    <option value="" disabled selected>- Pilih Tanaman Utama -</option>
    @foreach(\$masterTanaman as \$t)
        <option value="{{ \$t->nama_lengkap }}">{{ \$t->nama_lengkap }} ({{ \$t->lama_hari_ke_panen }} Hari)</option>
    @endforeach
</select>
EOF;

$oldTanam2 = '<input type="text" name="plant_name_2" placeholder="Contoh: Daun Bawang"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">';

$newTanam2 = <<<EOF
<select name="plant_name_2" style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
    <option value="">- Tidak Ada Tambahan -</option>
    @foreach(\$masterTanaman as \$t)
        <option value="{{ \$t->nama_lengkap }}">{{ \$t->nama_lengkap }} ({{ \$t->lama_hari_ke_panen }} Hari)</option>
    @endforeach
</select>
EOF;

$c = str_replace($oldTanam1, $newTanam1, $c);
$c = str_replace($oldTanam2, $newTanam2, $c);

file_put_contents($f, $c);
echo "bedengan-detail.blade.php updated.\n";
