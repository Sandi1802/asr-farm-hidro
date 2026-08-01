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
        $countKosong  = $allHoles->where('status', 'kosong')->count();
        $countPanen   = $allHoles->where('status', 'panen')->count();
        $countRusak   = $allHoles->where('status', 'rusak')->count();
        $totalDitanam = $allHoles->where('status', 'ditanam')->count();

        $defaultGrowthDays = 30;

        $plantsReadyToHarvest = $allHoles->where('status', 'ditanam')
            ->filter(function($h) use ($plantTypeMap, $defaultGrowthDays) {
                if (empty($h->plant_name) || !$h->planted_at) return false;
                $days = $plantTypeMap[$h->plant_name] ?? $defaultGrowthDays;
                return \Carbon\Carbon::parse($h->planted_at)->addDays($days)->lte(now());
            })
            ->groupBy('plant_name')
            ->map(fn($g) => $g->count());

        $countReady = $plantsReadyToHarvest->sum();

        $plantsPlanted = $allHoles->where('status', 'ditanam')
            ->filter(fn($h) => !empty($h->plant_name))
            ->groupBy('plant_name')
            ->map(fn($g) => $g->count());

        return view('hydroponics.scan.rack-result', compact(
            'rack', 'plantTypes', 'plantTypeMap',
            'countKosong', 'countPanen', 'countRusak', 'totalDitanam',
            'countReady', 'plantsPlanted', 'plantsReadyToHarvest'
        ));
    }
}
