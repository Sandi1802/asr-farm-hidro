<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DamageNote;
use Carbon\Carbon;

class DamageApiController extends Controller
{
    public function index()
    {
        try {
            $notes = DamageNote::with(['hole', 'user'])->orderBy('created_at', 'desc')->get();

            $data = $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'hole_id' => $note->hole_id,
                    'plant_name' => $note->plant_name,
                    'damage_type' => $note->damage_type,
                    'description' => $note->description,
                    'severity' => $note->severity,
                    'location' => $note->location,
                    'damaged_at' => $note->damaged_at ? Carbon::parse($note->damaged_at)->format('Y-m-d H:i') : null,
                    'action_taken' => $note->action_taken,
                    'status' => $note->status,
                    'user' => $note->user ? $note->user->name : null,
                    'created_at' => $note->created_at->format('Y-m-d H:i')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat catatan kerusakan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'damage_type' => 'required|string',
                'severity' => 'required|in:ringan,sedang,berat',
                'hole_id' => 'nullable|exists:holes,id',
                'plant_name' => 'nullable|string',
                'description' => 'nullable|string',
                'location' => 'nullable|string',
                'damaged_at' => 'nullable|date',
                'action_taken' => 'nullable|string'
            ]);

            $note = DamageNote::create([
                'hole_id' => $request->hole_id,
                'user_id' => $request->user()->id,
                'plant_name' => $request->plant_name,
                'damage_type' => $request->damage_type,
                'description' => $request->description,
                'severity' => $request->severity,
                'location' => $request->location,
                'damaged_at' => $request->filled('damaged_at') ? $request->damaged_at : now(),
                'action_taken' => $request->action_taken,
                'status' => 'open'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Catatan kerusakan berhasil ditambahkan',
                'data' => $note
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah catatan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $note = DamageNote::find($id);
            if (!$note) return response()->json(['success' => false, 'message' => 'Catatan tidak ditemukan'], 404);

            $request->validate([
                'action_taken' => 'nullable|string',
                'status' => 'nullable|in:open,handling,resolved'
            ]);

            $note->update($request->only(['action_taken', 'status']));

            return response()->json([
                'success' => true,
                'message' => 'Catatan berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update catatan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stats()
    {
        try {
            $bySeverity = DamageNote::select('severity', \DB::raw('count(*) as total'))
                                    ->groupBy('severity')
                                    ->pluck('total', 'severity');
                                    
            $byStatus = DamageNote::select('status', \DB::raw('count(*) as total'))
                                  ->groupBy('status')
                                  ->pluck('total', 'status');

            return response()->json([
                'success' => true,
                'data' => [
                    'severity' => [
                        'ringan' => $bySeverity['ringan'] ?? 0,
                        'sedang' => $bySeverity['sedang'] ?? 0,
                        'berat' => $bySeverity['berat'] ?? 0,
                    ],
                    'status' => [
                        'open' => $byStatus['open'] ?? 0,
                        'handling' => $byStatus['handling'] ?? 0,
                        'resolved' => $byStatus['resolved'] ?? 0,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik: ' . $e->getMessage()
            ], 500);
        }
    }
}
