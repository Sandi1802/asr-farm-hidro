import re

with open('app/Http/Controllers/HydroponicController.php', 'r', encoding='utf-8') as f:
    content = f.read()

# I will replace the block that aggregates Hole harvests.
# Since I used %MONTH_VAR% etc earlier, let's find it.
hole_block = """        $holeHarvested = \App\Models\Hole::whereNotNull('plant_name')
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
        }"""

content = content.replace(hole_block, "")

with open('app/Http/Controllers/HydroponicController.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Hole aggregation removed.")
