<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Greenhouse;
use App\Models\Rack;
use App\Models\Hole;
use App\Models\Semai;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $totalGreenhouses = Greenhouse::count();
            $totalRacks = Rack::count();
            
            $holesCount = Hole::selectRaw("
                count(*) as total,
                sum(case when status = 'ditanam' then 1 else 0 end) as ditanam,
                sum(case when status = 'kosong' then 1 else 0 end) as kosong
            ")->first();

            $logs = \App\Models\MaintenanceLog::whereMonth('created_at', now()->month)->get();
            $panenThisMonth = $logs->where('action_type', 'panen')->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });
            $rusakThisMonth = $logs->where('action_type', 'rusak')->sum(function($l) { return json_decode($l->details)->jumlah ?? 0; });

            $alertsCount = Rack::whereNotNull('ph_level')
                ->where(function($q) {
                    $q->where('ph_level', '<', 5.5)->orWhere('ph_level', '>', 6.8);
                })
                ->orWhere(function($q) {
                    $q->whereNotNull('ppm_level')
                      ->where(function($q2) {
                          $q2->where('ppm_level', '<', 500)->orWhere('ppm_level', '>', 1500);
                      });
                })
                ->count();

            $semaiAktif = Semai::where('status', 'aktif')->count();
            
            $today = Carbon::today()->toDateString();
            $semaiSiapPindah = Semai::where('status', 'aktif')
                                    ->whereNotNull('estimated_transfer_date')
                                    ->whereDate('estimated_transfer_date', '<=', $today)
                                    ->count();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully',
                'data' => [
                    'total_greenhouses' => $totalGreenhouses,
                    'total_racks' => $totalRacks,
                    'total_holes' => (int)($holesCount->total ?? 0),
                    'ditanam' => (int)($holesCount->ditanam ?? 0),
                    'kosong' => (int)($holesCount->kosong ?? 0),
                    'panen' => $panenThisMonth,
                    'rusak' => $rusakThisMonth,
                    'alerts_count' => $alertsCount,
                    'semai_aktif' => $semaiAktif,
                    'semai_siap_pindah' => $semaiSiapPindah,
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
}
