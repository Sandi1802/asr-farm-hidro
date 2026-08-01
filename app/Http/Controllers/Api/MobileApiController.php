<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Rack;

class MobileApiController extends Controller
{
    /**
     * API Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Revoke all old tokens to keep it simple, or just create new one
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token
            ]
        ], 200);
    }

    /**
     * API Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * API Get GH Detail (Scan QR)
     */
    public function getGHDetail($id)
    {
        $gh = \App\Models\Greenhouse::with(['racks.rows.holes'])->find($id);

        if (!$gh) {
            return response()->json([
                'success' => false,
                'message' => 'Greenhouse tidak ditemukan'
            ], 404);
        }

        $allHoles = $gh->racks->flatMap->rows->flatMap->holes;
        
        $countKosong = $allHoles->where('status', 'kosong')->count();
        $countPanen = $allHoles->where('status', 'panen')->count();
        $countRusak = $allHoles->where('status', 'rusak')->count();
        $totalDitanam = $allHoles->where('status', 'ditanam')->count();

        $plantedPlants = $allHoles->where('status', 'ditanam')
            ->filter(fn($h) => !empty($h->plant_name))
            ->groupBy('plant_name')
            ->map(fn($group) => $group->count());

        $plantsArray = [];
        foreach ($plantedPlants as $name => $count) {
            $plantsArray[] = [
                'name' => $name,
                'count' => $count
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $gh->id,
                'name' => $gh->name,
                'last_sprayed_at' => $gh->last_sprayed_at ? $gh->last_sprayed_at->translatedFormat('d M Y') : 'Belum pernah dicatat',
                'racks_count' => $gh->racks->count(),
                'stats' => [
                    'total_holes' => $allHoles->count(),
                    'ditanam' => $totalDitanam,
                    'kosong' => $countKosong,
                    'panen' => $countPanen,
                    'rusak' => $countRusak,
                ],
                'plants' => $plantsArray
            ]
        ], 200);
    }

    /**
     * API Get Rack Detail (Scan QR)
     */
    public function getRackDetail($id)
    {
        $rack = Rack::with(['greenhouse', 'rows.holes'])->find($id);

        if (!$rack) {
            return response()->json([
                'success' => false,
                'message' => 'Rak tidak ditemukan'
            ], 404);
        }

        $allHoles = $rack->rows->flatMap->holes;
        
        // Status Counts
        $countKosong = $allHoles->where('status', 'kosong')->count();
        $countPanen = $allHoles->where('status', 'panen')->count();
        $countRusak = $allHoles->where('status', 'rusak')->count();
        $totalDitanam = $allHoles->where('status', 'ditanam')->count();

        // Plants Summary (Grouping)
        $plantedPlants = $allHoles->where('status', 'ditanam')
            ->filter(fn($h) => !empty($h->plant_name))
            ->groupBy('plant_name');

        $plantsArray = [];
        foreach ($plantedPlants as $name => $group) {
            // Get the oldest plant in this group
            $oldestHole = $group->filter(fn($h) => $h->planted_at != null)->sortBy('planted_at')->first();
            $ageDays = $oldestHole ? \Carbon\Carbon::parse($oldestHole->planted_at)->diffInDays(now()) : 0;
            
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
                'last_drained_at' => $rack->last_drained_at ? $rack->last_drained_at->translatedFormat('d M Y') : 'Belum pernah dikuras',
                'ppm_ph_updated_at' => $rack->ppm_ph_updated_at ? $rack->ppm_ph_updated_at->translatedFormat('d M Y, H:i') : 'Belum ada data',
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
                                'planted_at' => $hole->planted_at ? $hole->planted_at->translatedFormat('d M Y') : null,
                                'planted_days' => $hole->planted_at ? \Carbon\Carbon::parse($hole->planted_at)->diffInDays(now()) : null,
                            ];
                        })
                    ];
                })
            ]
        ], 200);
    }

    public function updatePpmPh(Request $request, $id)
    {
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

        return response()->json(['success' => true, 'message' => 'Sensor berhasil diupdate']);
    }

    public function drainRack(Request $request, $id)
    {
        $rack = Rack::find($id);
        if (!$rack) return response()->json(['success' => false, 'message' => 'Rack not found'], 404);

        $rack->update(['last_drained_at' => now()]);
        return response()->json(['success' => true, 'message' => 'Air rak berhasil dikuras']);
    }

    public function getAllRacks()
    {
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

        $racksData = $racks->map(function ($rack) use (&$summary) {
            $allHoles = $rack->rows->flatMap->holes;
            $ditanam = $allHoles->where('status', 'ditanam')->count();
            $panen = $allHoles->where('status', 'panen')->count();
            $rusak = $allHoles->where('status', 'rusak')->count();
            $kosong = $allHoles->where('status', 'kosong')->count();

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
    }

    public function updateHole(Request $request, $id)
    {
        $hole = \App\Models\Hole::find($id);
        if (!$hole) return response()->json(['success' => false, 'message' => 'Hole not found'], 404);

        $request->validate([
            'status' => 'required|in:kosong,ditanam,panen,rusak',
            'plant_name' => 'nullable|string',
            'planted_at' => 'nullable|date'
        ]);

        $data = ['status' => $request->status];
        
        if ($request->status === 'kosong') {
            $data['plant_name'] = null;
            $data['planted_at'] = null;
            $data['harvested_at'] = null;
        } elseif ($request->status === 'ditanam') {
            $data['plant_name'] = $request->plant_name;
            $data['planted_at'] = $request->filled('planted_at') ? $request->planted_at : ($hole->planted_at ?? now());
        } elseif ($request->status === 'panen') {
            if (!$hole->harvested_at) $data['harvested_at'] = now();
        }

        $hole->update($data);

        return response()->json(['success' => true, 'message' => 'Status lubang berhasil diupdate']);
    }

    public function bulkUpdateHoles(Request $request)
    {
        $request->validate([
            'hole_ids'   => 'required|array',
            'hole_ids.*' => 'integer|exists:holes,id',
            'status'     => 'required|in:kosong,ditanam,panen,rusak',
            'plant_name' => 'nullable|string',
            'planted_at' => 'nullable|date'
        ]);

        $holes = \App\Models\Hole::whereIn('id', $request->hole_ids)->get();
        $now = now();
        $customPlantedAt = $request->filled('planted_at') ? $request->planted_at : null;

        foreach ($holes as $hole) {
            $st = $request->status;

            if ($st == 'kosong') {
                $hole->status = 'kosong';
                $hole->plant_name = null;
                $hole->planted_at = null;
                $hole->harvested_at = null;
            } elseif ($st == 'ditanam') {
                $hole->status = 'ditanam';
                $hole->planted_at = $customPlantedAt ?? ($hole->planted_at ?? $now);
                $hole->harvested_at = null;
                if ($request->filled('plant_name')) {
                    $hole->plant_name = $request->plant_name;
                }
            } elseif ($st == 'panen') {
                $hole->status = 'panen';
                $hole->harvested_at = $hole->harvested_at ?? $now;
            } elseif ($st == 'rusak') {
                $hole->status = 'rusak';
            }
            $hole->save();
        }

        return response()->json(['success' => true, 'message' => count($request->hole_ids) . ' lubang berhasil diupdate']);
    }
}
