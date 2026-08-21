<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rack;
use Carbon\Carbon;

class RackApiController extends Controller
{
    public function index()
    {
        try {
            $racks = Rack::with(['greenhouse', 'rows.holes'])->get();

            $summary = [
                'total_greenhouses' => \App\Models\Greenhouse::count(),
                'total_racks' => $racks->count(),
                'total_holes' => 0,
                'ditanam' => 0,
                'panen' => 0,
                'rusak' => 0,
                'kosong' => 0,
                'alerts_count' => 0,
            ];

            $panenLogCounts = \App\Models\MaintenanceLog::where('loggable_type', 'App\Models\Hole')
                ->where('action_type', 'panen')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->pluck('loggable_id');

            $rusakLogCounts = \App\Models\MaintenanceLog::where('loggable_type', 'App\Models\Hole')
                ->where('action_type', 'rusak')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->pluck('loggable_id');

            $racksData = $racks->map(function ($rack) use (&$summary, $panenLogCounts, $rusakLogCounts) {
                $allHoles = $rack->rows->flatMap->holes;
                $holeIds = $allHoles->pluck('id');
                
                $ditanam = $allHoles->where('status', 'ditanam')->count();
                $kosong = $allHoles->where('status', 'kosong')->count();
                
                $panen = $panenLogCounts->intersect($holeIds)->count();
                $rusak = $rusakLogCounts->intersect($holeIds)->count();

                $summary['total_holes'] += $allHoles->count();
                $summary['ditanam'] += $ditanam;
                $summary['panen'] += $panen;
                $summary['rusak'] += $rusak;
                $summary['kosong'] += $kosong;

                $hasAlert = false;
                if ($rack->ph_level !== null && ($rack->ph_level < 5.5 || $rack->ph_level > 6.8)) {
                    $hasAlert = true;
                }
                if ($rack->ppm_level !== null && ($rack->ppm_level < 500 || $rack->ppm_level > 1500)) {
                    $hasAlert = true;
                }

                if ($hasAlert) {
                    $summary['alerts_count']++;
                }

                return [
                    'id' => $rack->id,
                    'name' => $rack->name,
                    'greenhouse_name' => $rack->greenhouse->name ?? 'GH',
                    'ppm_level' => $rack->ppm_level,
                    'ph_level' => $rack->ph_level,
                    'has_alert' => $hasAlert,
                    'stats' => [
                        'total_holes' => $allHoles->count(),
                        'ditanam' => $ditanam,
                        'panen' => $panen,
                        'rusak' => $rusak,
                        'kosong' => $kosong,
                    ]
                ];
            });

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'data' => $racksData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data rak: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $rack = Rack::with(['greenhouse', 'rows.holes'])->find($id);

            if (!$rack) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rak tidak ditemukan'
                ], 404);
            }

            $allHoles = $rack->rows->flatMap->holes;
            
            // Status Counts
            $holeIds = $allHoles->pluck('id');
            $countKosong = $allHoles->where('status', 'kosong')->count();
            $totalDitanam = $allHoles->where('status', 'ditanam')->count();
            
            $countPanen = \App\Models\MaintenanceLog::where('loggable_type', 'App\Models\Hole')
                ->whereIn('loggable_id', $holeIds)
                ->where('action_type', 'panen')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
                
            $countRusak = \App\Models\MaintenanceLog::where('loggable_type', 'App\Models\Hole')
                ->whereIn('loggable_id', $holeIds)
                ->where('action_type', 'rusak')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();

            // Plants Summary (Grouping)
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
                    'rack_id' => $rack->id,
                    'rack_name' => $rack->name,
                    'greenhouse_name' => $rack->greenhouse->name ?? 'GH',
                    'ppm_level' => $rack->ppm_level,
                    'ph_level' => $rack->ph_level,
                    'last_drained_at' => $rack->last_drained_at ? Carbon::parse($rack->last_drained_at)->translatedFormat('d M Y') : 'Belum pernah dikuras',
                    'ppm_ph_updated_at' => $rack->ppm_ph_updated_at ? Carbon::parse($rack->ppm_ph_updated_at)->translatedFormat('d M Y, H:i') : 'Belum ada data',
                    'stats' => [
                        'total_holes' => $allHoles->count(),
                        'ditanam' => $totalDitanam,
                        'kosong' => $countKosong,
                        'panen' => $countPanen,
                        'rusak' => $countRusak,
                    ],
                    'plants' => $plantsArray,
                    'rows' => $rack->rows->map(function($row) {
                        return [
                            'id' => $row->id,
                            'name' => $row->name,
                            'holes' => $row->holes->map(function($hole) {
                                return [
                                    'id' => $hole->id,
                                    'status' => $hole->status,
                                    'plant_name' => $hole->plant_name,
                                    'planted_at' => $hole->planted_at ? Carbon::parse($hole->planted_at)->translatedFormat('d M Y') : null,
                                    'planted_days' => $hole->planted_at ? Carbon::parse($hole->planted_at)->diffInDays(now()) : null,
                                ];
                            })
                        ];
                    })
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail rak: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePpmPh(Request $request, $id)
    {
        try {
            $rack = Rack::find($id);
            if (!$rack) return response()->json(['success' => false, 'message' => 'Rack not found'], 404);

            $request->validate([
                'ppm_level' => 'nullable|numeric',
                'ph_level' => 'nullable|numeric'
            ]);

            $rack->update([
                'ppm_level' => $request->ppm_level,
                'ph_level' => $request->ph_level,
                'ppm_ph_updated_at' => now(),
            ]);

            \App\Models\MaintenanceLog::create([
                'loggable_type' => 'App\Models\Rack',
                'loggable_id' => $rack->id,
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'isi_ab_mix',
                'notes' => 'Update nutrisi AB Mix',
                'details' => json_encode([
                    'ppm' => $request->ppm_level,
                    'ph' => $request->ph_level
                ])
            ]);

            return response()->json(['success' => true, 'message' => 'Sensor berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update sensor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function drain(Request $request, $id)
    {
        try {
            $rack = Rack::find($id);
            if (!$rack) return response()->json(['success' => false, 'message' => 'Rack not found'], 404);

            $rack->update(['last_drained_at' => now()]);
            
            \App\Models\MaintenanceLog::create([
                'loggable_type' => 'App\Models\Rack',
                'loggable_id' => $rack->id,
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'kuras_tandon',
                'notes' => 'Melakukan kuras tandon air nutrisi',
            ]);

            return response()->json(['success' => true, 'message' => 'Air rak berhasil dikuras']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal kuras rak: ' . $e->getMessage()
            ], 500);
        }
    }

    public function plant(Request $request, $id)
    {
        try {
            $rack = Rack::with('rows.holes')->find($id);
            if (!$rack) return response()->json(['success' => false, 'message' => 'Rack not found'], 404);

            $request->validate([
                'plant_name' => 'required|string',
                'jumlah' => 'required|integer|min:1'
            ]);

            $jumlah = $request->jumlah;
            $plantName = $request->plant_name;

            // Collect all empty holes
            $emptyHoles = collect();
            foreach ($rack->rows as $row) {
                foreach ($row->holes as $hole) {
                    if ($hole->status === 'kosong') {
                        $emptyHoles->push($hole);
                    }
                }
            }

            if ($emptyHoles->count() < $jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah benih melebihi sisa lubang kosong. Sisa lubang: ' . $emptyHoles->count()
                ], 400);
            }

            // Take the first $jumlah holes and update them
            $holesToUpdate = $emptyHoles->take($jumlah);
            $holeIds = $holesToUpdate->pluck('id')->toArray();

            $plantedAt = $request->filled('planted_at') ? \Carbon\Carbon::parse($request->planted_at) : now();

            \App\Models\Hole::whereIn('id', $holeIds)->update([
                'status' => 'ditanam',
                'plant_name' => $plantName,
                'planted_at' => $plantedAt,
            ]);
            
            // Log this action
            \App\Models\MaintenanceLog::create([
                'loggable_type' => 'App\Models\Rack',
                'loggable_id' => $rack->id,
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'pindah_tanam',
                'notes' => 'Pindah tanam ' . $jumlah . ' tanaman ' . $plantName,
                'details' => json_encode(['jumlah' => $jumlah, 'plant_name' => $plantName])
            ]);

            return response()->json(['success' => true, 'message' => 'Berhasil memindahkan ' . $jumlah . ' tanaman.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal pindah tanam: ' . $e->getMessage()
            ], 500);
        }
    }

    public function harvest(Request $request, $id)
    {
        try {
            $rack = Rack::with('rows.holes')->find($id);
            if (!$rack) return response()->json(['success' => false, 'message' => 'Rack not found'], 404);

            $request->validate([
                'jumlah' => 'required|integer|min:1'
            ]);

            $jumlah = $request->jumlah;

            // Collect all planted holes
            $plantedHoles = collect();
            foreach ($rack->rows as $row) {
                foreach ($row->holes as $hole) {
                    if ($hole->status === 'ditanam') {
                        $plantedHoles->push($hole);
                    }
                }
            }

            if ($plantedHoles->count() < $jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah panen melebihi tanaman yang ada. Tersedia: ' . $plantedHoles->count()
                ], 400);
            }

            // Take the first $jumlah holes (oldest planted first)
            $holesToUpdate = $plantedHoles->sortBy('planted_at')->take($jumlah);
            $holeIds = $holesToUpdate->pluck('id')->toArray();

            \App\Models\Hole::whereIn('id', $holeIds)->update([
                'status' => 'kosong',
                'plant_name' => null,
                'planted_at' => null,
                'harvested_at' => now(),
            ]);
            
            \App\Models\MaintenanceLog::create([
                'loggable_type' => 'App\Models\Rack',
                'loggable_id' => $rack->id,
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'panen',
                'notes' => 'Panen ' . $jumlah . ' tanaman',
                'details' => json_encode(['jumlah' => $jumlah])
            ]);

            return response()->json(['success' => true, 'message' => 'Berhasil memanen ' . $jumlah . ' tanaman.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal panen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function damage(Request $request, $id)
    {
        try {
            $rack = Rack::with('rows.holes')->find($id);
            if (!$rack) return response()->json(['success' => false, 'message' => 'Rack not found'], 404);

            $request->validate([
                'jumlah' => 'required|integer|min:1',
                'alasan' => 'required|string'
            ]);

            $jumlah = $request->jumlah;
            $alasan = $request->alasan;

            // Collect all planted holes
            $plantedHoles = collect();
            foreach ($rack->rows as $row) {
                foreach ($row->holes as $hole) {
                    if ($hole->status === 'ditanam') {
                        $plantedHoles->push($hole);
                    }
                }
            }

            if ($plantedHoles->count() < $jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah rusak melebihi tanaman yang ada. Tersedia: ' . $plantedHoles->count()
                ], 400);
            }

            $holesToUpdate = $plantedHoles->take($jumlah);
            $holeIds = $holesToUpdate->pluck('id')->toArray();

            \App\Models\Hole::whereIn('id', $holeIds)->update([
                'status' => 'kosong',
                'plant_name' => null,
                'planted_at' => null,
            ]);
            
            \App\Models\MaintenanceLog::create([
                'loggable_type' => 'App\Models\Rack',
                'loggable_id' => $rack->id,
                'user_id' => auth()->id() ?? 1,
                'action_type' => 'rusak',
                'notes' => 'Lapor rusak ' . $jumlah . ' tanaman: ' . $alasan,
                'details' => json_encode(['jumlah' => $jumlah, 'alasan' => $alasan])
            ]);

            // Create Damage Note so it shows up on the Web Dashboard
            \App\Models\DamageNote::create([
                'user_id' => auth()->id() ?? 1,
                'plant_name' => 'Dilaporkan dari Mobile',
                'damage_type' => 'Lainnya',
                'description' => "Kerusakan $jumlah lubang tanam. Alasan: $alasan",
                'severity' => 'sedang',
                'location' => $rack->greenhouse->name . ' › ' . $rack->name,
                'damaged_at' => now(),
                'status' => 'open'
            ]);

            return response()->json(['success' => true, 'message' => 'Berhasil melaporkan kerusakan ' . $jumlah . ' tanaman.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal lapor rusak: ' . $e->getMessage()
            ], 500);
        }
    }
}
