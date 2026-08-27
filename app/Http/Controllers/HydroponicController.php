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
        
        $logs = \App\Models\MaintenanceLog::whereMonth('created_at', now()->month)->get();
        // Panen details grouped by plant_name
        $panenLogs = $logs->where('action_type', 'panen');
        $harvestedHoles = $panenLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
        $harvestedByPlant = [];
        foreach ($panenLogs as $l) {
            $det = json_decode($l->details);
            $pName = $det->plant_name ?? 'Tidak Diketahui';
            $qty = $det->jumlah ?? 0;
            if (!isset($harvestedByPlant[$pName])) {
                $harvestedByPlant[$pName] = 0;
            }
            $harvestedByPlant[$pName] += $qty;
        }
        $harvestedTypesCount = count(array_filter(array_keys($harvestedByPlant), fn($k) => $k !== 'Tidak Diketahui')) ?: count($harvestedByPlant);

        // Rusak details grouped by alasan
        $rusakLogs = $logs->where('action_type', 'rusak');
        $damagedHoles = $rusakLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
        $damagedByReason = [];
        foreach ($rusakLogs as $l) {
            $det = json_decode($l->details);
            $reason = $det->alasan ?? 'Lainnya';
            $qty = $det->jumlah ?? 0;
            if (!isset($damagedByReason[$reason])) {
                $damagedByReason[$reason] = 0;
            }
            $damagedByReason[$reason] += $qty;
        }
        $damagedTypesCount = count($damagedByReason);

        // Build a map of plant_name -> growth_days from plant_types
        $plantTypeMap = PlantType::pluck('growth_days', 'name');  // ['Pakcoy' => 20, ...]
        $defaultDays  = 30;

        // Siap Panen — per-plant dynamic threshold
        $readyIds = collect();
        foreach ($plantTypeMap as $plantName => $days) {
            $thresholdDate = now()->subDays($days);
            $ids = Hole::where('status', 'ditanam')
                ->where('plant_name', $plantName)
                ->whereDate('planted_at', '<=', $thresholdDate)
                ->pluck('id');
            $readyIds = $readyIds->merge($ids);
        }
        $knownPlants = $plantTypeMap->keys()->toArray();
        $defaultThreshold = now()->subDays($defaultDays);
        $ids = Hole::where('status', 'ditanam')
            ->whereNotNull('plant_name')
            ->whereNotIn('plant_name', $knownPlants)
            ->whereDate('planted_at', '<=', $defaultThreshold)
            ->pluck('id');
        $readyIds = $readyIds->merge($ids);

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

        // Tanam / Pindah ke GH (Bulan ini) -> dari Activity
        $tanamBulanIni = Activity::where('type', 'tanam')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

        // Panen (Bulan ini) -> dari Activity
        $panenBulanIni = Activity::where('type', 'panen')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();

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
        $topHarvestedQuery = \App\Models\Hole::whereNotNull('plant_name')
            ->whereNotNull('harvested_at')
            ->selectRaw('plant_name, count(*) as count')
            ->groupBy('plant_name')
            ->orderByDesc('count')
            ->take(8)
            ->pluck('count', 'plant_name')
            ->toArray();
        $mostHarvestedLabels = array_keys($topHarvestedQuery);
        $mostHarvestedValues = array_values($topHarvestedQuery);

        // ─── CHART: Tingkat Occupancy Tiap Greenhouse (Perputaran) ───
        $greenhousesList = Greenhouse::all();
        $rotationData = [];
        foreach ($greenhousesList as $gh) {
            $ghTotal     = Hole::whereHas('row.rack', fn($q) => $q->where('greenhouse_id', $gh->id))->count();
            $ghPlanted   = Hole::whereHas('row.rack', fn($q) => $q->where('greenhouse_id', $gh->id))->where('status', 'ditanam')->count();
            $rackIds = $gh->racks->pluck('id')->toArray();
            $ghHarvested = \App\Models\MaintenanceLog::where('loggable_type', 'App\Models\Rack')
                ->whereIn('loggable_id', $rackIds)
                ->whereMonth('created_at', now()->month)
                ->where('action_type', 'panen')
                ->get()
                ->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
            $ghReady = Hole::whereHas('row.rack', fn($q) => $q->where('greenhouse_id', $gh->id))
                ->whereIn('id', $readyIds)
                ->count();
            $rotationData[] = [
                'name'      => $gh->name,
                'total'     => $ghTotal,
                'planted'   => $ghPlanted,
                'harvested' => $ghHarvested,
                'ready'     => $ghReady,
                'rate'      => $ghTotal > 0 ? round(($ghPlanted / $ghTotal) * 100, 1) : 0,
            ];
        }


        // Data for GH Distribution Chart
        $ghDistribution = \App\Models\Greenhouse::with('racks')->get()->map(function($gh) {
            $racksCount = $gh->racks->count();
            $plantedCount = \App\Models\Hole::whereHas('row.rack', function($q) use ($gh) {
                $q->where('greenhouse_id', $gh->id);
            })->where('status', 'ditanam')->count();
            $plants = \App\Models\Hole::whereHas('row.rack', function($q) use ($gh) {
                $q->where('greenhouse_id', $gh->id);
            })->where('status', 'ditanam')->whereNotNull('plant_name')->distinct('plant_name')->pluck('plant_name')->toArray();
            
            return [
                'name' => $gh->name,
                'racks' => $racksCount,
                'planted_count' => $plantedCount,
                'plants' => $plants
            ];
        });

        return view('hydroponics.dashboard', compact(
            'totalGH', 'totalRacks', 'totalHoles', 'plantedHoles',
            'harvestedHoles', 'damagedHoles', 'inventoryByCategory',
            'inventoryItems', 'recentActivities',
            'readyToHarvestCount', 'readyToHarvestItems',
            'emptyHolesCount', 'occupancyRate', 'totalInventoryItems',
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
        $panenLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'panen')->get();
        $totalPanen = $panenLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });

        // Rusak in period
        $rusakLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'rusak')->get();
        $totalRusak = $rusakLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });

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
                $panenLogs = \App\Models\MaintenanceLog::whereYear('created_at', $year)->where('action_type', 'panen')->get();
                $panen[] = $panenLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
                $rusakLogs = \App\Models\MaintenanceLog::whereYear('created_at', $year)->where('action_type', 'rusak')->get();
                $rusak[] = $rusakLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
            }
        } elseif ($period === 'bulanan') {
            for ($i = 5; $i >= 0; $i--) {
                $start = now()->subMonths($i)->startOfMonth();
                $end   = now()->subMonths($i)->endOfMonth();
                $labels[] = $start->translatedFormat('M Y');
                $semai[] = \App\Models\Semai::whereBetween('semai_date', [$start, $end])->sum('quantity');
                $tanam[] = \App\Models\Hole::whereBetween('planted_at', [$start, $end])->count();
                $panenLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'panen')->get();
                $panen[] = $panenLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
                $rusakLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'rusak')->get();
                $rusak[] = $rusakLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
            }
        } else {
            // mingguan
            for ($i = 3; $i >= 0; $i--) {
                $start = now()->subWeeks($i)->startOfWeek();
                $end   = now()->subWeeks($i)->endOfWeek();
                $labels[] = "Mg " . $start->format('d/m');
                $semai[] = \App\Models\Semai::whereBetween('semai_date', [$start, $end])->sum('quantity');
                $tanam[] = \App\Models\Hole::whereBetween('planted_at', [$start, $end])->count();
                $panenLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'panen')->get();
                $panen[] = $panenLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
                $rusakLogs = \App\Models\MaintenanceLog::whereBetween('created_at', [$start, $end])->where('action_type', 'rusak')->get();
                $rusak[] = $rusakLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
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
            
            $plantedHolesGroup = \App\Models\Hole::where('status', 'ditanam')->whereNotNull('plant_name')->get()->groupBy('plant_name');
            $plantedHoles = \App\Models\Hole::where('status', 'ditanam')->count();
            $plantedTypesCount = $plantedHolesGroup->count();

            $logs = \App\Models\MaintenanceLog::whereMonth('created_at', now()->month)->get();
            
            // Panen details grouped by plant_name
            $panenLogs = $logs->where('action_type', 'panen');
            $harvestedHoles = $panenLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
            $harvestedByPlant = [];
            foreach ($panenLogs as $l) {
                $det = json_decode($l->details);
                $pName = $det->plant_name ?? 'Tidak Diketahui';
                $qty = $det->jumlah ?? 0;
                if (!isset($harvestedByPlant[$pName])) {
                    $harvestedByPlant[$pName] = 0;
                }
                $harvestedByPlant[$pName] += $qty;
            }
            $harvestedTypesCount = count(array_filter(array_keys($harvestedByPlant), fn($k) => $k !== 'Tidak Diketahui')) ?: count($harvestedByPlant);

            // Rusak details grouped by alasan
            $rusakLogs = $logs->where('action_type', 'rusak');
            $damagedHoles = $rusakLogs->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
            $damagedByReason = [];
            foreach ($rusakLogs as $l) {
                $det = json_decode($l->details);
                $reason = $det->alasan ?? 'Lainnya';
                $qty = $det->jumlah ?? 0;
                if (!isset($damagedByReason[$reason])) {
                    $damagedByReason[$reason] = 0;
                }
                $damagedByReason[$reason] += $qty;
            }
            $damagedTypesCount = count($damagedByReason);
            
            // Siap panen logic
            $plantTypes = \App\Models\PlantType::all()->keyBy('name');
            $defaultDays = 30;
            $activePlanted = \App\Models\Hole::where('status', 'ditanam')->whereNotNull('planted_at')->get(['id', 'plant_name', 'planted_at']);
            $readyIds = $activePlanted->filter(function ($hole) use ($plantTypes, $defaultDays, $month, $year) {
                $pt = $plantTypes->get($hole->plant_name);
                $days = $pt ? ($pt->growth_days - $pt->semai_days) : $defaultDays;
                $harvestDate = \Carbon\Carbon::parse($hole->planted_at)->addDays($days);
                return $harvestDate->month == (int)$month && $harvestDate->year == (int)$year;
            })->pluck('id');
            $readyToHarvestCount = $readyIds->count();
            $readyTypesCount = \App\Models\Hole::whereIn('id', $readyIds)->whereNotNull('plant_name')->get()->groupBy('plant_name')->count();
            
            $panenBulanIni = \App\Models\Activity::where('type', 'panen')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $tanamBulanIni = \App\Models\Activity::where('type', 'tanam')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $semaiBulanIni = \App\Models\Semai::whereMonth('semai_date', $month)->whereYear('semai_date', $year)->sum('quantity');

            return response()->json([
                'lubang_kosong' => number_format($emptyHolesCount,0,',','.'),
                'lubang_terisi' => number_format($plantedHoles,0,',','.'),
                'lubang_terisi_sub' => $plantedTypesCount.' Jenis Tanaman',
                'siap_panen' => number_format($readyToHarvestCount,0,',','.'),
                'siap_panen_sub' => $readyTypesCount.' Jenis Tanaman',
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
            // Historical from Activity
            $plantedHoles = \App\Models\Activity::where('type', 'tanam')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $harvestedHoles = \App\Models\Activity::where('type', 'panen')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $damagedHoles = \App\Models\Activity::where('type', 'rusak')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            
            $emptyHolesCount = $totalHoles - $plantedHoles;
            if ($emptyHolesCount < 0) $emptyHolesCount = 0;
            
            // Siap panen logic (Projected from current planted holes)
            $plantTypes = \App\Models\PlantType::all()->keyBy('name');
            $defaultDays = 30;
            $activePlanted = \App\Models\Hole::where('status', 'ditanam')->whereNotNull('planted_at')->get(['id', 'plant_name', 'planted_at']);
            $readyIds = $activePlanted->filter(function ($hole) use ($plantTypes, $defaultDays, $month, $year) {
                $pt = $plantTypes->get($hole->plant_name);
                $days = $pt ? ($pt->growth_days - $pt->semai_days) : $defaultDays;
                $harvestDate = \Carbon\Carbon::parse($hole->planted_at)->addDays($days);
                return $harvestDate->month == (int)$month && $harvestDate->year == (int)$year;
            })->pluck('id');
            
            $readyToHarvestCount = $readyIds->count();
            $readyTypesCount = \App\Models\Hole::whereIn('id', $readyIds)->whereNotNull('plant_name')->get()->groupBy('plant_name')->count();

            $panenBulanIniHist = \App\Models\Activity::where('type', 'panen')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $tanamBulanIniHist = \App\Models\Activity::where('type', 'tanam')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count();
            $semaiBulanIniHist = \App\Models\Semai::whereMonth('semai_date', $month)->whereYear('semai_date', $year)->sum('quantity');

            return response()->json([
                'lubang_kosong' => number_format($emptyHolesCount,0,',','.'),
                'lubang_terisi' => number_format($plantedHoles,0,',','.'),
                'lubang_terisi_sub' => 'Total Penanaman',
                'siap_panen' => number_format($readyToHarvestCount,0,',','.'),
                'siap_panen_sub' => $readyTypesCount.' Jenis (Proyeksi)',
                'sudah_panen' => number_format($harvestedHoles,0,',','.'),
                'sudah_panen_sub' => 'Total Panen',
                'gagal_panen' => number_format($damagedHoles,0,',','.'),
                'gagal_panen_sub' => 'Total Kerusakan',
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
        $greenhouses = Greenhouse::with(['racks.rows.holes'])->withCount('racks')->get();
        $plantTypeMap = PlantType::pluck('growth_days', 'name');
        $defaultDays = 30;

        return view('hydroponics.greenhouses', compact('greenhouses', 'plantTypeMap', 'defaultDays'));
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

        $activePlanted = Hole::whereHas('row.rack', function($q) use ($id) {
            $q->where('greenhouse_id', $id);
        })->where('status', 'ditanam')
          ->whereNotNull('planted_at')
          ->get(['id', 'plant_name', 'planted_at']);

        $readyToHarvestCount = $activePlanted->filter(function ($hole) use ($plantTypeMap, $defaultDays) {
            $days = $plantTypeMap->get($hole->plant_name, $defaultDays);
            return Carbon::parse($hole->planted_at)->addDays($days)->lte(now());
        })->count();

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

        Activity::create([
            'user_id' => auth()->id(),
            'hole_id' => $hole->id,
            'type' => $status == 'siap_panen' ? 'ditanam' : $status,
            'description' => $desc,
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

            Activity::create([
                'user_id' => auth()->id(),
                'hole_id' => $hole->id,
                'type' => $st == 'siap_panen' ? 'ditanam' : $st,
                'description' => $desc,
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
        $plantTypeMap = \App\Models\PlantType::pluck('growth_days', 'name');
        $defaultDays = 30;

        $activePlanted = \App\Models\Hole::with(['row.rack.greenhouse'])->where('status', 'ditanam')->whereNotNull('planted_at')->get();
        $readyHoles = $activePlanted->filter(function ($hole) use ($plantTypeMap, $defaultDays) {
            $days = $plantTypeMap->get($hole->plant_name, $defaultDays);
            return \Carbon\Carbon::parse($hole->planted_at)->addDays($days)->lte(now());
        });

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
        $plantTypeMap = \App\Models\PlantType::pluck('growth_days', 'name');
        $defaultDays = 30;

        $count = \App\Models\Hole::where('status', 'ditanam')->whereNotNull('planted_at')->get()
            ->filter(function ($hole) use ($plantTypeMap, $defaultDays) {
                $days = $plantTypeMap->get($hole->plant_name, $defaultDays);
                return \Carbon\Carbon::parse($hole->planted_at)->addDays($days)->lte(now());
            })->count();

        session(['harvest_notif_read_count' => $count]);
        return response()->json(['success' => true]);
    }
}
