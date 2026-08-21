<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lahan;
use App\Models\Bedengan;
use App\Models\TitikTanam;
use App\Models\BibitKonvensional;

class KonvensionalApiController extends Controller
{
    public function dashboard()
    {
        try {
            $totalLahan = Lahan::count();
            $totalBedengan = Bedengan::count();
            $totalTitik = TitikTanam::count();
            
            $titikKosong = TitikTanam::where('status', 'kosong')->count();
            $titikDitanam = TitikTanam::where('status', 'ditanam')->count();
            $titikPanen = TitikTanam::where('status', 'panen')->count();
            $titikRusak = TitikTanam::where('status', 'rusak')->count();

            $totalJenisBibit = BibitKonvensional::count();

            // Lahan list for chart or detailed list
            $lahans = Lahan::withCount('bedengan')->get()->map(function($lahan) {
                $titikCount = TitikTanam::whereHas('bedengan', function($q) use ($lahan) {
                    $q->where('lahan_id', $lahan->id);
                })->count();

                $ditanam = TitikTanam::whereHas('bedengan', function($q) use ($lahan) {
                    $q->where('lahan_id', $lahan->id);
                })->where('status', 'ditanam')->count();

                return [
                    'id' => $lahan->id,
                    'name' => $lahan->nama_lahan,
                    'location' => $lahan->lokasi,
                    'bedengan_count' => $lahan->bedengan_count,
                    'total_titik' => $titikCount,
                    'ditanam' => $ditanam
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
}
