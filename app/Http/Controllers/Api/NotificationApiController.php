<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hole;
use App\Models\Rack;
use App\Models\Semai;
use Carbon\Carbon;

/**
 * Versi JSON dari lonceng notifikasi web (HydroponicController::getNotifications)
 * + peringatan pH/PPM dan semai siap pindah.
 */
class NotificationApiController extends Controller
{
    public const PH_MIN = 5.5;
    public const PH_MAX = 6.8;
    public const PPM_MIN = 500;
    public const PPM_MAX = 1500;

    public function index()
    {
        try {
            $siapPanen = self::siapPanenGroups();
            $alerts    = self::rackAlerts();

            $semai = Semai::where('status', 'aktif')
                ->whereNotNull('estimated_transfer_date')
                ->whereDate('estimated_transfer_date', '<=', Carbon::today()->toDateString())
                ->with('targetGreenhouse:id,name')
                ->orderBy('estimated_transfer_date')
                ->get()
                ->map(fn ($s) => [
                    'id'         => $s->id,
                    'plant'      => $s->plant_name,
                    'quantity'   => (int) $s->quantity,
                    'semai_date' => optional($s->semai_date)->toDateString(),
                    'target_gh'  => optional($s->targetGreenhouse)->name,
                    'days_old'   => $s->daysOld(),
                ])->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'total'       => $siapPanen->count() + $alerts->count() + $semai->count(),
                    'siap_panen'  => $siapPanen,
                    'alerts'      => $alerts,
                    'semai_siap'  => $semai,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /** Tanaman siap panen dikelompokkan per jenis (logika sama dengan web). */
    public static function siapPanenGroups()
    {
        $defaultDays = 30;

        $readyHoles = Hole::with(['row.rack.greenhouse'])
            ->leftJoin('plant_types', 'holes.plant_name', '=', 'plant_types.name')
            ->where('holes.status', 'ditanam')
            ->whereNotNull('holes.planted_at')
            ->whereRaw("DATE(holes.planted_at + ((COALESCE(plant_types.growth_days, ?) - COALESCE(plant_types.semai_days, 0)) * INTERVAL '1 day')) <= CURRENT_DATE", [$defaultDays])
            ->select('holes.*')
            ->get();

        return $readyHoles->map(function ($hole) {
            $rack = optional($hole->row)->rack;
            $name = $hole->plant_name ?? 'Unknown';
            if (strtolower(trim($name)) === 'caisim/sawi') $name = 'Caisim';
            return [
                'plant'     => $name,
                'rack_id'   => optional($rack)->id,
                'rack_name' => optional($rack)->name ?? 'Rak',
                'gh_id'     => optional(optional($rack)->greenhouse)->id,
                'gh_name'   => optional(optional($rack)->greenhouse)->name ?? 'GH',
                'age_days'  => $hole->planted_at ? Carbon::parse($hole->planted_at)->startOfDay()->diffInDays(Carbon::today()) : 0,
            ];
        })->groupBy('plant')->map(function ($items, $plant) {
            $locations = collect($items)->groupBy('rack_id')->map(function ($rows) {
                $first = $rows->first();
                return [
                    'rack_id'   => $first['rack_id'],
                    'rack_name' => $first['rack_name'],
                    'gh_id'     => $first['gh_id'],
                    'gh_name'   => $first['gh_name'],
                    'count'     => $rows->count(),
                    'max_age'   => $rows->max('age_days'),
                ];
            })->values();

            return [
                'plant'     => $plant,
                'count'     => count($items),
                'locations' => $locations,
            ];
        })->values();
    }

    /** Rak dengan pH / PPM di luar batas ideal. */
    public static function rackAlerts()
    {
        return Rack::with('greenhouse:id,name')
            ->where(function ($q) {
                $q->where(function ($q1) {
                    $q1->whereNotNull('ph_level')
                       ->where(fn ($q2) => $q2->where('ph_level', '<', self::PH_MIN)->orWhere('ph_level', '>', self::PH_MAX));
                })->orWhere(function ($q1) {
                    $q1->whereNotNull('ppm_level')
                       ->where(fn ($q2) => $q2->where('ppm_level', '<', self::PPM_MIN)->orWhere('ppm_level', '>', self::PPM_MAX));
                });
            })
            ->orderBy('greenhouse_id')
            ->get()
            ->map(function ($rack) {
                $issues = [];
                $ph  = $rack->ph_level !== null ? (float) $rack->ph_level : null;
                $ppm = $rack->ppm_level !== null ? (float) $rack->ppm_level : null;
                if ($ph !== null && $ph < self::PH_MIN)  $issues[] = 'pH Rendah';
                if ($ph !== null && $ph > self::PH_MAX)  $issues[] = 'pH Tinggi';
                if ($ppm !== null && $ppm < self::PPM_MIN) $issues[] = 'PPM Rendah';
                if ($ppm !== null && $ppm > self::PPM_MAX) $issues[] = 'PPM Tinggi';

                return [
                    'rack_id'    => $rack->id,
                    'rack_name'  => $rack->name,
                    'gh_id'      => $rack->greenhouse_id,
                    'gh_name'    => optional($rack->greenhouse)->name,
                    'ph'         => $ph,
                    'ppm'        => $ppm,
                    'issues'     => $issues,
                    'updated_at' => optional($rack->ppm_ph_updated_at)->toIso8601String(),
                ];
            })->values();
    }
}
