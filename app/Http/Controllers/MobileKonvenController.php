<?php

namespace App\Http\Controllers;

use App\Models\KonvenLahanV2;
use App\Models\KonvenKodeV2;
use App\Models\KonvenZonaV2;
use App\Models\KonvenBedenganV2;
use App\Models\KonvenLubangTanamV2;
use App\Models\KonvenPerawatanV2;
use App\Models\KonvenTanamLogV2;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MobileKonvenController extends Controller
{
    public function index()
    {
        $lahans = KonvenLahanV2::withCount('kodes')->get();
        return view('mobile.konven.index', compact('lahans'));
    }

    public function lahan($id)
    {
        $lahan = KonvenLahanV2::findOrFail($id);
        $kodes = KonvenKodeV2::where('lahan_id', $id)->withCount('zonas')->get();
        return view('mobile.konven.lahan', compact('lahan', 'kodes'));
    }

    public function kode($id)
    {
        $kode = KonvenKodeV2::findOrFail($id);
        $zonas = KonvenZonaV2::where('kode_id', $id)->withCount('bedengans')->get();
        return view('mobile.konven.kode', compact('kode', 'zonas'));
    }

    public function zona($id)
    {
        $zona = KonvenZonaV2::findOrFail($id);
        // Load bedengans with lubang tanam summary
        $bedengans = KonvenBedenganV2::where('zona_id', $id)
            ->withCount([
                'lubangTanams as total_lubang',
                'lubangTanams as lubang_kosong' => function ($query) { $query->where('status', 'kosong'); },
                'lubangTanams as lubang_ditanam' => function ($query) { $query->where('status', 'ditanam'); },
                'lubangTanams as lubang_rusak' => function ($query) { $query->where('status', 'rusak'); },
            ])
            ->get();
            
        return view('mobile.konven.zona', compact('zona', 'bedengans'));
    }

    public function bedengan($id)
    {
        $bedengan = KonvenBedenganV2::with('zona.kode.lahan')->findOrFail($id);
        
        $lubangs = KonvenLubangTanamV2::where('bedengan_id', $id)->orderBy('nomor_lubang')->get();
        
        $stats = [
            'total' => $lubangs->count(),
            'kosong' => $lubangs->where('status', 'kosong')->count(),
            'ditanam' => $lubangs->where('status', 'ditanam')->count(),
            'rusak' => $lubangs->where('status', 'rusak')->count(),
        ];

        return view('mobile.konven.bedengan', compact('bedengan', 'lubangs', 'stats'));
    }

    public function tanamMassal(Request $request, $id)
    {
        $request->validate([
            'plant_name_1' => 'required|string',
            'plant_name_2' => 'nullable|string',
            'estimated_harvest_at' => 'nullable|date'
        ]);

        $bedengan = KonvenBedenganV2::findOrFail($id);
        $plantName = $request->plant_name_1;
        if (!empty($request->plant_name_2)) {
            $plantName .= ', ' . $request->plant_name_2;
        }

        DB::transaction(function () use ($bedengan, $plantName, $request) {
            $lubangs = KonvenLubangTanamV2::where('bedengan_id', $bedengan->id)
                                          ->where('status', '!=', 'ditanam') // Hanya tanam di yg tidak sedang ditanam (bisa kosong, rusak, panen)
                                          ->get();

            foreach ($lubangs as $lubang) {
                $lubang->update([
                    'status' => 'ditanam',
                    'plant_name' => $plantName,
                    'planted_at' => Carbon::now(),
                    'estimated_harvest_at' => $request->estimated_harvest_at,
                    'harvested_at' => null
                ]);

                KonvenTanamLogV2::create([
                    'lubang_id' => $lubang->id,
                    'action_type' => 'tanam',
                    'plant_name' => $plantName,
                    'details' => json_encode(['method' => 'massal']),
                    'created_by' => auth()->id()
                ]);
            }
        });

        return back()->with('success', 'Berhasil tanam massal!');
    }

    public function panen(Request $request, $id)
    {
        $bedengan = KonvenBedenganV2::findOrFail($id);

        DB::transaction(function () use ($bedengan) {
            $lubangs = KonvenLubangTanamV2::where('bedengan_id', $bedengan->id)
                                          ->where('status', 'ditanam')
                                          ->get();

            foreach ($lubangs as $lubang) {
                $oldPlant = $lubang->plant_name;
                $lubang->update([
                    'status' => 'kosong',
                    'harvested_at' => Carbon::now()
                ]);

                KonvenTanamLogV2::create([
                    'lubang_id' => $lubang->id,
                    'action_type' => 'panen',
                    'plant_name' => $oldPlant,
                    'details' => json_encode(['method' => 'massal']),
                    'created_by' => auth()->id()
                ]);
            }
        });

        return back()->with('success', 'Berhasil panen massal!');
    }

    public function perawatan(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|in:pemupukan,penyemprotan',
            'nama_bahan' => 'required|string',
            'dosis' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        KonvenPerawatanV2::create([
            'bedengan_id' => $id,
            'jenis' => $request->jenis,
            'nama_bahan' => $request->nama_bahan,
            'dosis' => $request->dosis,
            'tanggal' => Carbon::now(),
            'keterangan' => $request->keterangan,
            'created_by' => auth()->id()
        ]);

        return back()->with('success', ucfirst($request->jenis) . ' berhasil dicatat!');
    }

    public function kerusakan(Request $request, $id)
    {
        $bedengan = KonvenBedenganV2::findOrFail($id);
        
        // This marks ALL unharvested/undamaged plants as rusak, or just mark specific? 
        // User said: "Tombol Aksi: Tanam Massal, Panen, Pemupukan, Penyemprotan, Kerusakan" 
        // Maybe it marks all 'ditanam' or 'kosong' as rusak for the whole bedengan?
        // Let's make it mass rusak for now, or allow selecting holes in the view if they want.
        // If it's a simple button: Mark all 'ditanam' as rusak. Or perhaps they need to select lubang?
        // For simplicity, we'll mark all as rusak. Wait, maybe just mark all 'ditanam' as rusak.
        
        DB::transaction(function () use ($bedengan) {
            $lubangs = KonvenLubangTanamV2::where('bedengan_id', $bedengan->id)
                                          ->where('status', 'ditanam')
                                          ->get();

            foreach ($lubangs as $lubang) {
                $oldPlant = $lubang->plant_name;
                $lubang->update([
                    'status' => 'rusak'
                ]);

                KonvenTanamLogV2::create([
                    'lubang_id' => $lubang->id,
                    'action_type' => 'rusak',
                    'plant_name' => $oldPlant,
                    'details' => json_encode(['method' => 'massal']),
                    'created_by' => auth()->id()
                ]);
            }
        });

        return back()->with('success', 'Status bedengan ditandai rusak!');
    }
}
