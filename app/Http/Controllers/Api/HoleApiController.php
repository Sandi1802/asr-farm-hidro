<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hole;
use App\Models\Activity;
use Carbon\Carbon;

class HoleApiController extends Controller
{
    public function update(Request $request, $id)
    {
        try {
            $hole = Hole::find($id);
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
                $data['harvested_at'] = null;
            } elseif ($request->status === 'panen') {
                if (!$hole->harvested_at) $data['harvested_at'] = now();
            } elseif ($request->status === 'rusak') {
                $data['status'] = 'kosong';
                $data['plant_name'] = null;
                $data['planted_at'] = null;
                $data['harvested_at'] = null;
            }

            $hole->update($data);

            return response()->json(['success' => true, 'message' => 'Status lubang berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update lubang: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdate(Request $request)
    {
        try {
            $request->validate([
                'hole_ids'   => 'required|array',
                'hole_ids.*' => 'integer|exists:holes,id',
                'status'     => 'required|in:kosong,ditanam,panen,rusak',
                'plant_name' => 'nullable|string',
                'planted_at' => 'nullable|date'
            ]);

            $holes = Hole::whereIn('id', $request->hole_ids)->get();
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
                    $hole->status = 'kosong';
                    $hole->plant_name = null;
                    $hole->planted_at = null;
                    $hole->harvested_at = null;
                }
                $hole->save();
            }

            return response()->json(['success' => true, 'message' => count($request->hole_ids) . ' lubang berhasil diupdate']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal bulk update lubang: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history($id)
    {
        try {
            $hole = Hole::find($id);
            if (!$hole) return response()->json(['success' => false, 'message' => 'Hole not found'], 404);

            $activities = Activity::where('hole_id', $id)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat history: ' . $e->getMessage()
            ], 500);
        }
    }
}
