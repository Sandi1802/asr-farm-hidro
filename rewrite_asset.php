<?php
$file = 'resources/views/hydroponics/asset-damage-notes.blade.php';
$content = file_get_contents($file);

// Replace title and text
$content = str_replace('Catatan Kerusakan', 'Kerusakan Aset', $content);
$content = str_replace('Laporan dan pencatatan kerusakan tanaman di seluruh greenhouse', 'Laporan dan pencatatan kerusakan aset (pompa, pipa, dll) di seluruh area', $content);
$content = str_replace('Cari tanaman', 'Cari aset', $content);
$content = str_replace('Daftar Catatan Kerusakan', 'Daftar Kerusakan Aset', $content);

// Form routes
$content = str_replace("route('hydroponics.damage-notes.store')", "route('hydroponics.asset-damage-notes.store')", $content);
$content = preg_replace("/route\('hydroponics\.damage-notes\.update',\s*\\$note->id\)/", "route('hydroponics.asset-damage-notes.update', \$note->id)", $content);
$content = preg_replace("/route\('hydroponics\.damage-notes\.destroy',\s*\\$note->id\)/", "route('hydroponics.asset-damage-notes.destroy', \$note->id)", $content);
$content = str_replace("hydroponics/damage-notes", "hydroponics/asset-damage-notes", $content);

// Table Columns
$content = str_replace('<th>Tanaman</th>', '<th>Aset</th>', $content);
$content = str_replace('<th>Jenis Kerusakan</th>', '', $content); // Remove damage_type col

// Table Row Data
$content = preg_replace("/<td[^>]*>\s*<div[^>]*>\s*<i[^>]*><\/i>\s*\{\{\s*\\$note->plant_name\s*\?\?\s*'-'\s*\}\}\s*<\/div>\s*<\/td>/s", '<td><div style="font-weight:600; color:var(--text-main); display:flex; align-items:center; gap:0.4rem;"><i class="ph ph-hard-drives" style="color:#0ea5e9;"></i> {{ $note->asset_name }}</div></td>', $content);
$content = preg_replace("/<td[^>]*>\s*<span[^>]*>\s*<i[^>]*><\/i>\s*\{\{\s*\\$note->damage_type\s*\?\?\s*'Umum'\s*\}\}\s*<\/span>\s*<\/td>/s", '', $content); // Remove damage_type cell

// Modal Form Inputs
$content = preg_replace("/<label>Tanaman<\/label>.*?<\/select>/s", '<label>Nama Aset</label><input type="text" name="asset_name" required placeholder="Contoh: Pompa GH 1" style="width:100%; padding:0.65rem; border:1px solid #d1d5db; border-radius:8px; font-family:inherit;">', $content);
$content = preg_replace("/<div style=\"flex:1; min-width:200px;\">\s*<label>Jenis Kerusakan<\/label>.*?<\/select>\s*<\/div>/s", '', $content); // Remove damage_type input
$content = preg_replace("/<div style=\"flex:1; min-width:200px;\">\s*<label>Lokasi Tanaman<\/label>.*?<\/select>\s*<\/div>/s", '', $content); // Remove hole_id input
$content = str_replace('Lokasi Manual / Kustom', 'Lokasi Aset', $content);
$content = str_replace('name="location_manual"', 'name="location"', $content);

file_put_contents($file, $content);
echo "Asset page rewritten.\n";
