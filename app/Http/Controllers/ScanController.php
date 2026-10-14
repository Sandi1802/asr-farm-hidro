<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Greenhouse;
use App\Models\Rack;
use App\Models\PlantType;
use App\Models\Hole;
use Carbon\Carbon;

class ScanController extends Controller
{
    /**
     * Display Greenhouse info for Mobile Web View
     */
    public function scanGreenhouse($id)
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
        $plantedHoles = $greenhouse->racks->sum('planted_holes');
        $emptyHoles = $greenhouse->racks->sum('empty_holes');
        $harvestedHoles = $greenhouse->racks->sum('harvested_holes');
        $damagedHoles = $greenhouse->racks->sum('damaged_holes');

        // Plant Types Calculation
        $plantTypeMap = PlantType::pluck('growth_days', 'name');
        $defaultDays  = 30;

        $activePlanted = Hole::whereHas('row.rack', function($q) use ($id) {
            $q->where('greenhouse_id', $id);
        })->where('status', 'ditanam')
          ->whereNotNull('planted_at')
          ->get(['id', 'plant_name', 'planted_at']);

        // Group active plants by name and count
        $plantsPlanted = $activePlanted->groupBy('plant_name')->map->count();

        // Siap Panen (Ready to harvest)
        $readyToHarvestCount = $activePlanted->filter(function ($hole) use ($plantTypeMap, $defaultDays) {
            $days = $plantTypeMap->get($hole->plant_name, $defaultDays);
            return Carbon::parse($hole->planted_at)->addDays($days)->lte(now());
        })->count();

        return view('hydroponics.scan.gh-result', compact(
            'greenhouse', 'totalHoles', 'plantedHoles', 'emptyHoles', 
            'harvestedHoles', 'damagedHoles', 'readyToHarvestCount', 'plantsPlanted'
        ));
    }

    /**
     * Display Rack info for Mobile Web View
     */
    public function scanRack($id)
    {
        $rack = Rack::with(['rows.holes', 'greenhouse'])->findOrFail($id);
        
        $plantTypes = PlantType::orderBy('name')->get();
        $plantTypeMap = $plantTypes->pluck('growth_days', 'name');
        
        $allHoles = $rack->rows->flatMap->holes;
        $totalHoles   = $allHoles->count();
        $countKosong  = $allHoles->where('status', 'kosong')->count();
        $countPanen   = $allHoles->where('status', 'panen')->count();
        $countRusak   = $allHoles->where('status', 'rusak')->count();
        $totalDitanam = $allHoles->where('status', 'ditanam')->count();

        $defaultGrowthDays = 30;

        $activeHoles = $allHoles->where('status', 'ditanam')
            ->filter(fn($h) => !empty($h->plant_name));

        // Group by plant_name AND planted_at date
        $batchGroups = $activeHoles->groupBy(function($h) {
            $dateKey = $h->planted_at ? Carbon::parse($h->planted_at)->format('Y-m-d') : 'unknown';
            return $h->plant_name . '___' . $dateKey;
        });

        $batches = collect();
        $countReady = 0;

        foreach ($batchGroups as $batchKey => $holesInBatch) {
            $firstHole = $holesInBatch->first();
            $plantName = $firstHole->plant_name;
            $plantedAt = $firstHole->planted_at ? Carbon::parse($firstHole->planted_at) : null;
            $growthDays = $plantTypeMap[$plantName] ?? $defaultGrowthDays;

            $ageDays = $plantedAt ? (int) $plantedAt->diffInDays(now()) : 0;
            $isReady = $plantedAt ? ($ageDays >= $growthDays) : false;
            $daysRemaining = max(0, $growthDays - $ageDays);
            $estimatedHarvest = $plantedAt ? $plantedAt->copy()->addDays($growthDays) : null;
            $count = $holesInBatch->count();

            if ($isReady) {
                $countReady += $count;
            }

            $batches->push([
                'name' => $plantName,
                'plant_name' => $plantName,
                'count' => $count,
                'planted_at' => $plantedAt ? $plantedAt->format('Y-m-d') : null,
                'planted_at_formatted' => $plantedAt ? $plantedAt->locale('id')->isoFormat('dddd, D MMMM YYYY') : 'Tanpa Tanggal',
                'planted_date' => $plantedAt ? $plantedAt->locale('id')->isoFormat('D MMM YYYY') : 'Tanpa Tanggal',
                'day_name' => $plantedAt ? $plantedAt->locale('id')->isoFormat('dddd') : '-',
                'age_days' => $ageDays,
                'hst' => $ageDays,
                'growth_days' => $growthDays,
                'days_remaining' => $daysRemaining,
                'is_ready' => $isReady,
                'progress_percent' => $growthDays > 0 ? min(100, (int) round(($ageDays / $growthDays) * 100)) : 100,
                'estimated_harvest_at' => $estimatedHarvest ? $estimatedHarvest->locale('id')->isoFormat('dddd, D MMMM YYYY') : null,
            ]);
        }

        // Sort: ready to harvest first, then oldest planted
        $batches = $batches->sort(function($a, $b) {
            if ($a['is_ready'] !== $b['is_ready']) {
                return $b['is_ready'] <=> $a['is_ready'];
            }
            return ($b['age_days'] ?? 0) <=> ($a['age_days'] ?? 0);
        })->values();

        $uniquePlantTypes = $activeHoles->pluck('plant_name')->unique()->values();
        $plantsPlanted = $activeHoles->groupBy('plant_name')->map->count();

        return view('hydroponics.scan.rack-result', compact(
            'rack', 'plantTypes', 'plantTypeMap', 'totalHoles',
            'countKosong', 'countPanen', 'countRusak', 'totalDitanam',
            'countReady', 'plantsPlanted', 'batches', 'uniquePlantTypes'
        ));
    }
}
