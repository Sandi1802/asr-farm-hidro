<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Hole;

class ReportController extends Controller
{
    public function index()
    {
        $rawActivities = Activity::with(['user', 'hole.row.rack.greenhouse'])->latest()->take(500)->get();
        
        $grouped = collect();
        foreach ($rawActivities as $act) {
            $dateMinute = $act->created_at->format('Y-m-d H:i');
            $userId = $act->user_id;
            $type = $act->type;
            
            $ghName = optional(optional(optional(optional($act->hole)->row)->rack)->greenhouse)->name ?? '-';
            $rackName = optional(optional(optional($act->hole)->row)->rack)->name ?? '-';
            $locationBase = $ghName . ' > ' . $rackName;

            $key = "{$dateMinute}_{$userId}_{$type}_{$locationBase}";
            
            if (!$grouped->has($key)) {
                $act->location_base = $locationBase;
                $act->hole_count = 1;
                $grouped->put($key, $act);
            } else {
                $existing = $grouped->get($key);
                $existing->hole_count++;
            }
        }
        $recentActivities = $grouped->values()->take(30);

        $damagedHoles = Hole::where('status', 'rusak')->count();
        $harvestedCount = Activity::where('type', 'panen')->count();
        $plantedCount   = Activity::whereIn('type', ['tanam', 'ditanam'])->count();

        return view('hydroponics.reports', compact('recentActivities', 'damagedHoles', 'harvestedCount', 'plantedCount'));
    }
}
