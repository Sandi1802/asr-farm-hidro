<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Greenhouse;
use App\Models\Rack;
use App\Models\Hole;
use App\Models\MaintenanceLog;

class MaintenanceLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'loggable_type' => 'required|string|in:Greenhouse,Rack',
            'loggable_id' => 'required|integer',
            'action_type' => 'required|string',
            'notes' => 'nullable|string',
            'details' => 'nullable|array',
        ]);

        $modelClass = $request->loggable_type === 'Greenhouse' ? Greenhouse::class : Rack::class;
        $model = $modelClass::find($request->loggable_id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'message' => $request->loggable_type . ' tidak ditemukan.'
            ], 404);
        }

        // Create log
        $log = MaintenanceLog::create([
            'user_id' => auth()->id(),
            'loggable_type' => $modelClass,
            'loggable_id' => $model->id,
            'action_type' => $request->action_type,
            'notes' => $request->notes,
            'details' => $request->details,
        ]);

        // Process details auto-updates for Rack
        if ($request->loggable_type === 'Rack' && is_array($request->details)) {
            // Update PPM and pH if provided
            if (isset($request->details['ppm_level']) || isset($request->details['ph_level'])) {
                $model->update([
                    'ppm_level' => $request->details['ppm_level'] ?? $model->ppm_level,
                    'ph_level' => $request->details['ph_level'] ?? $model->ph_level,
                    'ppm_ph_updated_at' => now(),
                ]);
            }
            
            // Mark as drained if checkbox checked
            if (isset($request->details['drained']) && $request->details['drained'] == true) {
                $model->update(['last_drained_at' => now()]);
            }

            // Process damaged holes
            if (isset($request->details['damaged_holes_count']) && $request->details['damaged_holes_count'] > 0) {
                $count = (int) $request->details['damaged_holes_count'];
                
                // Find N holes that are currently 'ditanam' in this rack
                $rowIds = $model->rows->pluck('id');
                $ditanamHoles = Hole::whereIn('row_id', $rowIds)
                                    ->where('status', 'ditanam')
                                    ->limit($count)
                                    ->get();

                foreach ($ditanamHoles as $hole) {
                    $hole->update([
                        'status' => 'rusak',
                    ]);
                }
            }
        }

        // Process details auto-updates for Greenhouse
        if ($request->loggable_type === 'Greenhouse' && is_array($request->details)) {
            if (isset($request->details['sprayed']) && $request->details['sprayed'] == true) {
                $model->update(['last_sprayed_at' => now()]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Catatan perawatan berhasil disimpan.',
            'data' => $log
        ]);
    }

    public function index(Request $request)
    {
        $logs = MaintenanceLog::with(['user', 'loggable'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get()
            ->map(function($log) {
                // Formatting for display in mobile
                $targetName = 'Unknown';
                if ($log->loggable) {
                    $targetName = $log->loggable_type === Greenhouse::class 
                        ? 'GH ' . $log->loggable->name 
                        : 'Rak ' . $log->loggable->name;
                }
                return [
                    'id' => $log->id,
                    'target' => $targetName,
                    'action' => $log->action_type,
                    'notes' => $log->notes,
                    'details' => $log->details,
                    'created_at' => $log->created_at->format('d M Y, H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
}
