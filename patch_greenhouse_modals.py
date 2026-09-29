import re

with open('resources/views/hydroponics/greenhouses.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the span for Ditanam
old_ditanam = """<td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span style="background: #dcfce7; color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;">{{ number_format($cntDitanam, 0, ',', '.') }}</span>
                    </td>"""

new_ditanam = """<td style="padding: 1rem; vertical-align: middle; text-align: center;">
                        <span @if($cntDitanam > 0) onclick="document.getElementById('ditanamModal_{{ $gh->id }}').style.display='flex'" style="background: #dcfce7; color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700; cursor: pointer; text-decoration: underline;" @else style="background: #dcfce7; color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 700;" @endif>
                            {{ number_format($cntDitanam, 0, ',', '.') }}
                        </span>
                    </td>"""

content = content.replace(old_ditanam, new_ditanam)

# Replace the readyModal with both readyModal and ditanamModal
old_modals = """                                @if($cntReady > 0)
                <div id="readyModal_{{ $gh->id }}" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(2px);">
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; width: 100%; max-width: 500px; max-height: 85vh; display:flex; flex-direction:column; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                            <h3 style="margin:0; font-size: 1.15rem; font-weight: 700; color:var(--text-main);"><i class="ph ph-trophy" style="color:#ea580c; margin-right:8px;"></i> Tanaman Siap Panen ({{ $gh->name }})</h3>
                            <button onclick="document.getElementById('readyModal_{{ $gh->id }}').style.display='none'" style="border:none; background:transparent; cursor:pointer; font-size:1.2rem; color:var(--text-muted);"><i class="ph ph-x"></i></button>
                        </div>
                        <div style="overflow-y:auto; flex-grow:1; padding-right:0.5rem;">
                            <ul style="list-style:none; padding:0; margin:0;">
                                @foreach($readyGrouped as $plantName => $holes)
                                <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; display:flex; justify-content:space-between; align-items:center; background: #fdfaf5;">
                                    <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                    <div style="background: #ffedd5; color: #ea580c; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                        {{ number_format($holes->count(), 0, ',', '.') }} Lubang
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif"""

new_modals = """                                @if($cntReady > 0)
                <div id="readyModal_{{ $gh->id }}" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(2px);">
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; width: 100%; max-width: 600px; max-height: 85vh; display:flex; flex-direction:column; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                            <h3 style="margin:0; font-size: 1.15rem; font-weight: 700; color:var(--text-main);"><i class="ph ph-trophy" style="color:#ea580c; margin-right:8px;"></i> Tanaman Siap Panen ({{ $gh->name }})</h3>
                            <button onclick="document.getElementById('readyModal_{{ $gh->id }}').style.display='none'" style="border:none; background:transparent; cursor:pointer; font-size:1.2rem; color:var(--text-muted);"><i class="ph ph-x"></i></button>
                        </div>
                        <div style="overflow-y:auto; flex-grow:1; padding-right:0.5rem;">
                            <ul style="list-style:none; padding:0; margin:0;">
                                @foreach($readyGrouped as $plantName => $holes)
                                @php
                                    $rackGrouped = [];
                                    foreach($holes as $h) {
                                        $r = optional(optional($h->row)->rack)->name ?? 'Unknown Rak';
                                        if(!isset($rackGrouped[$r])) $rackGrouped[$r] = 0;
                                        $rackGrouped[$r]++;
                                    }
                                    arsort($rackGrouped);
                                @endphp
                                <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; background: #fdfaf5;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                        <div style="background: #ffedd5; color: #ea580c; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                            {{ number_format($holes->count(), 0, ',', '.') }} Lubang
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                        @foreach($rackGrouped as $rName => $rCount)
                                            <span style="background: white; border: 1px solid #fed7aa; padding: 2px 6px; border-radius: 4px; color: #9a3412;">{{ $rName }}: {{ $rCount }}</span>
                                        @endforeach
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($cntDitanam > 0)
                @php
                    $ditanamGrouped = $allHoles->where('status', 'ditanam')->diff($readyHoles)->whereNotNull('plant_name')->groupBy('plant_name');
                @endphp
                <div id="ditanamModal_{{ $gh->id }}" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(2px);">
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; width: 100%; max-width: 600px; max-height: 85vh; display:flex; flex-direction:column; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                            <h3 style="margin:0; font-size: 1.15rem; font-weight: 700; color:var(--text-main);"><i class="ph ph-plant" style="color:#16a34a; margin-right:8px;"></i> Tanaman Sedang Ditanam ({{ $gh->name }})</h3>
                            <button onclick="document.getElementById('ditanamModal_{{ $gh->id }}').style.display='none'" style="border:none; background:transparent; cursor:pointer; font-size:1.2rem; color:var(--text-muted);"><i class="ph ph-x"></i></button>
                        </div>
                        <div style="overflow-y:auto; flex-grow:1; padding-right:0.5rem;">
                            <ul style="list-style:none; padding:0; margin:0;">
                                @foreach($ditanamGrouped as $plantName => $holes)
                                @php
                                    $rackGrouped = [];
                                    foreach($holes as $h) {
                                        $r = optional(optional($h->row)->rack)->name ?? 'Unknown Rak';
                                        if(!isset($rackGrouped[$r])) $rackGrouped[$r] = 0;
                                        $rackGrouped[$r]++;
                                    }
                                    arsort($rackGrouped);
                                @endphp
                                <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; background: #f0fdf4;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                        <div style="background: #dcfce7; color: #16a34a; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                            {{ number_format($holes->count(), 0, ',', '.') }} Lubang
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                        @foreach($rackGrouped as $rName => $rCount)
                                            <span style="background: white; border: 1px solid #bbf7d0; padding: 2px 6px; border-radius: 4px; color: #166534;">{{ $rName }}: {{ $rCount }}</span>
                                        @endforeach
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif"""

content = content.replace(old_modals, new_modals)

with open('resources/views/hydroponics/greenhouses.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Patched.")
