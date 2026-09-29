import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

old_func = """    public function greenhouses()
    {
        $greenhouses = Greenhouse::with(['racks.rows.holes'])->withCount('racks')->get();
        $plantTypeMap = PlantType::pluck('growth_days', 'name');
        $defaultDays = 30;

        return view('hydroponics.greenhouses', compact('greenhouses', 'plantTypeMap', 'defaultDays'));
    }"""

new_func = """    public function greenhouses()
    {
        $greenhouses = Greenhouse::withCount('racks')->get();
        
        // Optimasi: Gunakan agregasi SQL untuk menghitung lubang per GH daripada load 19,000 model Hole ke RAM
        $holeStats = \Illuminate\Support\Facades\DB::table('holes')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->select('racks.greenhouse_id', 'holes.status', \Illuminate\Support\Facades\DB::raw('COUNT(holes.id) as count'))
            ->groupBy('racks.greenhouse_id', 'holes.status')
            ->get();
            
        $ghStats = [];
        foreach ($greenhouses as $gh) {
            $ghStats[$gh->id] = [
                'kosong' => 0,
                'ditanam' => 0,
                'panen' => 0,
                'rusak' => 0,
                'total' => 0
            ];
        }
        
        foreach ($holeStats as $stat) {
            if (isset($ghStats[$stat->greenhouse_id])) {
                $status = $stat->status;
                if (isset($ghStats[$stat->greenhouse_id][$status])) {
                    $ghStats[$stat->greenhouse_id][$status] += $stat->count;
                }
                $ghStats[$stat->greenhouse_id]['total'] += $stat->count;
            }
        }

        // Agregasi Siap Panen per GH, per jenis tanaman, dan per rak
        $readyHolesAgg = \Illuminate\Support\Facades\DB::table('holes')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, 30) * INTERVAL '1 day')")
            ->select(
                'racks.greenhouse_id',
                'holes.plant_name',
                'racks.name as rack_name',
                \Illuminate\Support\Facades\DB::raw('COUNT(holes.id) as count')
            )
            ->groupBy('racks.greenhouse_id', 'holes.plant_name', 'racks.name')
            ->get();
            
        $ghReadyGrouped = [];
        $ghReadyTotal = [];
        foreach ($greenhouses as $gh) {
            $ghReadyGrouped[$gh->id] = [];
            $ghReadyTotal[$gh->id] = 0;
        }
        
        foreach ($readyHolesAgg as $r) {
            if (isset($ghReadyGrouped[$r->greenhouse_id])) {
                $pName = $r->plant_name ?: 'Tidak Diketahui';
                if (!isset($ghReadyGrouped[$r->greenhouse_id][$pName])) {
                    $ghReadyGrouped[$r->greenhouse_id][$pName] = [
                        'total' => 0,
                        'racks' => []
                    ];
                }
                $ghReadyGrouped[$r->greenhouse_id][$pName]['total'] += $r->count;
                if (!isset($ghReadyGrouped[$r->greenhouse_id][$pName]['racks'][$r->rack_name])) {
                    $ghReadyGrouped[$r->greenhouse_id][$pName]['racks'][$r->rack_name] = 0;
                }
                $ghReadyGrouped[$r->greenhouse_id][$pName]['racks'][$r->rack_name] += $r->count;
                $ghReadyTotal[$r->greenhouse_id] += $r->count;
            }
        }
        
        foreach ($ghReadyGrouped as $ghId => &$plants) {
            foreach ($plants as $pName => &$pData) {
                uksort($pData['racks'], 'strnatcmp');
            }
        }

        return view('hydroponics.greenhouses', compact('greenhouses', 'ghStats', 'ghReadyGrouped', 'ghReadyTotal'));
    }"""

if old_func in content:
    content = content.replace(old_func, new_func)
    with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Controller updated")
else:
    print("Failed to find old function in controller")


# Now update the view
with open('resources/views/hydroponics/greenhouses.blade.php', 'r', encoding='utf-8') as f:
    view_content = f.read()

old_blade_logic = """                @foreach($greenhouses as $gh)
                @php
                    $thirtyDaysAgo = now()->subDays(30);
                    $allHoles = $gh->racks->flatMap->rows->flatMap->holes;
                    $cntKosong  = $allHoles->where('status', 'kosong')->count();
                    $cntDitanamTotal = $allHoles->where('status', 'ditanam')->count();
                    $readyHoles   = $allHoles->where('status', 'ditanam')->filter(function($h) use ($plantTypeMap, $defaultDays) {
                        if (!$h->planted_at) return false;
                        $days = isset($plantTypeMap[$h->plant_name]) ? $plantTypeMap[$h->plant_name] : $defaultDays;
                        return \Carbon\Carbon::parse($h->planted_at)->addDays($days)->lte(now());
                    });
                    $cntReady = $readyHoles->count();
                    $cntDitanam = max(0, $cntDitanamTotal - $cntReady);
                    
                    $readyGrouped = $readyHoles->whereNotNull('plant_name')->groupBy('plant_name');
                    $cntPanen   = $allHoles->where('status', 'panen')->count();
                    $cntRusak   = $allHoles->where('status', 'rusak')->count();
                    
                    $total = $allHoles->count() ?: 1;
                    $pct = round((($cntDitanamTotal) / $total) * 100);
                @endphp"""

new_blade_logic = """                @foreach($greenhouses as $gh)
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

if old_blade_logic in view_content:
    view_content = view_content.replace(old_blade_logic, new_blade_logic)
else:
    print("Failed to find old_blade_logic")

old_holes_count = "{{ number_format($allHoles->count(), 0, ',', '.') }}"
new_holes_count = "{{ number_format($total, 0, ',', '.') }}"
view_content = view_content.replace(old_holes_count, new_holes_count)


old_modal_loop = """                                @foreach($readyGrouped as $plantName => $holes)
                                @php
                                    $rackGrouped = [];
                                    foreach($holes as $h) {
                                        $r = optional(optional($h->row)->rack)->name ?? 'Unknown Rak';
                                        if(!isset($rackGrouped[$r])) $rackGrouped[$r] = 0;
                                        $rackGrouped[$r]++;
                                    }
                                    uksort($rackGrouped, 'strnatcmp');
                                @endphp
                                <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; background: #fdfaf5;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                        <div style="background: #ffedd5; color: #ea580c; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                            {{ number_format($holes->count(), 0, ',', '.') }} Lubang
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                        @foreach($rackGrouped as $rName => $rCount)"""

new_modal_loop = """                                @foreach($readyGrouped as $plantName => $pData)
                                <li style="padding: 1rem; border: 1px solid var(--border-color); border-radius: 8px; margin-bottom: 0.75rem; background: #fdfaf5;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 0.5rem;">
                                        <div style="font-weight: 700; font-size: 1.05rem; color: var(--text-main);">{{ $plantName }}</div>
                                        <div style="background: #ffedd5; color: #ea580c; padding: 0.35rem 0.8rem; border-radius: 20px; font-size: 0.9rem; font-weight: 700;">
                                            {{ number_format($pData['total'], 0, ',', '.') }} Lubang
                                        </div>
                                    </div>
                                    <div style="font-size: 0.85rem; color: var(--text-muted); display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                        @foreach($pData['racks'] as $rName => $rCount)"""

if old_modal_loop in view_content:
    view_content = view_content.replace(old_modal_loop, new_modal_loop)
else:
    print("Failed to find old_modal_loop")

with open('resources/views/hydroponics/greenhouses.blade.php', 'w', encoding='utf-8') as f:
    f.write(view_content)
print("View updated")

