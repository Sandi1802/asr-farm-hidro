<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semai;
use App\Models\PlantType;
use Carbon\Carbon;

class SemaiApiController extends Controller
{
    public function index()
    {
        try {
            $semaiList = Semai::with(['plantType', 'targetGreenhouse'])->orderBy('created_at', 'desc')->get();

            $data = $semaiList->map(function ($s) {
                $today = Carbon::today();
                $semaiDate = Carbon::parse($s->semai_date);
                
                $daysOld = $semaiDate->diffInDays($today, false);
                $isReady = false;
                $remainingDays = 0;

                if ($s->estimated_transfer_date) {
                    $transferDate = Carbon::parse($s->estimated_transfer_date);
                    $remainingDays = $today->diffInDays($transferDate, false);
                    if ($remainingDays <= 0) {
                        $isReady = true;
                    }
                }

                return [
                    'id' => $s->id,
                    'plant_name' => $s->plant_name,
                    'plant_type' => $s->plantType ? $s->plantType->name : null,
                    'quantity' => $s->quantity,
                    'semai_date' => $semaiDate->format('Y-m-d'),
                    'estimated_transfer_date' => $s->estimated_transfer_date ? Carbon::parse($s->estimated_transfer_date)->format('Y-m-d') : null,
                    'target_greenhouse' => $s->targetGreenhouse ? $s->targetGreenhouse->name : null,
                    'notes' => $s->notes,
                    'status' => $s->status,
                    'transferred_date' => $s->transferred_date ? Carbon::parse($s->transferred_date)->format('Y-m-d') : null,
                    'days_old' => $daysOld > 0 ? $daysOld : 0,
                    'remaining_days' => $remainingDays,
                    'is_ready' => $isReady
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data semai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'plant_type_id' => 'required|exists:plant_types,id',
                'quantity' => 'required|integer|min:1',
                'semai_date' => 'required|date',
                'estimated_transfer_date' => 'nullable|date',
                'target_greenhouse_id' => 'nullable|exists:greenhouses,id',
                'notes' => 'nullable|string'
            ]);

            $plantType = PlantType::find($request->plant_type_id);

            $semai = Semai::create([
                'plant_type_id' => $request->plant_type_id,
                'plant_name' => $plantType->name,
                'quantity' => $request->quantity,
                'semai_date' => $request->semai_date,
                'estimated_transfer_date' => $request->estimated_transfer_date,
                'target_greenhouse_id' => $request->target_greenhouse_id,
                'notes' => $request->notes,
                'status' => 'aktif',
                'user_id' => $request->user()->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data semai berhasil ditambahkan',
                'data' => $semai
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah data semai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $semai = Semai::find($id);
            if (!$semai) return response()->json(['success' => false, 'message' => 'Semai tidak ditemukan'], 404);

            $request->validate([
                'quantity' => 'nullable|integer|min:1',
                'notes' => 'nullable|string',
                'estimated_transfer_date' => 'nullable|date',
                'target_greenhouse_id' => 'nullable|exists:greenhouses,id'
            ]);

            $semai->update($request->only(['quantity', 'notes', 'estimated_transfer_date', 'target_greenhouse_id']));

            return response()->json([
                'success' => true,
                'message' => 'Data semai berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update data semai: ' . $e->getMessage()
            ], 500);
        }
    }

    public function transfer($id)
    {
        try {
            $semai = Semai::find($id);
            if (!$semai) return response()->json(['success' => false, 'message' => 'Semai tidak ditemukan'], 404);

            $semai->update([
                'status' => 'sudah_pindah',
                'transferred_date' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semai ditandai sudah pindah tanam'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function gagal($id)
    {
        try {
            $semai = Semai::find($id);
            if (!$semai) return response()->json(['success' => false, 'message' => 'Semai tidak ditemukan'], 404);

            $semai->update([
                'status' => 'gagal'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Semai ditandai gagal'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update status: ' . $e->getMessage()
            ], 500);
        }
    }
}
