import re

with open('resources/views/hydroponics/dashboard.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("{{ $reason }}", "{!! $reason !!}")

with open('resources/views/hydroponics/dashboard.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content2 = f.read()

old_damaged = """            $det = json_decode($log->details);
            $pName = $det->plant_name ?? 'Tidak Diketahui';
            $alasan = $det->alasan ?? 'Lainnya';
            $qty = $det->jumlah ?? 0;
            
            $key = $pName . ' (' . $alasan . ')';
            $damagedTotals[$key] = ($damagedTotals[$key] ?? 0) + $qty;"""

new_damaged = """            $det = json_decode($log->details);
            $pName = $det->plant_name ?? 'Tidak Diketahui';
            $alasan = $det->alasan ?? 'Lainnya';
            $catatan = $det->catatan ?? null;
            $qty = $det->jumlah ?? 0;
            
            $key = htmlspecialchars($pName) . ' (' . htmlspecialchars($alasan) . ')';
            if ($catatan) {
                $key .= '<br><span style="font-size:0.85rem; color:var(--text-muted); font-weight:normal; margin-top:4px; display:inline-block;">Catatan: ' . htmlspecialchars($catatan) . '</span>';
            }
            $damagedTotals[$key] = ($damagedTotals[$key] ?? 0) + $qty;"""

content2 = content2.replace(old_damaged, new_damaged)

old_damaged_hist = """                $det = json_decode($log->details);
                $pName = $det->plant_name ?? 'Tidak Diketahui';
                $alasan = $det->alasan ?? 'Lainnya';
                $qty = $det->jumlah ?? 0;
                
                $key = $pName . ' (' . $alasan . ')';
                $damagedTotalsHist[$key] = ($damagedTotalsHist[$key] ?? 0) + $qty;"""

new_damaged_hist = """                $det = json_decode($log->details);
                $pName = $det->plant_name ?? 'Tidak Diketahui';
                $alasan = $det->alasan ?? 'Lainnya';
                $catatan = $det->catatan ?? null;
                $qty = $det->jumlah ?? 0;
                
                $key = htmlspecialchars($pName) . ' (' . htmlspecialchars($alasan) . ')';
                if ($catatan) {
                    $key .= '<br><span style="font-size:0.85rem; color:var(--text-muted); font-weight:normal; margin-top:4px; display:inline-block;">Catatan: ' . htmlspecialchars($catatan) . '</span>';
                }
                $damagedTotalsHist[$key] = ($damagedTotalsHist[$key] ?? 0) + $qty;"""

content2 = content2.replace(old_damaged_hist, new_damaged_hist)

with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content2)

print("Patched.")
