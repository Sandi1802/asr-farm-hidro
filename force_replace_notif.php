<?php
$f = 'app/Http/Controllers/HydroponicController.php';
$c = file_get_contents($f);

// getNotifications replacement
$search1 = '/public function getNotifications\(\).*?return response\(\)->json\(\[.*?\]\);/s';
$replace1 = <<<'EOF'
public function getNotifications()
    {
        $defaultDays = 30;

        $readyHoles = \App\Models\Hole::with(['row.rack.greenhouse'])
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("DATE(holes.planted_at + ((COALESCE(plant_types.growth_days, ?) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')) <= CURRENT_DATE", [$defaultDays])
            ->select('holes.*')
            ->get();

        $groups = $readyHoles->map(function ($hole) {
            $ghName = optional(optional(optional($hole->row)->rack)->greenhouse)->name ?? 'GH Unknown';
            $rackName = optional(optional($hole->row)->rack)->name ?? 'Rak Unknown';
            $pName = method_exists($this, 'normalizePlantName') ? $this->normalizePlantName($hole->plant_name) : ($hole->plant_name ?? 'Unknown');
            return [
                'plant' => $pName,
                'gh_name' => $ghName,
                'rack_name' => $rackName,
                'planted_at' => $hole->planted_at,
            ];
        })->groupBy('plant');

        $typeCount = $groups->count();
        $readCount = session('harvest_notif_read_count', 0);
        
        if ($typeCount < $readCount) {
            session(['harvest_notif_read_count' => $typeCount]);
            $readCount = $typeCount;
        }

        $hasNew = $typeCount > 0 && $typeCount > $readCount;
        $displayCount = $typeCount - $readCount;

        $html = view('components.notifications', ['readyGroups' => $groups, 'typeCount' => $typeCount])->render();

        return response()->json([
            'count' => $hasNew ? $displayCount : $typeCount,
            'has_new' => $hasNew,
            'html' => $html
        ]);
EOF;
// Need to add closing brace for the function body!
$replace1 .= "\n    }";

$c = preg_replace($search1, $replace1, $c);

// markNotificationsRead replacement
$search2 = '/public function markNotificationsRead\(\).*?return response\(\)->json\(\[\'success\' => true\]\);/s';
$replace2 = <<<'EOF'
public function markNotificationsRead()
    {
        $defaultDays = 30;

        $readyHoles = \App\Models\Hole::leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("DATE(holes.planted_at + ((COALESCE(plant_types.growth_days, ?) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')) <= CURRENT_DATE", [$defaultDays])
            ->select('holes.plant_name')
            ->get();

        $typeCount = $readyHoles->map(function($h) {
            return method_exists($this, 'normalizePlantName') ? $this->normalizePlantName($h->plant_name) : ($h->plant_name ?? 'Unknown');
        })->unique()->count();

        session(['harvest_notif_read_count' => $typeCount]);

        return response()->json(['success' => true]);
EOF;
$replace2 .= "\n    }";

$c = preg_replace($search2, $replace2, $c);

file_put_contents($f, $c);
echo "Force replace done.\n";
