<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Greenhouse;
use Carbon\Carbon;

class GreenhouseApiController extends Controller
{
    public function index()
    {
        try {
            $greenhouses = Greenhouse::with(['racks.rows.holes'])->orderBy('name', 'asc')->get();

            $data = $greenhouses->map(function ($gh) {
                $allHoles = $gh->racks->flatMap->rows->flatMap->holes;
                $rackIds = $gh->racks->pluck('id')->toArray();
                $logs = \App\Models\MaintenanceLog::where('loggable_type', 'App\Models\Rack')
                    ->whereIn('loggable_id', $rackIds)
                    ->whereMonth('created_at', now()->month)
                    ->get();
                
                $panen = $logs->where('action_type', 'panen')->sum(function($l) {
                    return json_decode($l->details)->jumlah ?? 0;
                });
                
                $rusak = $logs->where('action_type', 'rusak')->sum(function($l) {
                    return json_decode($l->details)->jumlah ?? 0;
                });

                return [
                    'id' => $gh->id,
                    'name' => $gh->name,
                    'last_sprayed_at' => $gh->last_sprayed_at ? Carbon::parse($gh->last_sprayed_at)->translatedFormat('d M Y') : 'Belum pernah dicatat',
                    'racks_count' => $gh->racks->count(),
                    'stats' => [
                        'total_holes' => $allHoles->count(),
                        'ditanam' => $allHoles->where('status', 'ditanam')->count(),
                        'kosong' => $allHoles->where('status', 'kosong')->count(),
                        'panen' => $panen,
                        'rusak' => $rusak,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data greenhouses',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $gh = Greenhouse::with(['racks.rows.holes'])->find($id);

            if (!$gh) {
                return response()->json([
                    'success' => false,
                    'message' => 'Greenhouse tidak ditemukan'
                ], 404);
            }

            $allHoles = $gh->racks->flatMap->rows->flatMap->holes;
            
            $countKosong = $allHoles->where('status', 'kosong')->count();
            $rackIds = $gh->racks->pluck('id')->toArray();
            $logs = \App\Models\MaintenanceLog::where('loggable_type', 'App\Models\Rack')
                ->whereIn('loggable_id', $rackIds)
                ->whereMonth('created_at', now()->month)
                ->get();
            
            $countPanen = $logs->where('action_type', 'panen')->sum(function($l) {
                return json_decode($l->details)->jumlah ?? 0;
            });
            
            $countRusak = $logs->where('action_type', 'rusak')->sum(function($l) {
                return json_decode($l->details)->jumlah ?? 0;
            });
            $totalDitanam = $allHoles->where('status', 'ditanam')->count();

            $defaultGrowthDays = 35;
            $plantTypes = \App\Models\PlantType::all()->keyBy('name');

            $plantedPlants = $allHoles->where('status', 'ditanam')
                ->filter(fn($h) => !empty($h->plant_name))
                ->groupBy('plant_name');

            $plantsArray = [];
            foreach ($plantedPlants as $name => $group) {
                $oldestHole = $group->filter(fn($h) => $h->planted_at != null)->sortBy('planted_at')->first();
                $ageDays = $oldestHole ? Carbon::parse($oldestHole->planted_at)->diffInDays(now()) : 0;
                
                $plantsArray[] = [
                    'name' => $name,
                    'count' => $group->count(),
                    'age_days' => $ageDays
                ];
            }

            $totalTargetPopulasi = 0;
            $totalRealisasiTanam = 0;
            $totalSisaTanam = 0;
            $totalEstPanenKg = 0;

            $racksList = $gh->racks->sortBy('name', SORT_NATURAL)->values()->map(function($r) use ($plantTypes, $defaultGrowthDays, &$totalTargetPopulasi, &$totalRealisasiTanam, &$totalSisaTanam, &$totalEstPanenKg) {
                $rHoles = $r->rows->flatMap->holes;
                $targetPopulasi = $rHoles->count();
                $plantedHoles = $rHoles->where('status', 'ditanam');
                $realisasiTanam = $plantedHoles->count();
                $sisaTanam = $rHoles->where('status', 'kosong')->count();

                $persenCapaian = $targetPopulasi > 0 ? round(($realisasiTanam / $targetPopulasi) * 100, 1) : 0;

                // Komoditas
                $activePlantNames = $plantedHoles->pluck('plant_name')->filter()->unique()->values();
                $komoditas = $activePlantNames->count() > 0 ? $activePlantNames->implode(' & ') : '-';

                // Tanggal Tanam & Hari
                $holesWithDate = $plantedHoles->filter(fn($h) => !empty($h->planted_at))->sortBy('planted_at');
                $firstHole = $holesWithDate->first();
                $plantedAt = $firstHole ? Carbon::parse($firstHole->planted_at) : null;

                // Plant Type target days
                $firstPlantName = $firstHole ? $firstHole->plant_name : ($activePlantNames->first() ?? null);
                $pt = $firstPlantName ? $plantTypes->get($firstPlantName) : null;
                $masaPanenHst = $pt ? ($pt->growth_days ?: $defaultGrowthDays) : $defaultGrowthDays;

                $hst = $plantedAt ? (int) $plantedAt->diffInDays(now()) : 0;
                $isReady = $plantedAt ? ($hst >= $masaPanenHst) : false;

                // Estimated harvest
                $estTglPanen = $plantedAt ? $plantedAt->copy()->addDays($masaPanenHst) : null;

                // Yield ratio: standard ~13 lubang per 1 kg
                $lubangPerKg = 13;
                $estPanenKg = $realisasiTanam > 0 ? (int) round($realisasiTanam / $lubangPerKg) : 0;

                // Status
                $status = 'Belum mulai';
                if ($realisasiTanam > 0) {
                    $status = $isReady ? 'Selesai' : 'Berjalan';
                }

                $totalTargetPopulasi += $targetPopulasi;
                $totalRealisasiTanam += $realisasiTanam;
                $totalSisaTanam += $sisaTanam;
                $totalEstPanenKg += $estPanenKg;

                return [
                    'id' => $r->id,
                    'name' => $r->name,
                    'ppm_level' => $r->ppm_level,
                    'ph_level' => $r->ph_level,
                    'ditanam' => $realisasiTanam,
                    'kosong' => $sisaTanam,
                    'target_populasi' => $targetPopulasi,
                    'realisasi_tanam' => $realisasiTanam,
                    'sisa_tanam' => $sisaTanam,
                    'persen_capaian' => $persenCapaian,
                    'komoditas' => $komoditas,
                    'tgl_tanam' => $plantedAt ? $plantedAt->locale('id')->isoFormat('dddd, D MMMM YYYY') : null,
                    'tgl_tanam_short' => $plantedAt ? $plantedAt->locale('id')->isoFormat('D MMM YYYY') : null,
                    'hari_tanam' => $plantedAt ? $plantedAt->locale('id')->isoFormat('dddd') : null,
                    'masa_panen_hst' => $masaPanenHst,
                    'hst' => $hst,
                    'umur_hari' => $hst,
                    'est_tgl_panen' => $estTglPanen ? $estTglPanen->locale('id')->isoFormat('D MMM YYYY') : null,
                    'bulan_panen' => $estTglPanen ? $estTglPanen->locale('id')->isoFormat('MMMM YYYY') : null,
                    'lubang_per_kg' => $lubangPerKg,
                    'est_panen_kg' => $estPanenKg,
                    'status' => $status,
                    'is_ready' => $isReady,
                ];
            });

            $persenCapaianGH = $totalTargetPopulasi > 0 ? round(($totalRealisasiTanam / $totalTargetPopulasi) * 100, 1) : 0;
            $statusGH = $totalRealisasiTanam > 0 ? 'Berjalan' : 'Belum mulai';

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $gh->id,
                    'name' => $gh->name,
                    'last_sprayed_at' => $gh->last_sprayed_at ? Carbon::parse($gh->last_sprayed_at)->translatedFormat('d M Y') : 'Belum pernah dicatat',
                    'racks_count' => $gh->racks->count(),
                    'stats' => [
                        'total_holes' => $allHoles->count(),
                        'ditanam' => $totalDitanam,
                        'kosong' => $countKosong,
                        'panen' => $countPanen,
                        'rusak' => $countRusak,
                    ],
                    'table_summary' => [
                        'target_populasi' => $totalTargetPopulasi,
                        'realisasi_tanam' => $totalRealisasiTanam,
                        'persen_capaian' => $persenCapaianGH,
                        'sisa_tanam' => $totalSisaTanam,
                        'est_panen_kg' => $totalEstPanenKg,
                        'status' => $statusGH,
                    ],
                    'plants' => $plantsArray,
                    'racks' => $racksList,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail greenhouse: ' . $e->getMessage()
            ], 500);
        }
    }

    public function spray($id)
    {
        try {
            $gh = Greenhouse::find($id);
            if (!$gh) {
                return response()->json(['success' => false, 'message' => 'Greenhouse tidak ditemukan'], 404);
            }

            $gh->update(['last_sprayed_at' => now()]);
            
            \App\Models\MaintenanceLog::create([
                'loggable_type' => 'App\Models\Greenhouse',
                'loggable_id' => $gh->id,
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'penyemprotan',
                'notes' => 'Melakukan penyemprotan Greenhouse'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Penyemprotan berhasil dicatat'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencatat penyemprotan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkPlant(Request $request, $id)
    {
        $request->validate([
            'racks' => 'required|array',
            'racks.*.rack_id' => 'required|exists:racks,id',
            'racks.*.plant_name' => 'required|string',
            'racks.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            \DB::beginTransaction();
            $gh = Greenhouse::find($id);
            if (!$gh) return response()->json(['success' => false, 'message' => 'GH not found'], 404);

            $totalPlanted = 0;
            foreach ($request->racks as $item) {
                $rackId = $item['rack_id'];
                $qty = $item['quantity'];
                $plantName = $item['plant_name'];

                $holes = \App\Models\Hole::whereHas('row', function($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                })->where('status', 'kosong')->orderBy('id')->limit($qty)->get();

                if ($holes->count() < $qty) {
                    throw new \Exception("Sisa lubang kosong di rak tidak mencukupi untuk menanam $qty benih.");
                }

                $plantedAt = isset($item['planted_at']) ? \Carbon\Carbon::parse($item['planted_at']) : now();

                $holeIds = $holes->pluck('id')->toArray();
                \App\Models\Hole::whereIn('id', $holeIds)->update([
                    'status' => 'ditanam',
                    'plant_name' => $plantName,
                    'planted_at' => $plantedAt,
                    'harvested_at' => null
                ]);

                $totalPlanted += $qty;
                
                \App\Models\MaintenanceLog::create([
                    'loggable_type' => 'App\Models\Rack',
                    'loggable_id' => $rackId,
                    'user_id' => auth()->id() ?? 1,
                    'action_type' => 'pindah_tanam',
                    'notes' => "Pindah tanam $qty $plantName",
                    'details' => json_encode(['jumlah' => $qty, 'plant_name' => $plantName])
                ]);
            }
            \DB::commit();
            return response()->json(['success' => true, 'message' => "Berhasil menanam $totalPlanted benih secara masal."]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkHarvest(Request $request, $id)
    {
        $request->validate([
            'racks' => 'required|array',
            'racks.*.rack_id' => 'required|exists:racks,id',
            'racks.*.quantity' => 'required|integer|min:1'
        ]);

        try {
            \DB::beginTransaction();
            $gh = Greenhouse::find($id);
            if (!$gh) return response()->json(['success' => false, 'message' => 'GH not found'], 404);

            $totalHarvest = 0;
            foreach ($request->racks as $item) {
                $rackId = $item['rack_id'];
                $qty = $item['quantity'];

                $holes = \App\Models\Hole::whereHas('row', function($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                })->where('status', 'ditanam')->orderBy('planted_at', 'asc')->limit($qty)->get();

                if ($holes->count() < $qty) {
                    throw new \Exception("Tanaman yang bisa dipanen di rak tidak mencukupi.");
                }
                
                $holeIds = $holes->pluck('id')->toArray();
                \App\Models\Hole::whereIn('id', $holeIds)->update([
                    'status' => 'kosong',
                    'plant_name' => null,
                    'planted_at' => null,
                    'harvested_at' => now(),
                ]);

                $totalHarvest += $qty;
                
                \App\Models\MaintenanceLog::create([
                    'loggable_type' => 'App\Models\Rack',
                    'loggable_id' => $rackId,
                    'user_id' => auth()->id() ?? 1,
                    'action_type' => 'panen',
                    'notes' => "Panen $qty tanaman",
                    'details' => json_encode(['jumlah' => $qty])
                ]);
            }
            \DB::commit();
            return response()->json(['success' => true, 'message' => "Berhasil memanen $totalHarvest tanaman."]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDamage(Request $request, $id)
    {
        $request->validate([
            'racks' => 'required|array',
            'racks.*.rack_id' => 'required|exists:racks,id',
            'racks.*.quantity' => 'required|integer|min:1',
            'racks.*.reason' => 'required|string'
        ]);

        try {
            \DB::beginTransaction();
            
            $totalDamage = 0;
            foreach ($request->racks as $item) {
                $rackId = $item['rack_id'];
                $qty = $item['quantity'];
                $reason = $item['reason'];

                $holes = \App\Models\Hole::whereHas('row', function($q) use ($rackId) {
                    $q->where('rack_id', $rackId);
                })->where('status', 'ditanam')->orderBy('id')->limit($qty)->get();

                if ($holes->count() < $qty) {
                    throw new \Exception("Jumlah tanaman ditanam di rak tidak mencukupi untuk dilaporkan rusak.");
                }

                $holeIds = $holes->pluck('id')->toArray();
                \App\Models\Hole::whereIn('id', $holeIds)->update([
                    'status' => 'kosong',
                    'plant_name' => null, 
                    'planted_at' => null
                ]);

                $totalDamage += $qty;
                
                \App\Models\MaintenanceLog::create([
                    'loggable_type' => 'App\Models\Rack',
                    'loggable_id' => $rackId,
                    'user_id' => auth()->id() ?? 1,
                    'action_type' => 'rusak',
                    'notes' => "Dilaporkan rusak $qty tanaman: $reason",
                    'details' => json_encode(['jumlah' => $qty, 'alasan' => $reason])
                ]);
            }
            \DB::commit();
            return response()->json(['success' => true, 'message' => "Berhasil melaporkan $totalDamage tanaman rusak."]);
        } catch (\Exception $e) {
            \DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
