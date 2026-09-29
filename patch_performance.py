import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Refactor getSummaryCardsData
old_readyHoles_summary = """            $readyIds = $readyHoles->pluck('id');
            $readyToHarvestCount = $readyIds->count();
            $readyHolesHist = \\App\\Models\\Hole::with(['row.rack.greenhouse'])->whereIn('id', $readyIds)->get();
            $readyTypesCount = $readyHolesHist->whereNotNull('plant_name')->groupBy('plant_name')->count();
            $siapPanenHtml = $this->buildSiapPanenHtml($readyHolesHist);"""

new_readyHoles_summary = """            $readyToHarvestCount = $readyHoles->count();
            $readyTypesCount = $readyHoles->whereNotNull('plant_name')->groupBy('plant_name')->count();
            $siapPanenHtml = $this->buildSiapPanenHtml($readyHoles);"""
content = content.replace(old_readyHoles_summary, new_readyHoles_summary)

# 2. Refactor buildSiapPanenHtml
old_buildSiapPanenHtml = """    private function buildSiapPanenHtml($readyHoles)
    {
        $groupedHoles = $readyHoles->whereNotNull('plant_name')->groupBy('plant_name');
        $siapPanenHtml = '';
        
        if ($groupedHoles->isEmpty()) {
            $siapPanenHtml = '<div style="text-align:center; padding:2rem; color:var(--text-muted);"><i class="ph ph-leaf" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>Belum ada tanaman yang diproyeksikan panen.</div>';
        } else {
            foreach ($groupedHoles as $plantName => $holes) {
                $totalHoles = $holes->count();
                $locations = [];
                foreach ($holes as $hole) {
                    $gh = optional(optional(optional($hole->row)->rack)->greenhouse)->name ?? 'GH Unknown';
                    $rackModel = optional(optional($hole->row)->rack);
                    $rack = $rackModel->name ?? 'Rak Unknown';
                    $catatan = $rackModel->catatan_lapangan ?? '-';
                    $age = \Carbon\Carbon::parse($hole->planted_at)->diffInDays(now());
                    
                    $locKey = $gh . ' - ' . $rack;
                    if (!isset($locations[$locKey])) {
                        $locations[$locKey] = ['count' => 0, 'ages' => [], 'catatan' => $catatan];
                    }
                    $locations[$locKey]['count']++;
                    $locations[$locKey]['ages'][] = $age;
                }
                
                // Sort locations naturally
                uksort($locations, 'strnatcmp');
                
                $siapPanenHtml .= '<div style="margin-bottom:1.5rem; border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">';
                $siapPanenHtml .= '<div style="background:var(--bg-light); padding:0.75rem 1rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">';
                $siapPanenHtml .= '<strong style="color:var(--text-main); font-size:1.05rem;">'.htmlspecialchars($plantName ?? 'Tanaman Tidak Diketahui').'</strong>';
                $siapPanenHtml .= '<span style="background:rgba(202, 138, 4, 0.15); color:#ca8a04; padding:3px 10px; border-radius:20px; font-weight:700; font-size:0.85rem;">'.$totalHoles.' Lubang</span>';
                $siapPanenHtml .= '</div>';
                $siapPanenHtml .= '<table style="width:100%; border-collapse:collapse; font-size:0.9rem;"><thead>';
                $siapPanenHtml .= '<tr style="background:var(--card-bg, #fff); border-bottom:1px solid var(--border-color); color:var(--text-muted);">';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Lokasi (GH / Rak)</th>';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Jumlah</th>';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Usia Tanaman</th>';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Kondisi di lapangan</th>';
                $siapPanenHtml .= '</tr></thead><tbody>';
                
                foreach ($locations as $loc => $data) {
                    if (empty($data['ages'])) continue;
                    $minAge = min($data['ages']);
                    $maxAge = max($data['ages']);
                    $ageStr = ($minAge == $maxAge) ? $minAge . ' Hari' : $minAge . ' - ' . $maxAge . ' Hari';
                    
                    $siapPanenHtml .= '<tr style="border-bottom:1px solid var(--border-color);">';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; color:var(--text-main);">'.htmlspecialchars($loc).'</td>';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; text-align:center; font-weight:600; color:var(--text-main);">'.$data['count'].'</td>';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; text-align:center; color:var(--text-muted);">'.htmlspecialchars($ageStr).'</td>';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; color:var(--text-main); font-style:italic;">'.htmlspecialchars($data['catatan']).'</td>';
                    $siapPanenHtml .= '</tr>';
                }
                $siapPanenHtml .= '</tbody></table></div>';
            }
        }
        return $siapPanenHtml;
    }"""

new_buildSiapPanenHtml = """    private function buildSiapPanenHtml($readyHoles)
    {
        // Optimasi: Agregasi langsung via SQL (Jauh lebih cepat dari looping model)
        $readyData = \\Illuminate\\Support\\Facades\\DB::table('holes')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->join('greenhouses', 'racks.greenhouse_id', '=', 'greenhouses.id')
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, 30) * INTERVAL '1 day')")
            ->selectRaw("
                holes.plant_name,
                greenhouses.name as gh_name,
                racks.name as rack_name,
                racks.catatan_lapangan,
                MIN(holes.planted_at) as min_planted,
                MAX(holes.planted_at) as max_planted,
                COUNT(holes.id) as hole_count
            ")
            ->groupBy('holes.plant_name', 'greenhouses.name', 'racks.name', 'racks.catatan_lapangan')
            ->get();

        $groupedHoles = $readyData->groupBy('plant_name');
        $siapPanenHtml = '';
        
        if ($groupedHoles->isEmpty()) {
            $siapPanenHtml = '<div style="text-align:center; padding:2rem; color:var(--text-muted);"><i class="ph ph-leaf" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>Belum ada tanaman yang diproyeksikan panen.</div>';
        } else {
            foreach ($groupedHoles as $plantName => $items) {
                if (!$plantName) continue;
                $totalHoles = $items->sum('hole_count');
                
                // Sort locations naturally by GH and Rack
                $locations = $items->mapWithKeys(function($item) {
                    $locKey = $item->gh_name . ' - ' . $item->rack_name;
                    $minAge = \\Carbon\\Carbon::parse($item->max_planted)->diffInDays(now()); // max planted_at = youngest
                    $maxAge = \\Carbon\\Carbon::parse($item->min_planted)->diffInDays(now()); // min planted_at = oldest
                    return [$locKey => [
                        'count' => $item->hole_count,
                        'catatan' => $item->catatan_lapangan ?? '-',
                        'minAge' => $minAge,
                        'maxAge' => $maxAge
                    ]];
                })->toArray();
                
                uksort($locations, 'strnatcmp');
                
                $siapPanenHtml .= '<div style="margin-bottom:1.5rem; border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">';
                $siapPanenHtml .= '<div style="background:var(--bg-light); padding:0.75rem 1rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">';
                $siapPanenHtml .= '<strong style="color:var(--text-main); font-size:1.05rem;">'.htmlspecialchars($plantName ?? 'Tanaman Tidak Diketahui').'</strong>';
                $siapPanenHtml .= '<span style="background:rgba(202, 138, 4, 0.15); color:#ca8a04; padding:3px 10px; border-radius:20px; font-weight:700; font-size:0.85rem;">'.$totalHoles.' Lubang</span>';
                $siapPanenHtml .= '</div>';
                $siapPanenHtml .= '<table style="width:100%; border-collapse:collapse; font-size:0.9rem;"><thead>';
                $siapPanenHtml .= '<tr style="background:var(--card-bg, #fff); border-bottom:1px solid var(--border-color); color:var(--text-muted);">';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Lokasi (GH / Rak)</th>';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Jumlah</th>';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Usia Tanaman</th>';
                $siapPanenHtml .= '<th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Kondisi di lapangan</th>';
                $siapPanenHtml .= '</tr></thead><tbody>';
                
                foreach ($locations as $loc => $data) {
                    $minAge = $data['minAge'];
                    $maxAge = $data['maxAge'];
                    $ageStr = ($minAge == $maxAge) ? $minAge . ' Hari' : $minAge . ' - ' . $maxAge . ' Hari';
                    
                    $siapPanenHtml .= '<tr style="border-bottom:1px solid var(--border-color);">';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; color:var(--text-main);">'.htmlspecialchars($loc).'</td>';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; text-align:center; font-weight:600; color:var(--text-main);">'.$data['count'].'</td>';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; text-align:center; color:var(--text-muted);">'.htmlspecialchars($ageStr).'</td>';
                    $siapPanenHtml .= '<td style="padding:0.75rem 1rem; color:var(--text-main); font-style:italic;">'.htmlspecialchars($data['catatan']).'</td>';
                    $siapPanenHtml .= '</tr>';
                }
                $siapPanenHtml .= '</tbody></table></div>';
            }
        }
        return $siapPanenHtml;
    }"""
content = content.replace(old_buildSiapPanenHtml, new_buildSiapPanenHtml)

# 3. Refactor buildCalendarEvents
old_buildCalendarEvents = """    private function buildCalendarEvents()
    {
        $plantTypes   = PlantType::all()->keyBy('name');
        $defaultDays  = 30;
        $events = collect();

        // 1. Hole Events â€” 4 fase per tanaman
        $holes = Hole::with(['row.rack.greenhouse'])->where('status', 'ditanam')->whereNotNull('planted_at')->get();
        foreach ($holes as $hole) {
            $pt      = $plantTypes->get($hole->plant_name);
            $semai   = $pt ? (int)($pt->semai_days  ?? 0) : 0;
            $tanam   = $pt ? (int)($pt->tanam_days  ?? 0) : 0;
            $remaja  = $pt ? (int)($pt->remaja_days ?? 0) : 0;
            $total   = $pt ? (int)$pt->growth_days : $defaultDays;
            $daysOld = Carbon::parse($hole->planted_at)->diffInDays(now());
            $base    = Carbon::parse($hole->planted_at);
            $ghName = optional(optional(optional($hole->row)->rack)->greenhouse)->name ?? 'GH';
            $rackName = optional(optional($hole->row)->rack)->name ?? 'Rak';
            $locationBase = $ghName . ' â€º ' . $rackName;
            $location = $locationBase . ' â€º ' . $hole->name;
            $harvester = $pt->harvested_by ?? null;

            // Fase 1 â€” Penanaman (hari ke-0, saat dipindah ke lubang)
            $events->push([
                'date'       => $base->format('Y-m-d'),
                'type'       => 'tanam',
                'title'      => 'Penanaman ' . ($hole->plant_name ?? 'Tanaman'),
                'location'   => $location,
                'status'     => 'selesai',
                'description'=> 'Bibit dipindah dari semaian ke rak hidroponik.'
            ]);

            // Fase 2 â€” Perawatan Remaja
            if ($remaja > 0 && $daysOld <= $remaja) {
                $events->push([
                    'date'       => $base->copy()->addDays($remaja)->format('Y-m-d'),
                    'type'       => 'perawatan',
                    'title'      => 'Fase Remaja ' . ($hole->plant_name ?? 'Tanaman'),
                    'location'   => $location,
                    'status'     => 'mendatang',
                    'description'=> 'Pengecekan nutrisi dan penyortiran awal.'
                ]);
            }

            // Fase 3 â€” Siap Panen (Target/Estimasi)
            $harvestDate = $base->copy()->addDays($total);
            if ($daysOld <= $total) {
                $events->push([
                    'date'       => $harvestDate->format('Y-m-d'),
                    'type'       => 'panen',
                    'title'      => 'Target Panen ' . ($hole->plant_name ?? 'Tanaman'),
                    'location'   => $location,
                    'harvester'  => $harvester,
                    'status'     => 'mendatang',
                    'description'=> 'Estimasi usia matang ('.$total.' hari).'
                ]);
            }
        }"""

new_buildCalendarEvents = """    private function buildCalendarEvents()
    {
        $plantTypes   = \\App\\Models\\PlantType::all()->keyBy('name');
        $defaultDays  = 30;
        $events = collect();

        // Optimasi: Agregasi via SQL untuk mengurangi beban memory dan waktu eksekusi
        $aggregatedHoles = \\Illuminate\\Support\\Facades\\DB::table('holes')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->join('greenhouses', 'racks.greenhouse_id', '=', 'greenhouses.id')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->selectRaw("
                holes.plant_name,
                greenhouses.name as gh_name,
                racks.name as rack_name,
                DATE(holes.planted_at) as planted_date,
                COUNT(holes.id) as hole_count
            ")
            ->groupBy('holes.plant_name', 'greenhouses.name', 'racks.name', 'planted_date')
            ->get();

        foreach ($aggregatedHoles as $holeGroup) {
            $pt      = $plantTypes->get($holeGroup->plant_name);
            $semai   = $pt ? (int)($pt->semai_days  ?? 0) : 0;
            $tanam   = $pt ? (int)($pt->tanam_days  ?? 0) : 0;
            $remaja  = $pt ? (int)($pt->remaja_days ?? 0) : 0;
            $total   = $pt ? (int)$pt->growth_days : $defaultDays;
            
            $base    = \\Carbon\\Carbon::parse($holeGroup->planted_date);
            $daysOld = $base->diffInDays(now());
            
            $location = $holeGroup->gh_name . ' › ' . $holeGroup->rack_name . ' (' . $holeGroup->hole_count . ' Lubang)';
            $harvester = $pt->harvested_by ?? null;

            // Fase 1 — Penanaman (hari ke-0, saat dipindah ke lubang)
            $events->push([
                'date'       => $base->format('Y-m-d'),
                'type'       => 'tanam',
                'title'      => 'Penanaman ' . ($holeGroup->plant_name ?? 'Tanaman'),
                'location'   => $location,
                'status'     => 'selesai',
                'description'=> 'Bibit dipindah dari semaian ke rak hidroponik.'
            ]);

            // Fase 2 — Perawatan Remaja
            if ($remaja > 0 && $daysOld <= $remaja) {
                $events->push([
                    'date'       => $base->copy()->addDays($remaja)->format('Y-m-d'),
                    'type'       => 'perawatan',
                    'title'      => 'Fase Remaja ' . ($holeGroup->plant_name ?? 'Tanaman'),
                    'location'   => $location,
                    'status'     => 'mendatang',
                    'description'=> 'Pengecekan nutrisi dan penyortiran awal.'
                ]);
            }

            // Fase 3 — Siap Panen (Target/Estimasi)
            $harvestDate = $base->copy()->addDays($total);
            if ($daysOld <= $total) {
                $events->push([
                    'date'       => $harvestDate->format('Y-m-d'),
                    'type'       => 'panen',
                    'title'      => 'Target Panen ' . ($holeGroup->plant_name ?? 'Tanaman'),
                    'location'   => $location,
                    'harvester'  => $harvester,
                    'status'     => 'mendatang',
                    'description'=> 'Estimasi usia matang ('.$total.' hari).'
                ]);
            }
        }"""
content = content.replace(old_buildCalendarEvents, new_buildCalendarEvents)


with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Performance fixes applied successfully to HydroponicController.")
