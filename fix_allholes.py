import re

with open('resources/views/hydroponics/greenhouses.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# I also need to make sure the loop uses $pData and not $holes
old_modal = """                @if($cntDitanam > 0)
                @php
                    $ditanamGrouped = $allHoles->where('status', 'ditanam')->diff($readyHoles)->whereNotNull('plant_name')->groupBy('plant_name');
                @endphp
                <div id="ditanamModal_{{ $gh->id }}\""""

new_modal = """                @if($cntDitanam > 0)
                <div id="ditanamModal_{{ $gh->id }}\""""

content = content.replace(old_modal, new_modal)

old_loop = """                                @foreach($ditanamGrouped as $plantName => $holes)
                                @php
                                    $rackGrouped = [];
                                    foreach($holes as $h) {
                                        $r = optional(optional($h->row)->rack)->name ?? 'Unknown Rak';
                                        if(!isset($rackGrouped[$r])) $rackGrouped[$r] = 0;
                                        $rackGrouped[$r]++;
                                    }
                                    uksort($rackGrouped, 'strnatcmp');
                                @endphp
                                <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; background: #f0fdf4;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                        <div style="background: #dcfce7; color: #16a34a; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                            {{ number_format($holes->count(), 0, ',', '.') }} Lubang
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                        @foreach($rackGrouped as $rName => $rCount)"""

new_loop = """                                @foreach($ditanamGrouped as $plantName => $pData)
                                <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; background: #f0fdf4;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                        <div style="background: #dcfce7; color: #16a34a; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                            {{ number_format($pData['total'], 0, ',', '.') }} Lubang
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                        @foreach($pData['racks'] as $rName => $rCount)"""

if old_loop in content:
    content = content.replace(old_loop, new_loop)
else:
    print("WARNING: loop replace failed")

with open('resources/views/hydroponics/greenhouses.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Fixed")
