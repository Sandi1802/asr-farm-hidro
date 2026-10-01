<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\KonvenLahanV2;
use App\Models\KonvenKodeV2;
use App\Models\KonvenZonaV2;
use App\Models\KonvenBedenganV2;
use App\Models\KonvenLubangTanamV2;
use App\Models\KonvenTanamLogV2;
use App\Models\KonvenPerawatanV2;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class KonvensionalApiController extends Controller
{
    public function dashboard()
    {
        try {
            $totalLahan = KonvenLahanV2::count();
            $totalBedengan = KonvenBedenganV2::count();
            $totalTitik = KonvenLubangTanamV2::count();
            
            $titikKosong = KonvenLubangTanamV2::where('status', 'kosong')->count();
            $titikDitanam = KonvenLubangTanamV2::where('status', 'ditanam')->count();
            $titikPanen = KonvenLubangTanamV2::where('status', 'panen')->count();
            $titikRusak = KonvenLubangTanamV2::where('status', 'rusak')->count();

            // Total Jenis Bibit from unique comma-separated values
            $allPlants = KonvenLubangTanamV2::whereNotNull('plant_name')->pluck('plant_name');
            $uniquePlants = [];
            foreach ($allPlants as $p) {
                $parts = explode(',', $p);
                foreach ($parts as $pt) {
                    $pt = trim($pt);
                    if (!empty($pt)) $uniquePlants[$pt] = true;
                }
            }
            $totalJenisBibit = count($uniquePlants);

            // Lahan list for chart or detailed list
            $lahans = KonvenLahanV2::orderBy('nama')->get()->map(function($lahan) {
                $stats = DB::table('konven_lubang_tanams')
                    ->join('konven_bedengans', 'konven_lubang_tanams.bedengan_id', '=', 'konven_bedengans.id')
                    ->join('konven_zonas', 'konven_bedengans.zona_id', '=', 'konven_zonas.id')
                    ->join('konven_kodes', 'konven_zonas.kode_id', '=', 'konven_kodes.id')
                    ->where('konven_kodes.lahan_id', $lahan->id)
                    ->select(
                        DB::raw('COUNT(konven_lubang_tanams.id) as total_titik'),
                        DB::raw('SUM(CASE WHEN konven_lubang_tanams.status = \'ditanam\' THEN 1 ELSE 0 END) as ditanam'),
                        DB::raw('COUNT(DISTINCT konven_bedengans.id) as bedengan_count')
                    )->first();

                return [
                    'id' => $lahan->id,
                    'name' => $lahan->nama,
                    'location' => $lahan->catatan ?? '-',
                    'bedengan_count' => $stats->bedengan_count ?? 0,
                    'total_titik' => $stats->total_titik ?? 0,
                    'ditanam' => $stats->ditanam ?? 0
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => [
                        'total_lahan' => $totalLahan,
                        'total_bedengan' => $totalBedengan,
                        'total_titik' => $totalTitik,
                        'kosong' => $titikKosong,
                        'ditanam' => $titikDitanam,
                        'panen' => $titikPanen,
                        'rusak' => $titikRusak,
                        'jenis_bibit' => $totalJenisBibit
                    ],
                    'lahans' => $lahans
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat dashboard konvensional: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detailLahan($id)
    {
        $lahan = KonvenLahanV2::findOrFail($id);
        $kodes = KonvenKodeV2::where('lahan_id', $id)->withCount('zonas')->get();
        return response()->json(['success' => true, 'data' => ['lahan' => $lahan, 'kodes' => $kodes]]);
    }

    public function detailKode($id)
    {
        $kode = KonvenKodeV2::findOrFail($id);
        $zonas = KonvenZonaV2::where('kode_id', $id)->withCount('bedengans')->get();
        return response()->json(['success' => true, 'data' => ['kode' => $kode, 'zonas' => $zonas]]);
    }

    public function detailZona($id)
    {
        $zona = KonvenZonaV2::findOrFail($id);
        $bedengans = KonvenBedenganV2::where('zona_id', $id)->get()->map(function($b) {
            $total = KonvenLubangTanamV2::where('bedengan_id', $b->id)->count();
            $ditanam = KonvenLubangTanamV2::where('bedengan_id', $b->id)->where('status', 'ditanam')->count();
            return [
                'id' => $b->id,
                'nomor' => $b->nomor,
                'total_lubang' => $total,
                'ditanam' => $ditanam
            ];
        });
        return response()->json(['success' => true, 'data' => ['zona' => $zona, 'bedengans' => $bedengans]]);
    }

    public function detailBedengan($id)
    {
        $bedengan = KonvenBedenganV2::with('zona.kode.lahan')->findOrFail($id);
        $stats = [
            'total' => KonvenLubangTanamV2::where('bedengan_id', $id)->count(),
            'kosong' => KonvenLubangTanamV2::where('bedengan_id', $id)->where('status', 'kosong')->count(),
            'ditanam' => KonvenLubangTanamV2::where('bedengan_id', $id)->where('status', 'ditanam')->count(),
            'panen' => KonvenLubangTanamV2::where('bedengan_id', $id)->where('status', 'panen')->count(),
            'rusak' => KonvenLubangTanamV2::where('bedengan_id', $id)->where('status', 'rusak')->count(),
        ];
        
        $plants = KonvenLubangTanamV2::where('bedengan_id', $id)
            ->where('status', 'ditanam')
            ->whereNotNull('plant_name')
            ->pluck('plant_name');
            
        $plantCounts = [];
        foreach ($plants as $p) {
            $parts = explode(',', $p);
            foreach ($parts as $pt) {
                $pt = trim($pt);
                if (!empty($pt)) {
                    if (!isset($plantCounts[$pt])) $plantCounts[$pt] = 0;
                    $plantCounts[$pt]++;
                }
            }
        }
        
        return response()->json([
            'success' => true, 
            'data' => [
                'bedengan' => $bedengan, 
                'stats' => $stats,
                'plants' => $plantCounts
            ]
        ]);
    }

    public function tanam(Request $r, $id)
    {
        $r->validate([
            'plant_name_1' => 'required|string',
            'plant_name_2' => 'nullable|string',
            'planted_at' => 'required|date'
        ]);

        $combined = $r->plant_name_1;
        if ($r->filled('plant_name_2')) {
            $combined .= ', ' . $r->plant_name_2;
        }

        $lubangs = KonvenLubangTanamV2::where('bedengan_id', $id)->where('status', 'kosong')->get();
        if ($lubangs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Tidak ada lubang kosong.'], 400);
        }

        foreach ($lubangs as $l) {
            $l->update([
                'status' => 'ditanam',
                'plant_name' => $combined,
                'planted_at' => $r->planted_at
            ]);
            KonvenTanamLogV2::create([
                'lubang_id' => $l->id,
                'action_type' => 'tanam',
                'plant_name' => $combined,
                'created_by' => Auth::id()
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Berhasil ditanam']);
    }

    public function panen(Request $r, $id)
    {
        $r->validate(['plant_name' => 'required|string']);
        $plantName = trim($r->plant_name);
        
        $lubangs = KonvenLubangTanamV2::where('bedengan_id', $id)->where('status', 'ditanam')->get();
        
        foreach ($lubangs as $l) {
            if (!$l->plant_name) continue;
            
            $plants = array_map('trim', explode(',', $l->plant_name));
            if (($key = array_search($plantName, $plants)) !== false) {
                unset($plants[$key]);
                
                $newPlantName = implode(', ', $plants);
                $newStatus = empty($newPlantName) ? 'panen' : 'ditanam';
                
                $l->update([
                    'status' => $newStatus,
                    'plant_name' => empty($newPlantName) ? null : $newPlantName,
                    'harvested_at' => empty($newPlantName) ? now() : $l->harvested_at
                ]);
                
                KonvenTanamLogV2::create([
                    'lubang_id' => $l->id,
                    'action_type' => 'panen',
                    'plant_name' => $plantName,
                    'created_by' => Auth::id()
                ]);
            }
        }
        return response()->json(['success' => true, 'message' => "Berhasil memanen $plantName"]);
    }

    public function perawatan(Request $r, $id)
    {
        $r->validate([
            'jenis' => 'required|in:pemupukan,penyemprotan',
            'nama_bahan' => 'required|string',
            'dosis' => 'nullable|string',
            'tanggal' => 'required|date'
        ]);

        KonvenPerawatanV2::create([
            'bedengan_id' => $id,
            'jenis' => $r->jenis,
            'nama_bahan' => $r->nama_bahan,
            'dosis' => $r->dosis,
            'tanggal' => $r->tanggal,
            'keterangan' => $r->keterangan,
            'created_by' => Auth::id()
        ]);

        return response()->json(['success' => true, 'message' => 'Perawatan berhasil dicatat']);
    }

    public function laporRusak(Request $r, $id)
    {
        $lubangs = KonvenLubangTanamV2::where('bedengan_id', $id)->where('status', 'ditanam')->get();
        foreach ($lubangs as $l) {
            $l->update(['status' => 'rusak']);
            KonvenTanamLogV2::create([
                'lubang_id' => $l->id,
                'action_type' => 'rusak',
                'created_by' => Auth::id()
            ]);
        }
        return response()->json(['success' => true, 'message' => 'Kerusakan berhasil dicatat']);
    }
}
