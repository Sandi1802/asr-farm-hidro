<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Greenhouse;
use App\Models\Rack;
use App\Models\Row;
use App\Models\Hole;
use App\Models\Activity;
use App\Models\Inventory;
use App\Models\PlantType;
use App\Models\CalendarEvent;
use App\Models\Semai;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HydroponicController extends Controller
{
    public function dashboard()
    {
        $totalGH        = Greenhouse::count();
        $totalRacks     = Rack::count();
        $totalHoles     = Hole::count();
        $plantedHoles   = Hole::where('status', 'ditanam')->count();
        $plantedTypesCount = Hole::where('status', 'ditanam')->distinct('plant_name')->count('plant_name');
        
$harvestedTotals = [];
        $t_month = now()->month;
        $t_year = now()->year;
        $today = now()->format('Y-m-d');
        $yesterday = now()->subDay()->format('Y-m-d');



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
        $harvestedTypesCount = count($harvestedTotals);

        $allRusakLogs = \App\Models\MaintenanceLog::whereMonth('created_at', now()->month)
            ->where('action_type', 'rusak')
            ->get();

        $damagedTotals = [];
        foreach ($allRusakLogs as $log) {
            $det = json_decode($log->details);
            $pName = $det->plant_name ?? 'Tidak Diketahui';
            $alasan = $det->alasan ?? 'Lainnya';
            $catatan = $det->catatan ?? null;
            $qty = $det->jumlah ?? 0;
            
            $key = htmlspecialchars($pName) . ' (' . htmlspecialchars($alasan) . ')';
            if ($catatan) {
                $key .= '<br><span style="font-size:0.85rem; color:var(--text-muted); font-weight:normal; margin-top:4px; display:inline-block;">Catatan: ' . htmlspecialchars($catatan) . '</span>';
            }
            $damagedTotals[$key] = ($damagedTotals[$key] ?? 0) + $qty;
        }
        $damagedByReason = $damagedTotals;
        $damagedHoles = array_sum($damagedTotals);
        $damagedTypesCount = count($damagedTotals);

        // Build a map of plant_name -> growth_days from plant_types
        $plantTypeMap = PlantType::pluck('growth_days', 'name');  // ['Pakcoy' => 20, ...]
        $defaultDays  = 30;

        // Siap Panen — per-plant dynamic threshold
        $readyIds = \App\Models\Hole::leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day')", [$defaultDays])
            ->pluck('holes.id');

        $readyToHarvestCount = $readyIds->count();

        // Detail siap panen grouped by plant name with location
        $readyToHarvestItems = Hole::with(['row.rack.greenhouse'])
            ->whereIn('id', $readyIds)
            ->get()
            ->groupBy('plant_name');
            
        $readyTypesCount = $readyToHarvestItems->count();

        // Inventory stats per category
        $inventoryByCategory = Inventory::select('type', DB::raw('count(*) as total_items'), DB::raw('sum(quantity) as total_qty'))
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        $inventoryItems   = Inventory::all();
        $recentActivities = Activity::with(['user', 'hole.row.rack.greenhouse'])
            ->latest()->take(5)->get();

        $emptyHolesCount     = Hole::where('status', 'kosong')->count();
        
        $emptyHolesRaw = Hole::with(['row.rack.greenhouse'])->where('status', 'kosong')->get();
        $emptyHolesGrouped = [];
        foreach ($emptyHolesRaw as $h) {
            $gh = optional(optional(optional($h->row)->rack)->greenhouse)->name ?? 'GH Unknown';
            $rack = optional(optional($h->row)->rack)->name ?? 'Rak Unknown';
            $locKey = $gh . ' - ' . $rack;
            if (!isset($emptyHolesGrouped[$locKey])) $emptyHolesGrouped[$locKey] = 0;
            $emptyHolesGrouped[$locKey]++;
        }
        arsort($emptyHolesGrouped);

        $occupancyRate       = $totalHoles > 0 ? round(($plantedHoles / $totalHoles) * 100, 1) : 0;
        $totalInventoryItems = Inventory::count();

        // Build calendar events
        $calendarEvents = $this->buildCalendarEvents();

        // CHART DATA
        // ─── NEW: PRODUKSI BULAN INI (This Month Summary) ───
        $currentMonth = now()->month;
        $currentYear  = now()->year;

        // Semai (Bulan ini)
        $semaiThisMonth = Semai::whereMonth('semai_date', $currentMonth)
            ->whereYear('semai_date', $currentYear)->get();
        $totalJenisSemaiBulanIni = $semaiThisMonth->unique('plant_name')->count();
        $totalBenihSemaiBulanIni = $semaiThisMonth->sum('quantity');

        // Tanam / Pindah ke GH (Bulan ini) -> dari MaintenanceLog
        $tanamBulanIni = \App\Models\MaintenanceLog::where('action_type', 'pindah_tanam')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get()->sum(function($log) {
                return json_decode($log->details)->jumlah ?? 0;
            });

        // Panen (Bulan ini) -> dari MaintenanceLog
        $panenBulanIni = \App\Models\MaintenanceLog::where('action_type', 'panen')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get()->sum(function($log) {
                return json_decode($log->details)->jumlah ?? 0;
            });

        $produksiBulanIni = [
            'jenis_semai' => $totalJenisSemaiBulanIni,
            'total_semai' => $totalBenihSemaiBulanIni,
            'total_tanam' => $tanamBulanIni,
            'total_panen' => $panenBulanIni
        ];

        // ─── NEW: TREN PRODUKSI MINGGUAN (4 Minggu Terakhir) ───
        $weeklyTrendLabels = [];
        $weeklySemai = [];
        $weeklyTanam = [];
        $weeklyPanen = [];
        $weeklyRusak = [];

        for ($i = 3; $i >= 0; $i--) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end   = now()->subWeeks($i)->endOfWeek();
            $weeklyTrendLabels[] = "Mg " . $start->format('d/m');

            $weeklySemai[] = \App\Models\Semai::whereBetween('semai_date', [$start, $end])->sum('quantity');
            $weeklyTanam[] = \App\Models\Hole::whereBetween('planted_at', [$start, $end])->count();
            $weeklyPanen[] = \App\Models\Hole::whereBetween('harvested_at', [$start, $end])->count();
            $weeklyRusak[] = \App\Models\Hole::where('status', 'rusak')->whereBetween('updated_at', [$start, $end])->count();
        }

        $weeklyTrendData = [
            'labels' => $weeklyTrendLabels,
            'semai'  => $weeklySemai,
            'tanam'  => $weeklyTanam,
            'panen'  => $weeklyPanen,
            'rusak'  => $weeklyRusak
        ];

        // ─── CHART: Tanaman Paling Sering Ditanam ───
        $topPlantedQuery = \App\Models\Hole::whereNotNull('plant_name')
            ->whereNotNull('planted_at')
            ->selectRaw('plant_name, count(*) as count')
            ->groupBy('plant_name')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'plant_name')
            ->toArray();
        $mostPlantedLabels = array_keys($topPlantedQuery);
        $mostPlantedValues = array_values($topPlantedQuery);

        // ─── CHART: Tanaman Paling Sering Dipanen ───
        arsort($harvestedTotals);
        $topHarvestedQuery = array_slice($harvestedTotals, 0, 8, true);
        $mostHarvestedLabels = array_keys($topHarvestedQuery);
        $mostHarvestedValues = array_values($topHarvestedQuery);

        // ─── CHART: Tingkat Occupancy Tiap Greenhouse (Perputaran) ───
        $greenhousesList = Greenhouse::with('racks')->get();
        
        $holesStats = \App\Models\Hole::select('racks.greenhouse_id')
            ->selectRaw('COUNT(holes.id) as total')
            ->selectRaw("SUM(CASE WHEN holes.status = 'ditanam' THEN 1 ELSE 0 END) as planted")
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->groupBy('racks.greenhouse_id')
            ->get()
            ->keyBy('greenhouse_id');
            
        $readyStats = \App\Models\Hole::select('racks.greenhouse_id')
            ->selectRaw('COUNT(holes.id) as ready')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->whereIn('holes.id', $readyIds->isEmpty() ? [0] : $readyIds)
            ->groupBy('racks.greenhouse_id')
            ->get()
            ->keyBy('greenhouse_id');

        $harvestedStats = \App\Models\MaintenanceLog::select('racks.greenhouse_id')
            ->selectRaw("SUM(CAST(details->>'jumlah' AS INTEGER)) as harvested")
            ->join('racks', 'maintenance_logs.loggable_id', '=', 'racks.id')
            ->where('maintenance_logs.loggable_type', 'App\Models\Rack')
            ->whereMonth('maintenance_logs.created_at', now()->month)
            ->where('maintenance_logs.action_type', 'panen')
            ->groupBy('racks.greenhouse_id')
            ->get()
            ->keyBy('greenhouse_id');

        $rotationData = [];
        foreach ($greenhousesList as $gh) {
            $ghTotal = $holesStats->has($gh->id) ? $holesStats[$gh->id]->total : 0;
            $ghPlanted = $holesStats->has($gh->id) ? $holesStats[$gh->id]->planted : 0;
            $ghReady = $readyStats->has($gh->id) ? $readyStats[$gh->id]->ready : 0;
            $ghHarvested = $harvestedStats->has($gh->id) ? $harvestedStats[$gh->id]->harvested : 0;
            
            $rotationData[] = [
                'name'      => $gh->name,
                'total'     => (int) $ghTotal,
                'planted'   => (int) $ghPlanted,
                'harvested' => (int) $ghHarvested,
                'ready'     => (int) $ghReady,
                'rate'      => $ghTotal > 0 ? round(($ghPlanted / $ghTotal) * 100, 1) : 0,
            ];
        }

        $plantsPerGh = \App\Models\Hole::select('racks.greenhouse_id', 'holes.plant_name')
            ->join('rows', 'holes.row_id', '=', 'rows.id')
            ->join('racks', 'rows.rack_id', '=', 'racks.id')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.plant_name')
            ->distinct()
            ->get()
            ->groupBy('greenhouse_id')
            ->map(function($items) {
                return $items->pluck('plant_name')->toArray();
            });

        // Data for GH Distribution Chart
        $ghDistribution = $greenhousesList->map(function($gh) use ($holesStats, $plantsPerGh) {
            return [
                'name' => $gh->name,
                'racks' => $gh->racks->count(),
                'planted_count' => $holesStats->has($gh->id) ? (int)$holesStats[$gh->id]->planted : 0,
                'plants' => $plantsPerGh->has($gh->id) ? $plantsPerGh[$gh->id] : []
            ];
        });

        return view('hydroponics.dashboard', compact(
            'totalGH', 'totalRacks', 'totalHoles', 'plantedHoles',
            'harvestedHoles', 'damagedHoles', 'inventoryByCategory',
            'inventoryItems', 'recentActivities',
            'readyToHarvestCount', 'readyToHarvestItems',
            'emptyHolesCount', 'emptyHolesGrouped', 'occupancyRate', 'totalInventoryItems',
            'calendarEvents', 'mostPlantedLabels', 'mostPlantedValues',
            'mostHarvestedLabels', 'mostHarvestedValues',
            'rotationData', 'produksiBulanIni', 'weeklyTrendData',
            'plantedTypesCount', 'harvestedTypesCount', 'damagedTypesCount', 'readyTypesCount',
            'ghDistribution', 'harvestedByPlant', 'damagedByReason'
        ));
    }

    public function getProduksiStats(\Illuminate\Http\Request $request)
    {
        $month = $request->query('month', now()->month);
        $year  = $request->query('year', now()->year);

        // Semai (Bulan terpilih)
        $semaiThisMonth = \App\Models\Semai::whereMonth('semai_date', $month)
            ->whereYear('semai_date', $year)->get();
        $totalJenisSemai = $semaiThisMonth->unique('plant_name')->count();
        $totalBenihSemai = $semaiThisMonth->sum('quantity');

        // Tanam / Pindah ke GH (Bulan terpilih)
        $tanamBulanIni = \App\Models\Hole::whereMonth('planted_at', $month)
            ->whereYear('planted_at', $year)
            ->count();

        // Panen (Bulan terpilih)
        $panenBulanIni = \App\Models\Hole::whereMonth('harvested_at', $month)
            ->whereYear('harvested_at', $year)
            ->count();

        return response()->json([
            'jenis_semai' => $totalJenisSemai,
            'total_semai' => $totalBenihSemai,
            'total_tanam' => $tanamBulanIni,
            'total_panen' => $panenBulanIni,
            'month_name' => \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y')
        ]);
    }

    public function getDashboardPeriodStats(\Illuminate\Http\Request $request)
    {
        $period = $request->query('period', 'month');
        $now = \Carbon\Carbon::now();

        switch ($period) {
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $periodLabel = 'Tahun ' . $now->year;
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $periodLabel = 'Minggu Ini';
                break;
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $periodLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
            default: // month
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $periodLabel = $now->translatedFormat('F Y');
                break;
        }

        // Semai in period
        $semaiInPeriod = \App\Models\Semai::whereBetween('semai_date', [$start, $end])->get();
        $totalJenisSemai = $semaiInPeriod->unique('plant_name')->count();
        $totalBenihSemai = $semaiInPeriod->sum('quantity');

        // Tanam in period
        $totalTanam = \App\Models\Hole::whereBetween('planted_at', [$start, $end])->count();

        // Panen in period
        $panenFromHoles = \App\Models\Hole::where('status', 'panen')->whereBetween('harvested_at', [$start, $end])->count();
        $panenFromLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])
            ->where('action_type', 'panen')
            ->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
        $totalPanen = $panenFromHoles + $panenFromLogs;

        // Rusak in period
        $rusakFromHoles = \App\Models\Hole::where('status', 'rusak')->whereBetween('updated_at', [$start, $end])->count();
        $rusakFromLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])
            ->where('action_type', 'rusak')
            ->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
        $totalRusak = $rusakFromHoles + $rusakFromLogs;

        return response()->json([
            'period_label' => $periodLabel,
            'total_semai_benih' => $totalBenihSemai,
            'total_semai_jenis' => $totalJenisSemai,
            'total_tanam' => $totalTanam,
            'total_panen' => $totalPanen,
            'total_rusak' => $totalRusak,
        ]);
    }

    /**
     * API for Trend Chart (Weekly, Monthly, Yearly)
     */
    public function getTrendChartData(\Illuminate\Http\Request $request)
    {
        $period = $request->query('period', 'mingguan');
        $labels = [];
        $semai = [];
        $tanam = [];
        $panen = [];
        $rusak = [];

        if ($period === 'tahunan') {
            for ($i = 4; $i >= 0; $i--) {
                $year = now()->subYears($i)->year;
                $labels[] = (string)$year;
                $semai[] = \App\Models\Semai::whereYear('semai_date', $year)->sum('quantity');
                $tanam[] = \App\Models\Hole::whereYear('planted_at', $year)->count();
                $panen[] = \App\Models\MaintenanceLog::whereYear('created_at', $year)->where('action_type', 'panen')->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
                $rusak[] = \App\Models\MaintenanceLog::whereYear('created_at', $year)->where('action_type', 'rusak')->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
            }
        } elseif ($period === 'bulanan') {
            for ($i = 5; $i >= 0; $i--) {
                $start = now()->subMonths($i)->startOfMonth();
                $end = now()->subMonths($i)->endOfMonth();
                $labels[] = $start->translatedFormat('M Y');
                $semai[] = \App\Models\Semai::whereBetween('semai_date', [$start, $end])->sum('quantity');
                $tanam[] = \App\Models\Hole::whereBetween('planted_at', [$start, $end])->count();
                $panen[] = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'panen')->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
                $rusak[] = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'rusak')->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
            }
        } else {
            for ($i = 5; $i >= 0; $i--) {
                $start = now()->subWeeks($i)->startOfWeek();
                $end = now()->subWeeks($i)->endOfWeek();
                $labels[] = "Mg " . $start->format('d/m');
                $semai[] = \App\Models\Semai::whereBetween('semai_date', [$start, $end])->sum('quantity');
                $tanam[] = \App\Models\Hole::whereBetween('planted_at', [$start, $end])->count();
                $panen[] = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'panen')->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
                $rusak[] = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'rusak')->sum(\Illuminate\Support\Facades\DB::raw("CAST(details->>'jumlah' AS INTEGER)"));
            }
        }

        return response()->json([
            'labels' => $labels,
            'semai' => $semai,
            'tanam' => $tanam,
            'panen' => $panen,
            'rusak' => $rusak
        ]);
    }

    /**
     * API for Summary Cards (Real-time & Historical)
     */
    public function getSummaryCardsData(\Illuminate\Http\Request $request)
    {
        $month = $request->query('month', now()->month);
        $year = $request->query('year', now()->year);

        $isCurrentMonth = ($month == now()->month && $year == now()->year);
        $totalHoles = \App\Models\Hole::count();
        
        if ($isCurrentMonth) {
            $emptyHolesCount = \App\Models\Hole::where('status', 'kosong')->count();
            
            $plantedHoles = \App\Models\Hole::where('status', 'ditanam')->count();
            $plantedTypesCount = \App\Models\Hole::where('status', 'ditanam')->whereNotNull('plant_name')->distinct('plant_name')->count('plant_name');

            $logs = \App\Models\MaintenanceLog::whereMonth('created_at', now()->month)->get();
            
            // Panen details grouped by plant_name
$harvestedTotals = [];
            $t_month = $month;
            $t_year = $year;
            $today = now()->format('Y-m-d');
            $yesterday = now()->subDay()->format('Y-m-d');
    

    
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
            $harvestedTypesCount = count($harvestedTotals);

            // Rusak details grouped by alasan
            $allRusakLogs = \App\Models\MaintenanceLog::whereMonth('created_at', now()->month)
            ->where('action_type', 'rusak')
            ->get();

        $damagedTotals = [];
        foreach ($allRusakLogs as $log) {
            $det = json_decode($log->details);
            $pName = $det->plant_name ?? 'Tidak Diketahui';
            $alasan = $det->alasan ?? 'Lainnya';
            $catatan = $det->catatan ?? null;
            $qty = $det->jumlah ?? 0;
            
            $key = htmlspecialchars($pName) . ' (' . htmlspecialchars($alasan) . ')';
            if ($catatan) {
                $key .= '<br><span style="font-size:0.85rem; color:var(--text-muted); font-weight:normal; margin-top:4px; display:inline-block;">Catatan: ' . htmlspecialchars($catatan) . '</span>';
            }
            $damagedTotals[$key] = ($damagedTotals[$key] ?? 0) + $qty;
        }
        $damagedByReason = $damagedTotals;
        $damagedHoles = array_sum($damagedTotals);
        $damagedTypesCount = count($damagedTotals);
            
            // Siap panen logic (Proyeksi bulan ini)
            $defaultDays = 30;

            $readyHoles = \App\Models\Hole::with(['row.rack.greenhouse'])
                ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
                ->where('holes.status', 'ditanam')
                ->whereNotNull('holes.planted_at')
                ->whereRaw("EXTRACT(YEAR FROM (holes.planted_at + (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day'))) * 100 + EXTRACT(MONTH FROM (holes.planted_at + (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day'))) <= ?", [$defaultDays, $defaultDays, $year * 100 + $month])
                ->select('holes.*')
                ->get();
            
            $plantTypes = \App\Models\PlantType::all()->keyBy('name');
            
            $readyIds = $readyHoles->pluck('id');
            $readyToHarvestCount = $readyIds->count();
            
            $groupedHoles = $readyHoles->groupBy('plant_name');
            $readyTypesCount = $groupedHoles->count();
            $siapPanenHtml = $this->buildSiapPanenHtml($readyHoles);
            
            $emptyHolesRaw = \App\Models\Hole::with(['row.rack.greenhouse'])->where('status', 'kosong')->get();
            $emptyHolesGrouped = [];
            foreach ($emptyHolesRaw as $h) {
                $gh = optional(optional(optional($h->row)->rack)->greenhouse)->name ?? 'GH Unknown';
                $rackModel = optional(optional($h->row)->rack);
                $rack = $rackModel->name ?? 'Rak Unknown';
                $locKey = $gh . ' - ' . $rack;
                $catatan = $rackModel->catatan_lapangan ?? '-';
                if (!isset($emptyHolesGrouped[$locKey])) {
                    $emptyHolesGrouped[$locKey] = ['qty' => 0, 'catatan' => $catatan];
                }
                $emptyHolesGrouped[$locKey]['qty']++;
            }
            
            // Sort by GH and Rack name naturally
            uksort($emptyHolesGrouped, 'strnatcmp');
            
            $lubangKosongHtml = '<ul id="lubangKosongModalList" style="list-style: none; padding: 0; margin: 0;">';
            if (empty($emptyHolesGrouped)) {
                $lubangKosongHtml .= '<div style="text-align:center; padding:2rem; color:var(--text-muted);">Tidak ada lubang kosong.</div>';
            } else {
                foreach ($emptyHolesGrouped as $loc => $data) {
                    $qty = $data['qty'];
                    $catatan = $data['catatan'];
                    $lubangKosongHtml .= '<li style="padding: 1rem; border-bottom: 1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">' . 
                        '<div>' . 
                            '<div style="font-weight:600; color:var(--text-main);">' . htmlspecialchars($loc) . '</div>' . 
                            '<div style="font-size:0.85rem; color:var(--text-muted); margin-top:4px;">Catatan: ' . htmlspecialchars($catatan) . '</div>' . 
                        '</div>' . 
                        '<div style="background:var(--bg-hover); padding: 0.3rem 0.8rem; border-radius: 20px; font-weight:700; font-size:0.9rem; color:#0f766e;">' . 
                        number_format($qty,0,',','.') . ' Lubang</div>' . 
                    '</li>';
                }
            }
            $lubangKosongHtml .= '</ul>';

            $panenBulanIni = \App\Models\MaintenanceLog::where('action_type', 'panen')
                ->whereMonth('created_at', $month)->whereYear('created_at', $year)
                ->get()->sum(function($log) { return json_decode($log->details)->jumlah ?? 0; });
            $tanamBulanIni = \App\Models\MaintenanceLog::where('action_type', 'pindah_tanam')
                ->whereMonth('created_at', $month)->whereYear('created_at', $year)
                ->get()->sum(function($log) { return json_decode($log->details)->jumlah ?? 0; });
            $semaiBulanIni = \App\Models\Semai::whereMonth('semai_date', $month)->whereYear('semai_date', $year)->sum('quantity');

            return response()->json([
                'lubang_kosong' => number_format($emptyHolesCount,0,',','.'),
                'lubang_terisi' => number_format($plantedHoles,0,',','.'),
                'lubang_terisi_sub' => $plantedTypesCount.' Jenis Tanaman',
                'siap_panen' => number_format($readyToHarvestCount,0,',','.'),
                'siap_panen_sub' => $readyTypesCount.' Jenis Tanaman',
                'siap_panen_html' => $siapPanenHtml,
                'lubang_kosong_html' => $lubangKosongHtml,
                'sudah_panen' => number_format($harvestedHoles,0,',','.'),
                'sudah_panen_sub' => $harvestedTypesCount.' Jenis Tanaman',
                'sudah_panen_detail' => $harvestedByPlant,
                'gagal_panen' => number_format($damagedHoles,0,',','.'),
                'gagal_panen_sub' => $damagedTypesCount.' Jenis Rusak',
                'gagal_panen_detail' => $damagedByReason,
                'total_tanam_bulan_ini' => number_format($tanamBulanIni,0,',','.'),
                'total_panen_bulan_ini' => number_format($panenBulanIni,0,',','.'),
                'total_semai_bulan_ini' => number_format($semaiBulanIni,0,',','.'),
            ]);
        } else {
            // Historical from MaintenanceLog instead of Activity (since Activity is unused)
            
            // Historical harvested details
            $harvestedTotalsHist = [];
            $allPanenLogsHist = \App\Models\MaintenanceLog::where('action_type', 'panen')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
            foreach ($allPanenLogsHist as $log) {
                $det = json_decode($log->details);
                $pName = $det->plant_name ?? 'Tidak Diketahui';
                if ($pName !== 'Tidak Diketahui') {
                    $qty = $det->jumlah ?? 0;
                    $harvestedTotalsHist[$pName] = ($harvestedTotalsHist[$pName] ?? 0) + $qty;
                }
            }
            $harvestedByPlantHist = $harvestedTotalsHist;
            $harvestedHoles = array_sum($harvestedTotalsHist);

            // Historical damaged details
            $allRusakLogsHist = \App\Models\MaintenanceLog::where('action_type', 'rusak')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
            $damagedTotalsHist = [];
            foreach ($allRusakLogsHist as $log) {
                $det = json_decode($log->details);
                $pName = $det->plant_name ?? 'Tidak Diketahui';
                $alasan = $det->alasan ?? 'Lainnya';
                $catatan = $det->catatan ?? null;
                $qty = $det->jumlah ?? 0;
                
                $key = htmlspecialchars($pName) . ' (' . htmlspecialchars($alasan) . ')';
                if ($catatan) {
                    $key .= '<br><span style="font-size:0.85rem; color:var(--text-muted); font-weight:normal; margin-top:4px; display:inline-block;">Catatan: ' . htmlspecialchars($catatan) . '</span>';
                }
                $damagedTotalsHist[$key] = ($damagedTotalsHist[$key] ?? 0) + $qty;
            }
            $damagedByReasonHist = $damagedTotalsHist;
            $damagedHoles = array_sum($damagedTotalsHist);

            // Historical planted details
            $allTanamLogsHist = \App\Models\MaintenanceLog::where('action_type', 'pindah_tanam')->whereMonth('created_at', $month)->whereYear('created_at', $year)->get();
            $plantedHoles = 0;
            foreach ($allTanamLogsHist as $log) {
                $det = json_decode($log->details);
                $qty = $det->jumlah ?? 0;
                $plantedHoles += $qty;
            }

            // For empty holes, since we don't have historical snapshot, we just show the current empty holes
            // so it doesn't look confusing on the dashboard where other current stats are displayed.
            $emptyHolesCount = \App\Models\Hole::where('status', 'kosong')->count();
            
            // Siap panen logic (Projected from current planted holes)
            $defaultDays = 30;
            $readyIds = \App\Models\Hole::leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
                ->where('holes.status', 'ditanam')
                ->whereNotNull('holes.planted_at')
                ->whereRaw("EXTRACT(YEAR FROM (holes.planted_at + (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day'))) * 100 + EXTRACT(MONTH FROM (holes.planted_at + (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day'))) <= ?", [$defaultDays, $defaultDays, $year * 100 + $month])
                ->pluck('holes.id');
            
            $readyToHarvestCount = $readyIds->count();
            $readyHolesHist = \App\Models\Hole::with(['row.rack.greenhouse'])->whereIn('id', $readyIds)->get();
            $readyTypesCount = $readyHolesHist->whereNotNull('plant_name')->groupBy('plant_name')->count();
            $siapPanenHtml = $this->buildSiapPanenHtml($readyHolesHist);

            $panenBulanIniHist = $allPanenLogsHist->sum(function($log) { return json_decode($log->details)->jumlah ?? 0; });
            $tanamBulanIniHist = $plantedHoles;
            $semaiBulanIniHist = \App\Models\Semai::whereMonth('semai_date', $month)->whereYear('semai_date', $year)->sum('quantity');

            return response()->json([
                'lubang_kosong' => number_format($emptyHolesCount,0,',','.'),
                'lubang_terisi' => number_format($plantedHoles,0,',','.'),
                'lubang_terisi_sub' => 'Total Penanaman',
                'siap_panen' => number_format($readyToHarvestCount,0,',','.'),
                'siap_panen_sub' => $readyTypesCount.' Jenis (Proyeksi)',
                'siap_panen_html' => $siapPanenHtml,
                'sudah_panen' => number_format($harvestedHoles,0,',','.'),
                'sudah_panen_sub' => 'Total Panen',
                'sudah_panen_detail' => $harvestedByPlantHist,
                'gagal_panen' => number_format($damagedHoles,0,',','.'),
                'gagal_panen_sub' => 'Total Kerusakan',
                'gagal_panen_detail' => $damagedByReasonHist,
                'total_tanam_bulan_ini' => number_format($tanamBulanIniHist,0,',','.'),
                'total_panen_bulan_ini' => number_format($panenBulanIniHist,0,',','.'),
                'total_semai_bulan_ini' => number_format($semaiBulanIniHist,0,',','.'),
            ]);
        }
    }

    /**
     * API for harvest calendar (AJAX)
     */
    public function calendarData()
    {
        return response()->json($this->buildCalendarEvents());
    }

    private function buildCalendarEvents()
    {
        $plantTypes   = PlantType::all()->keyBy('name');
        $defaultDays  = 30;
        $events = collect();

        // 1. Hole Events — 4 fase per tanaman
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
            $locationBase = $ghName . ' › ' . $rackName;
            $location = $locationBase . ' › ' . $hole->name;
            $harvester = $pt->harvested_by ?? null;

            // Fase 1 — Penanaman (hari ke-0, saat dipindah ke lubang)
            $events->push([
                'date'       => $base->format('Y-m-d'),
                'type'       => 'tanam',
                'plant_name' => $hole->plant_name ?? 'Tanaman',
                'location'   => $location,
                'location_base' => $locationBase,
                'gh_name'    => $ghName,
                'rack_name'  => $rackName,
                'time'       => $base->format('H:i'),
                'stage_day'  => 0,
                'days_old'   => $daysOld,
            ]);

            // Fase 2 — Remaja
            if ($tanam > 0) {
                $events->push([
                    'date'       => $base->copy()->addDays($tanam)->format('Y-m-d'),
                    'type'       => 'remaja',
                    'plant_name' => $hole->plant_name ?? 'Tanaman',
                    'location'   => $location,
                    'location_base' => $locationBase,
                    'gh_name'    => $ghName,
                    'rack_name'  => $rackName,
                    'stage_day'  => $tanam,
                    'days_old'   => $daysOld,
                ]);
            }

            // Fase 3 — Dewasa/Panen (total - semai)
            $harvestDate    = $base->copy()->addDays($total - $semai);
            $harvestDateStr = $harvestDate->format('Y-m-d');
            $events->push([
                'date'         => $harvestDateStr,
                'type'         => 'harvest',
                'plant_name'   => $hole->plant_name ?? 'Tanaman',
                'location'     => $location,
                'location_base'=> $locationBase,
                'gh_name'      => $ghName,
                'rack_name'    => $rackName,
                'is_ready'     => $harvestDate->lte(now()),
                'days_old'     => $daysOld,
                'growth_days'  => $total,
                'harvested_by' => $harvester,
            ]);
        }

        // Group hole events by GH to prevent duplicates but keep them separated per GH
        $groupedEvents = collect();
        foreach ($events as $ev) {
            $gh = $ev['gh_name'] ?? 'GH';
            $key = $ev['date'] . '_' . $ev['type'] . '_' . $gh;
            
            if (!$groupedEvents->has($key)) {
                $ev['hole_count'] = 1;
                $ev['plant_data'] = [];
                if (isset($ev['plant_name']) && isset($ev['rack_name'])) {
                    $ev['plant_data'][$ev['plant_name']] = [$ev['rack_name'] => true];
                }
                $groupedEvents->put($key, $ev);
            } else {
                $existing = $groupedEvents->get($key);
                $existing['hole_count']++;
                if (isset($ev['plant_name']) && isset($ev['rack_name'])) {
                    if (!isset($existing['plant_data'][$ev['plant_name']])) {
                        $existing['plant_data'][$ev['plant_name']] = [];
                    }
                    $existing['plant_data'][$ev['plant_name']][$ev['rack_name']] = true;
                }
                $groupedEvents->put($key, $existing);
            }
        }
        $events = collect($groupedEvents->values())->map(function($ev) {
            if (isset($ev['plant_data']) && count($ev['plant_data']) > 0) {
                $plantsList = [];
                $plants = array_keys($ev['plant_data']);
                natsort($plants);
                foreach ($plants as $plant) {
                    $rackKeys = array_keys($ev['plant_data'][$plant]);
                    natsort($rackKeys);
                    $rackNumbers = array_map(function($r) { return str_replace('Rak ', '', $r); }, $rackKeys);
                    $plantsList[] = [
                        'name' => $plant,
                        'racks' => implode(', ', $rackNumbers)
                    ];
                }
                $ev['plants_list'] = $plantsList;
                // Optional: remove raw data to save payload size
                unset($ev['plant_data']);
            }
            return $ev;
        });

        // 2. Custom Events
        $customs = CalendarEvent::all();
        foreach ($customs as $ce) {
            $events->push([
                'date' => $ce->event_date->format('Y-m-d'), 'type' => 'custom',
                'title' => $ce->title, 'description' => $ce->description,
                'time' => $ce->event_time ? Carbon::parse($ce->event_time)->format('H:i') : null,
                'event_type' => $ce->event_type, 'color' => $ce->color
            ]);
        }

        return $events->groupBy('date');
    }

    public function storeCalendarEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
        ]);

        CalendarEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'event_type' => $request->event_type ?? 'custom',
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Kegiatan berhasil ditambahkan ke kalender.');
    }

    public function greenhouses()
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

                $ditanamHolesAgg = \Illuminate\Support\Facades\DB::table('holes')
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

        return view('hydroponics.greenhouses', compact('greenhouses', 'ghStats', 'ghReadyGrouped', 'ghReadyTotal', 'ghDitanamGrouped'));
    }

    public function storeGreenhouse(Request $request)
    {
        $request->validate(['name' => 'required']);
        Greenhouse::create($request->only('name', 'description'));
        return redirect()->back()->with('success', 'Greenhouse ditambahkan.');
    }

    public function updateGreenhouse(Request $request, $id)
    {
        $gh = Greenhouse::findOrFail($id);
        $gh->update($request->only('name', 'description', 'status'));
        return redirect()->back()->with('success', 'Greenhouse diperbarui.');
    }

    public function destroyGreenhouse($id)
    {
        Greenhouse::findOrFail($id)->delete();
        return redirect()->route('hydroponics.greenhouses')->with('success', 'Greenhouse dihapus.');
    }

    public function showGreenhouse($id)
    {
        $greenhouse = Greenhouse::with(['racks' => function($query) {
            $query->withCount([
                'holes as total_holes',
                'holes as planted_holes' => function($q) {
                    $q->where('holes.status', 'ditanam');
                },
                'holes as empty_holes' => function($q) {
                    $q->where('holes.status', 'kosong');
                },
                'holes as harvested_holes' => function($q) {
                    $q->where('holes.status', 'panen');
                },
                'holes as damaged_holes' => function($q) {
                    $q->where('holes.status', 'rusak');
                },
            ]);
        }])->findOrFail($id);

        $totalHoles = $greenhouse->racks->sum('total_holes');
        $harvestedHoles = $greenhouse->racks->sum('harvested_holes');
        $damagedHoles = $greenhouse->racks->sum('damaged_holes');

        // Siap Panen Calculation specific to this greenhouse
        $plantTypeMap = PlantType::pluck('growth_days', 'name');
        $defaultDays  = 30;

        $readyToHarvestCount = Hole::whereHas('row.rack', function($q) use ($id) {
            $q->where('greenhouse_id', $id);
        })->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
          ->where('holes.status', 'ditanam')
          ->whereNotNull('holes.planted_at')
          ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day')", [$defaultDays])
          ->count();

        return view('hydroponics.greenhouse-detail', compact('greenhouse', 'totalHoles', 'harvestedHoles', 'damagedHoles', 'readyToHarvestCount'));
    }

    public function sprayGreenhouse($id)
    {
        $greenhouse = Greenhouse::findOrFail($id);
        $greenhouse->update(['last_sprayed_at' => now()]);
        return back()->with('success', 'Penyemprotan hama berhasil dicatat untuk ' . $greenhouse->name . '.');
    }

    public function printAllQr($id)
    {
        $greenhouse = Greenhouse::with('racks')->findOrFail($id);
        return view('hydroponics.greenhouse-print-qr', compact('greenhouse'));
    }

    public function printAllGreenhousesQr()
    {
        $greenhouses = Greenhouse::withCount('racks')
            ->with(['racks.rows.holes'])
            ->get()
            ->each(function ($gh) {
                $gh->holes_count = $gh->racks->sum(fn($r) => $r->holes->count());
            });
        return view('hydroponics.greenhouses-print-qr', compact('greenhouses'));
    }

    public function printGreenhouseQr($id)
    {
        $greenhouses = Greenhouse::withCount('racks')
            ->with(['racks.rows.holes'])
            ->where('id', $id)
            ->get()
            ->each(function ($gh) {
                $gh->holes_count = $gh->racks->sum(fn($r) => $r->holes->count());
            });
        return view('hydroponics.greenhouses-print-qr', compact('greenhouses'));
    }

    public function storeRack(Request $request, $greenhouse_id)
    {
        $request->validate([
            'jumlah_rak' => 'required|integer|min:1',
        ]);
        $numRows  = $request->input('num_rows', 8);
        $numHoles = $request->input('num_holes', 51);
        $jumlahRak = $request->input('jumlah_rak', 1);

        // Cari urutan terakhir Rak di greenhouse ini berdasarkan count atau angka terbesar
        $currentCount = Rack::where('greenhouse_id', $greenhouse_id)->count();
        $generatedRacks = [];

        for ($k = 1; $k <= $jumlahRak; $k++) {
            $currentCount++;
            $rackName = 'Rak ' . $currentCount;
            
            $rack = Rack::create([
                'greenhouse_id' => $greenhouse_id,
                'name' => $rackName,
            ]);
            
            for ($i = 1; $i <= $numRows; $i++) {
                $row = Row::create([
                    'rack_id' => $rack->id,
                    'name' => 'Baris ' . $i,
                ]);
                for ($j = 1; $j <= $numHoles; $j++) {
                    Hole::create([
                        'row_id' => $row->id,
                        'name' => 'L' . $j,
                    ]);
                }
            }
            $generatedRacks[] = $rackName;
        }

        $racksString = implode(', ', $generatedRacks);
        return redirect()->back()->with('success', "{$jumlahRak} Rak berhasil dibuat ({$racksString}), masing-masing dengan {$numRows} baris dan {$numHoles} lubang per baris.");
    }

    public function updateRack(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'num_rows' => 'nullable|integer|min:1'
        ]);
        
        $rack = Rack::findOrFail($id);
        $rack->update($request->only('name', 'status'));
        
        if ($request->has('num_rows')) {
            $requestedRows = (int) $request->num_rows;
            $currentRows = $rack->rows()->count();
            
            if ($requestedRows > $currentRows) {
                // Add new rows
                $rowsToAdd = $requestedRows - $currentRows;
                for ($i = 1; $i <= $rowsToAdd; $i++) {
                    $newRowIndex = $currentRows + $i;
                    $row = Row::create([
                        'rack_id' => $rack->id,
                        'name' => 'Baris ' . $newRowIndex,
                    ]);
                    for ($j = 1; $j <= 51; $j++) { // Default 51 holes
                        Hole::create([
                            'row_id' => $row->id,
                            'name' => 'L' . $j,
                        ]);
                    }
                }
            } elseif ($requestedRows < $currentRows) {
                // Remove extra rows from the end
                $rowsToRemove = $currentRows - $requestedRows;
                $rack->rows()->orderBy('id', 'desc')->take($rowsToRemove)->get()->each(function ($row) {
                    $row->delete();
                });
            }
        }

        return redirect()->back()->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroyRack($id)
    {
        $rack = Rack::findOrFail($id);
        $rack->delete();
        return redirect()->back()->with('success', 'Rak beserta baris dan lubangnya telah dihapus.');
    }

    public function destroyAllRacks($greenhouse_id)
    {
        $greenhouse = Greenhouse::findOrFail($greenhouse_id);
        $greenhouse->racks()->delete(); // This assumes onDelete cascade is correctly setup or you loop them.
        
        // Alternatively, to ensure observers/cascades fire properly:
        // $greenhouse->racks->each->delete();
        
        return redirect()->back()->with('success', 'Semua rak di Greenhouse ini berhasil dihapus secara permanen.');
    }

    public function drainRack($id)
    {
        $rack = Rack::findOrFail($id);
        $rack->update(['last_drained_at' => now()]);
        return redirect()->back()->with('success', 'Berhasil mencatat pengurasan air untuk ' . $rack->name);
    }

    public function showRack($id)
    {
        $rack = Rack::with(['rows.holes', 'greenhouse'])->findOrFail($id);
        // Plant types from master data (for dynamic dropdowns and growth duration)
        $plantTypes = PlantType::orderBy('name')->get();
        // Fallback: names from inventory bibit if no plant types yet
        $plantNames = $plantTypes->isNotEmpty()
            ? $plantTypes->pluck('name')
            : Inventory::where('type', 'bibit')->pluck('name');
        // Map name -> growth_days for JS
        $plantTypeMap = $plantTypes->pluck('growth_days', 'name');
        return view('hydroponics.rack-detail', compact('rack', 'plantNames', 'plantTypes', 'plantTypeMap'));
    }

    public function printQr($id)
    {
        $rack = Rack::with('greenhouse')->findOrFail($id);
        return view('hydroponics.rack-print-qr', compact('rack'));
    }

    public function updatePpmPh(Request $request, $id)
    {
        $rack = Rack::findOrFail($id);
        $rack->update([
            'ppm_level' => $request->ppm_level,
            'ph_level' => $request->ph_level,
            'ppm_ph_updated_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Data PPM & pH diperbarui.');
    }

    private function consumeSemaiStock($plantName, $requiredQuantity)
    {
        if ($requiredQuantity <= 0) return true;

        $semaiRecords = \App\Models\Semai::where('plant_name', $plantName)
                            ->where('status', 'aktif')
                            ->orderBy('semai_date', 'asc')
                            ->get();

        $totalAvailable = $semaiRecords->sum('quantity');

        if ($totalAvailable < $requiredQuantity) {
            return false;
        }

        $remainingToConsume = $requiredQuantity;

        foreach ($semaiRecords as $semai) {
            if ($remainingToConsume <= 0) break;

            if ($semai->quantity <= $remainingToConsume) {
                // Consume the whole batch
                $remainingToConsume -= $semai->quantity;
                $semai->update([
                    'status' => 'sudah_pindah',
                    'transferred_date' => now()->toDateString()
                ]);
            } else {
                // Consume partial batch (Split)
                $newSemai = $semai->replicate();
                $newSemai->quantity = $remainingToConsume;
                $newSemai->status = 'sudah_pindah';
                $newSemai->transferred_date = now()->toDateString();
                $newSemai->save();

                $semai->update([
                    'quantity' => $semai->quantity - $remainingToConsume
                ]);
                $remainingToConsume = 0;
            }
        }

        return true;
    }

    public function updateHole(Request $request, $id)
    {
        $hole = Hole::findOrFail($id);
        $oldStatus = $hole->status;
        $status = $request->status;
        
        $pName = $request->filled('plant_name') ? $request->plant_name : $hole->plant_name;
        
        if (in_array($status, ['ditanam', 'siap_panen']) && $oldStatus !== 'ditanam' && $pName) {
            $available = \App\Models\Semai::where('plant_name', $pName)->where('status', 'aktif')->sum('quantity');
            if ($available < 1) {
                return redirect()->back()->with('error', "Gagal: Saldo stok semai '{$pName}' tidak mencukupi (Tersedia: {$available}, Dibutuhkan: 1).");
            }
            $this->consumeSemaiStock($pName, 1);
        }

        $hole->status = $status;
        
        if ($status == 'siap_panen') {
            $hole->status = 'ditanam';
            $hole->planted_at = $request->filled('planted_at') ? \Carbon\Carbon::parse($request->planted_at) : now()->subDays(30);
            $hole->harvested_at = null;
            if ($request->filled('plant_name')) {
                $hole->plant_name = $request->plant_name;
            }
        } elseif ($status == 'ditanam') {
            $hole->status = 'ditanam';
            $hole->planted_at = $request->filled('planted_at') ? \Carbon\Carbon::parse($request->planted_at) : ($hole->planted_at ?? now());
            $hole->harvested_at = null;
            if ($request->filled('plant_name')) {
                $hole->plant_name = $request->plant_name;
            }
        } elseif ($status == 'panen') {
            $hole->status = 'panen';
            $hole->harvested_at = now();
            if ($request->filled('plant_name')) {
                $hole->plant_name = $request->plant_name;
            }
        } elseif ($status == 'rusak') {
            $hole->status = 'rusak';
            if ($request->filled('plant_name')) {
                $hole->plant_name = $request->plant_name;
            }
        } elseif ($status == 'kosong') {
            $hole->status = 'kosong';
            $hole->plant_name = null;
            $hole->planted_at = null;
            $hole->harvested_at = null;
        }
        
        $hole->save();

        $desc = $request->description;
        if (empty($desc) && $hole->plant_name) {
            $desc = ucfirst($status) . ' tanaman ' . $hole->plant_name;
        }

        $actType = $status == 'siap_panen' || $status == 'ditanam' ? 'pindah_tanam' : $status;
        \App\Models\MaintenanceLog::create([
            'loggable_type' => 'App\Models\Hole',
            'loggable_id' => $hole->id,
            'user_id' => auth()->id() ?? 1,
            'action_type' => $actType,
            'notes' => $desc,
            'details' => json_encode([
                'jumlah' => 1,
                'plant_name' => $hole->plant_name,
                'alasan' => $request->description ?? 'Update manual'
            ])
        ]);

        return redirect()->back()->with('success', 'Status lubang diperbarui.');
    }

    /**
     * Bulk update multiple holes at once (drag-select planting)
     */
    public function bulkUpdateHoles(Request $request)
    {
        $request->validate([
            'hole_ids'   => 'required|array',
            'hole_ids.*' => 'integer|exists:holes,id',
            'status'     => 'required|string',
            'plant_name' => 'nullable|string',
            'description'=> 'nullable|string',
        ]);

        $holes = Hole::whereIn('id', $request->hole_ids)->get();
        $now = now();
        $st = $request->status;

        // Pre-validate and deduct stock if transitioning to ditanam
        if (in_array($st, ['ditanam', 'siap_panen'])) {
            $holesToPlant = [];
            foreach ($holes as $hole) {
                if ($hole->status !== 'ditanam') {
                    $pName = $request->filled('plant_name') ? $request->plant_name : $hole->plant_name;
                    if ($pName) {
                        if (!isset($holesToPlant[$pName])) {
                            $holesToPlant[$pName] = 0;
                        }
                        $holesToPlant[$pName]++;
                    }
                }
            }

            foreach ($holesToPlant as $pName => $qty) {
                $available = \App\Models\Semai::where('plant_name', $pName)->where('status', 'aktif')->sum('quantity');
                if ($available < $qty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Gagal: Saldo stok semai '{$pName}' tidak mencukupi (Tersedia: {$available}, Dibutuhkan: {$qty})."
                    ], 422);
                }
            }

            foreach ($holesToPlant as $pName => $qty) {
                $this->consumeSemaiStock($pName, $qty);
            }
        }

        foreach ($holes as $hole) {
            $st = $request->status;

            if ($st == 'siap_panen') {
                $hole->status = 'ditanam';
                $hole->planted_at = now()->subDays(30);
                $hole->harvested_at = null;
                if ($request->filled('plant_name')) {
                    $hole->plant_name = $request->plant_name;
                }
            } elseif ($st == 'ditanam') {
                $hole->status = 'ditanam';
                $hole->planted_at = $hole->planted_at ?? $now;
                $hole->harvested_at = null;
                if ($request->filled('plant_name')) {
                    $hole->plant_name = $request->plant_name;
                }
            } elseif ($st == 'panen') {
                $hole->status = 'panen';
                $hole->harvested_at = $now;
                if ($request->filled('plant_name')) {
                    $hole->plant_name = $request->plant_name;
                }
            } elseif ($st == 'rusak') {
                $hole->status = 'rusak';
                if ($request->filled('plant_name')) {
                    $hole->plant_name = $request->plant_name;
                }
            } elseif ($st == 'kosong') {
                $hole->status = 'kosong';
                $hole->plant_name = null;
                $hole->planted_at = null;
                $hole->harvested_at = null;
            }

            $hole->save();

            $desc = $request->description ?? 'Penanaman massal';
            if ($hole->plant_name) {
                $desc = ucfirst($st) . ' massal ' . $hole->plant_name;
            }

            $actType = $st == 'siap_panen' || $st == 'ditanam' ? 'pindah_tanam' : $st;
            \App\Models\MaintenanceLog::create([
                'loggable_type' => 'App\Models\Hole',
                'loggable_id' => $hole->id,
                'user_id' => auth()->id() ?? 1,
                'action_type' => $actType,
                'notes' => $desc,
                'details' => json_encode([
                    'jumlah' => 1,
                    'plant_name' => $hole->plant_name,
                    'alasan' => $request->description ?? 'Update masal'
                ])
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => count($holes) . ' lubang berhasil diperbarui.',
            'count'   => count($holes),
        ]);
    }
    public function getNotifications()
    {
        $defaultDays = 30;

        $readyHoles = \App\Models\Hole::with(['row.rack.greenhouse'])
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day')", [$defaultDays])
            ->select('holes.*')
            ->get();

        $count = $readyHoles->count();
        $readCount = session('harvest_notif_read_count', 0);
        
        if ($count < $readCount) {
            session(['harvest_notif_read_count' => $count]);
            $readCount = $count;
        }

        $hasNew = $count > 0 && $count > $readCount;
        $displayCount = $count - $readCount;

        $groups = $readyHoles->map(function ($hole) {
            $ghName = optional(optional(optional($hole->row)->rack)->greenhouse)->name ?? 'GH Unknown';
            $rackName = optional(optional($hole->row)->rack)->name ?? 'Rak Unknown';
            return [
                'plant' => $hole->plant_name ?? 'Unknown',
                'gh_name' => $ghName,
                'rack_name' => $rackName,
                'planted_at' => $hole->planted_at,
            ];
        })->groupBy('plant');

        $html = view('components.notifications', ['readyGroups' => $groups, 'count' => $count])->render();

        return response()->json([
            'count' => $hasNew ? $displayCount : $count,
            'has_new' => $hasNew,
            'html' => $html
        ]);
    }

    public function markNotificationsRead()
    {
        $defaultDays = 30;

        $count = \App\Models\Hole::leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("holes.planted_at <= NOW() - (COALESCE(plant_types.growth_days, ?) * INTERVAL '1 day')", [$defaultDays])
            ->count();

        session(['harvest_notif_read_count' => $count]);
        return response()->json(['success' => true]);
    }

    private function buildSiapPanenHtml($readyHoles)
    {
        // Optimasi: Agregasi langsung via SQL (Jauh lebih cepat dari looping model)
        $readyData = \Illuminate\Support\Facades\DB::table('holes')
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
                    $minAge = \Carbon\Carbon::parse($item->max_planted)->diffInDays(now()); // max planted_at = youngest
                    $maxAge = \Carbon\Carbon::parse($item->min_planted)->diffInDays(now()); // min planted_at = oldest
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
    }
}