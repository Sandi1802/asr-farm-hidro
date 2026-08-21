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
                    'plants' => $plantsArray,
                    'racks' => $gh->racks->map(function($r) {
                        $rHoles = $r->rows->flatMap->holes;
                        return [
                            'id' => $r->id,
                            'name' => $r->name,
                            'ppm_level' => $r->ppm_level,
                            'ph_level' => $r->ph_level,
                            'ditanam' => $rHoles->where('status', 'ditanam')->count(),
                            'kosong' => $rHoles->where('status', 'kosong')->count(),
                        ];
                    })
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
