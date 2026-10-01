<?php
$file = 'resources/views/hydroponics/damage-notes.blade.php';
$content = file_get_contents($file);

// Replace title and text
$content = str_replace('Catatan Kerusakan', 'Kerusakan Aset', $content);
$content = str_replace('Laporan dan pencatatan kerusakan tanaman di seluruh greenhouse', 'Laporan dan pencatatan kerusakan aset (pompa, pipa, dll) di seluruh area', $content);
$content = str_replace('Cari tanaman', 'Cari aset', $content);
$content = str_replace('Daftar Catatan Kerusakan', 'Daftar Kerusakan Aset', $content);

// Form routes
$content = str_replace("hydroponics.damage-notes", "hydroponics.asset-damage-notes", $content);
$content = str_replace("hydroponics/damage-notes", "hydroponics/asset-damages", $content);

// Table Columns
$content = str_replace('<th>Tanaman</th>', '<th>Aset</th>', $content);
$content = str_replace('<th style="padding: 1rem; border-bottom: 2px solid #e5e7eb; white-space:nowrap;">Jenis Kerusakan</th>', '', $content); 

// Table Row Data
$content = preg_replace('/<td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">\s*<div style="font-weight: 600; color: var\(--text-main\); display: flex; align-items: center; gap: 0\.4rem;">\s*<i class="ph ph-plant" style="color: #16a34a;"><\/i>\s*\{\{ \$note->plant_name \?\? \'-\' \}\}\s*<\/div>\s*<\/td>/s', '<td><div style="font-weight:600; color:var(--text-main); display:flex; align-items:center; gap:0.4rem;"><i class="ph ph-hard-drives" style="color:#0ea5e9;"></i> {{ $note->asset_name }}</div></td>', $content);
$content = preg_replace('/<td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">\s*<span class="damage-badge.*?>\s*<i class="ph ph-warning"><\/i>\s*\{\{ \$note->damage_type \?\? \'Umum\' \}\}\s*<\/span>\s*<\/td>/s', '', $content);

// Modal Form Inputs
$content = preg_replace('/<label style="font-size: 0\.85rem; font-weight: 600; color: var\(--text-muted\); margin-bottom: 0\.5rem; display: block;">Tanaman<\/label>.*?<\/select>/s', '<label style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem; display: block;">Nama Aset</label><input type="text" name="asset_name" required placeholder="Contoh: Pompa GH 1" style="width: 100%; padding: 0.65rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px; font-family: inherit; font-size: 0.95rem;">', $content);

$content = preg_replace('/<div style="flex: 1; min-width: 200px;">\s*<label style="font-size: 0\.85rem; font-weight: 600; color: var\(--text-muted\); margin-bottom: 0\.5rem; display: block;">Jenis Kerusakan<\/label>.*?<\/select>\s*<\/div>/s', '', $content);

$content = preg_replace('/<div style="flex: 1; min-width: 200px;">\s*<label style="font-size: 0\.85rem; font-weight: 600; color: var\(--text-muted\); margin-bottom: 0\.5rem; display: block;">Lokasi Tanaman \(Opsional\)<\/label>.*?<\/select>\s*<\/div>/s', '', $content);

$content = str_replace('Lokasi Manual / Kustom (Opsional)', 'Lokasi Aset', $content);
$content = str_replace('name="location_manual"', 'name="location"', $content);

file_put_contents('resources/views/hydroponics/asset-damage-notes.blade.php', $content);
echo "Asset page rewritten.\n";
