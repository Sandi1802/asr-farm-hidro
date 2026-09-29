import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Define the old block that is shared between both methods
old_harvested_block_dashboard = """        $harvestedTotals = [];
        $holeHarvested = \App\Models\Hole::whereNotNull('plant_name')
            ->whereNotNull('harvested_at')
            ->selectRaw('plant_name, count(*) as count')
            ->groupBy('plant_name')
            ->pluck('count', 'plant_name')
            ->toArray();
        foreach ($holeHarvested as $pName => $qty) {
            if ($pName !== 'Tidak Diketahui') {
                $harvestedTotals[$pName] = ($harvestedTotals[$pName] ?? 0) + $qty;
            }
        }
        $allPanenLogs = \App\Models\MaintenanceLog::where('action_type', 'panen')->get();
        foreach ($allPanenLogs as $log) {
            $det = json_decode($log->details);
            $pName = $det->plant_name ?? 'Tidak Diketahui';
            if ($pName !== 'Tidak Diketahui') {
                $qty = $det->jumlah ?? 0;
                $harvestedTotals[$pName] = ($harvestedTotals[$pName] ?? 0) + $qty;
            }
        }
        
        $harvestedByPlant = $harvestedTotals;
        $harvestedHoles = array_sum($harvestedTotals);
        $harvestedTypesCount = count($harvestedTotals);"""

old_harvested_block_summary = """            $harvestedTotals = [];
            $holeHarvested = \App\Models\Hole::whereNotNull('plant_name')
                ->whereNotNull('harvested_at')
                ->selectRaw('plant_name, count(*) as count')
                ->groupBy('plant_name')
                ->pluck('count', 'plant_name')
                ->toArray();
            foreach ($holeHarvested as $pName => $qty) {
                if ($pName !== 'Tidak Diketahui') {
                    $harvestedTotals[$pName] = ($harvestedTotals[$pName] ?? 0) + $qty;
                }
            }
            $allPanenLogs = \App\Models\MaintenanceLog::where('action_type', 'panen')->get();
            foreach ($allPanenLogs as $log) {
                $det = json_decode($log->details);
                $pName = $det->plant_name ?? 'Tidak Diketahui';
                if ($pName !== 'Tidak Diketahui') {
                    $qty = $det->jumlah ?? 0;
                    $harvestedTotals[$pName] = ($harvestedTotals[$pName] ?? 0) + $qty;
                }
            }
            
            $harvestedByPlant = $harvestedTotals;
            $harvestedHoles = array_sum($harvestedTotals);
            $harvestedTypesCount = count($harvestedTotals);"""


new_harvested_block_base = """
        $harvestedTotals = [];
        $t_month = %MONTH_VAR%;
        $t_year = %YEAR_VAR%;
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');

        $holeHarvested = \App\Models\Hole::whereNotNull('plant_name')
            ->whereNotNull('harvested_at')
            ->whereMonth('harvested_at', $t_month)
            ->whereYear('harvested_at', $t_year)
            ->get(['plant_name', 'harvested_at']);

        foreach ($holeHarvested as $hole) {
            $pName = $hole->plant_name;
            if ($pName == 'Tidak Diketahui' || !$pName) continue;
            
            if (!isset($harvestedTotals[$pName])) {
                $harvestedTotals[$pName] = ['today' => 0, 'yesterday' => 0, 'month' => 0];
            }
            
            $harvestedTotals[$pName]['month']++;
            $date = \Carbon\Carbon::parse($hole->harvested_at)->format('Y-m-d');
            if ($date === $today) {
                $harvestedTotals[$pName]['today']++;
            } elseif ($date === $yesterday) {
                $harvestedTotals[$pName]['yesterday']++;
            }
        }

        $allPanenLogs = \App\Models\MaintenanceLog::where('action_type', 'panen')
            ->whereMonth('created_at', $t_month)
            ->whereYear('created_at', $t_year)
            ->get(['details', 'created_at']);

        foreach ($allPanenLogs as $log) {
            $det = json_decode($log->details);
            $pName = $det->plant_name ?? 'Tidak Diketahui';
            if ($pName == 'Tidak Diketahui' || !$pName) continue;

            $qty = (int) ($det->jumlah ?? 0);
            
            if (!isset($harvestedTotals[$pName])) {
                $harvestedTotals[$pName] = ['today' => 0, 'yesterday' => 0, 'month' => 0];
            }

            $harvestedTotals[$pName]['month'] += $qty;
            $date = \Carbon\Carbon::parse($log->created_at)->format('Y-m-d');
            if ($date === $today) {
                $harvestedTotals[$pName]['today'] += $qty;
            } elseif ($date === $yesterday) {
                $harvestedTotals[$pName]['yesterday'] += $qty;
            }
        }
        
        $harvestedByPlant = $harvestedTotals;
        $harvestedHoles = array_sum(array_column($harvestedTotals, 'month'));
        $harvestedTypesCount = count($harvestedTotals);"""

new_dashboard = new_harvested_block_base.replace('%MONTH_VAR%', 'now()->month').replace('%YEAR_VAR%', 'now()->year')

new_summary = new_harvested_block_base.replace('%MONTH_VAR%', '$month').replace('%YEAR_VAR%', '$year')
# Add indentation for summary method (which is inside an `if`)
new_summary = "\n".join(["    " + line for line in new_summary.split("\n")])

content = content.replace(old_harvested_block_dashboard, new_dashboard.strip())
content = content.replace(old_harvested_block_summary, new_summary.strip())

with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("HydroponicController patched.")
