<?php
$f = 'resources/views/konvensional/v2/bedengan-detail.blade.php';
$c = file_get_contents($f);

$oldModal = <<<'EOF'
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Nama Tanaman *</label>
                <input type="text" name="plant_name" required placeholder="Contoh: Pakcoy"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
EOF;

$newModal = <<<'EOF'
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanaman Utama *</label>
                <input type="text" name="plant_name_1" required placeholder="Contoh: Selada"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
            <div style="margin-bottom:1rem;">
                <label style="display:block; margin-bottom:0.4rem; font-size:0.85rem; font-weight:500;">Tanaman Tumpang Sari (Opsional)</label>
                <input type="text" name="plant_name_2" placeholder="Contoh: Daun Bawang"
                       style="width:100%; padding:0.65rem; border:1px solid var(--border-color); border-radius:8px; box-sizing:border-box;">
            </div>
EOF;

$c = str_replace($oldModal, $newModal, $c);
file_put_contents($f, $c);
echo "View updated.\n";
