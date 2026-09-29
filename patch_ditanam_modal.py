import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_return = "return view('hydroponics.greenhouses', compact('greenhouses', 'ghStats', 'ghReadyGrouped', 'ghReadyTotal'));"
new_ditanam = """        $ditanamHolesAgg = \Illuminate\Support\Facades\DB::table('holes')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->where(function($q) {
                $q->whereNull('holes.planted_at')
                  ->orWhereRaw("holes.planted_at > NOW() - (COALESCE(plant_types.growth_days, 30) * INTERVAL '1 day')");
            })
            ->select(
                'racks.greenhouse_id',
                'holes.plant_name',
                'racks.name as rack_name',
                \Illuminate\Support\Facades\DB::raw('COUNT(holes.id) as count')
            )
            ->groupBy('racks.greenhouse_id', 'holes.plant_name', 'racks.name')
            ->get();
            
        $ghDitanamGrouped = [];
        foreach ($greenhouses as $gh) {
            $ghDitanamGrouped[$gh->id] = [];
        }
        foreach ($ditanamHolesAgg as $r) {
            if (isset($ghDitanamGrouped[$r->greenhouse_id])) {
                $pName = $r->plant_name ?: 'Tidak Diketahui';
                if (!isset($ghDitanamGrouped[$r->greenhouse_id][$pName])) {
                    $ghDitanamGrouped[$r->greenhouse_id][$pName] = [
                        'total' => 0,
                        'racks' => []
                    ];
                }
                $ghDitanamGrouped[$r->greenhouse_id][$pName]['total'] += $r->count;
                if (!isset($ghDitanamGrouped[$r->greenhouse_id][$pName]['racks'][$r->rack_name])) {
                    $ghDitanamGrouped[$r->greenhouse_id][$pName]['racks'][$r->rack_name] = 0;
                }
                $ghDitanamGrouped[$r->greenhouse_id][$pName]['racks'][$r->rack_name] += $r->count;
            }
        }
        
        foreach ($ghDitanamGrouped as $ghId => &$plants) {
            foreach ($plants as $pName => &$pData) {
                uksort($pData['racks'], 'strnatcmp');
            }
        }

        return view('hydroponics.greenhouses', compact('greenhouses', 'ghStats', 'ghReadyGrouped', 'ghReadyTotal', 'ghDitanamGrouped'));"""

content = content.replace(old_return, new_ditanam)
with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Controller updated")


# View updates
with open('resources/views/hydroponics/greenhouses.blade.php', 'r', encoding='utf-8') as f:
    view_content = f.read()

old_blade = """                @foreach($greenhouses as $gh)
                @php
                    $stats = $ghStats[$gh->id] ?? ['kosong'=>0,'ditanam'=>0,'panen'=>0,'rusak'=>0,'total'=>1];
                    $cntKosong = $stats['kosong'];
                    $cntDitanamTotal = $stats['ditanam'];
                    $cntPanen = $stats['panen'];
                    $cntRusak = $stats['rusak'];
                    
                    $cntReady = $ghReadyTotal[$gh->id] ?? 0;
                    $cntDitanam = max(0, $cntDitanamTotal - $cntReady);
                    
                    $readyGrouped = $ghReadyGrouped[$gh->id] ?? [];
                    
                    $total = $stats['total'] ?: 1;
                    $pct = round((($cntDitanamTotal) / $total) * 100);
                @endphp"""

new_blade = """                @foreach($greenhouses as $gh)
                @php
                    $stats = $ghStats[$gh->id] ?? ['kosong'=>0,'ditanam'=>0,'panen'=>0,'rusak'=>0,'total'=>1];
                    $cntKosong = $stats['kosong'];
                    $cntDitanamTotal = $stats['ditanam'];
                    $cntPanen = $stats['panen'];
                    $cntRusak = $stats['rusak'];
                    
                    $cntReady = $ghReadyTotal[$gh->id] ?? 0;
                    $cntDitanam = max(0, $cntDitanamTotal - $cntReady);
                    
                    $readyGrouped = $ghReadyGrouped[$gh->id] ?? [];
                    $ditanamGrouped = $ghDitanamGrouped[$gh->id] ?? [];
                    
                    $total = $stats['total'] ?: 1;
                    $pct = round((($cntDitanamTotal) / $total) * 100);
                @endphp"""

view_content = view_content.replace(old_blade, new_blade)

old_ditanam_modal = """                  @if($cntDitanam > 0)
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

new_ditanam_modal = """                  @if($cntDitanam > 0)
                  <div id="ditanamModal_{{ $gh->id }}" class="modal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 9999; backdrop-filter: blur(2px);">
                      <div style="background: white; padding: 1.5rem; border-radius: 12px; width: 100%; max-width: 600px; max-height: 85vh; display:flex; flex-direction:column; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
                          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                              <h3 style="margin:0; font-size: 1.15rem; font-weight: 700; color:var(--text-main);"><i class="ph ph-plant" style="color:#16a34a; margin-right:8px;"></i> Tanaman Sedang Ditanam ({{ $gh->name }})</h3>
                              <button onclick="document.getElementById('ditanamModal_{{ $gh->id }}').style.display='none'" style="border:none; background:transparent; cursor:pointer; font-size:1.2rem; color:var(--text-muted);"><i class="ph ph-x"></i></button>
                          </div>
                          <div style="overflow-y:auto; flex-grow:1; padding-right:0.5rem;">
                              <ul style="list-style:none; padding:0; margin:0;">
                                  @foreach($ditanamGrouped as $plantName => $pData)
                                  <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; background: #f0fdf4;">
                                      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                                          <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                          <div style="background: #dcfce7; color: #16a34a; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                              {{ number_format($pData['total'], 0, ',', '.') }} Lubang
                                          </div>
                                      </div>
                                      <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                          @foreach($pData['racks'] as $rName => $rCount)"""

view_content = view_content.replace(old_ditanam_modal, new_ditanam_modal)
with open('resources/views/hydroponics/greenhouses.blade.php', 'w', encoding='utf-8') as f:
    f.write(view_content)
print("View updated")
