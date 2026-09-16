<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaprikaGreenhouse;
use App\Models\PaprikaPlant;
use App\Models\PaprikaFertilization;
use App\Models\PaprikaSpraying;

class PaprikaController extends Controller
{
    public function dashboard(Request $request)
    {
        $totalPlants = PaprikaPlant::count();
        $totalPlanted = PaprikaPlant::where('status', 'ditanam')->count();
        $totalProses = PaprikaPlant::where('status', 'proses')->count();
        $totalPanen = PaprikaPlant::where('status', 'panen')->count();
        $totalGagal = PaprikaPlant::where('status', 'gagal')->count();
        $totalKosong = PaprikaPlant::where('status', 'kosong')->count();

        return view('paprika.dashboard', compact(
            'totalPlants', 'totalPlanted', 'totalProses', 'totalPanen', 'totalGagal', 'totalKosong'
        ));
    }

    public function greenhouses()
    {
        $greenhouses = PaprikaGreenhouse::withCount([
            'plants',
            'plants as ditanam_count' => function ($query) {
                $query->where('status', 'ditanam');
            },
            'plants as panen_count' => function ($query) {
                $query->where('status', 'panen');
            }
        ])->get();

        return view('paprika.greenhouses', compact('greenhouses'));
    }

    public function greenhouseDetail($id)
    {
        $greenhouse = PaprikaGreenhouse::with('plants')->findOrFail($id);
        return view('paprika.greenhouse_detail', compact('greenhouse'));
    }

    public function bulkUpdatePlants(Request $request)
    {
        $request->validate([
            'plant_ids' => 'required|array',
            'status' => 'required|in:kosong,ditanam,proses,panen,gagal'
        ]);

        $updateData = ['status' => $request->status];
        
        if ($request->status === 'ditanam') {
            $updateData['planted_at'] = now();
            $updateData['harvested_at'] = null;
        } elseif ($request->status === 'panen') {
            $updateData['harvested_at'] = now();
        } elseif ($request->status === 'kosong') {
            $updateData['planted_at'] = null;
            $updateData['harvested_at'] = null;
        }

        PaprikaPlant::whereIn('id', $request->plant_ids)->update($updateData);

        return back()->with('success', 'Plants updated successfully.');
    }

    public function pemupukan()
    {
        $fertilizations = PaprikaFertilization::with('greenhouse')->latest()->get();
        $greenhouses = PaprikaGreenhouse::all();
        return view('paprika.pemupukan', compact('fertilizations', 'greenhouses'));
    }

    public function storePemupukan(Request $request)
    {
        $request->validate([
            'paprika_greenhouse_id' => 'required|exists:paprika_greenhouses,id',
            'fertilizer_name' => 'required|string',
            'dose' => 'required|string',
            'date' => 'required|date',
            'worker_name' => 'required|string',
        ]);

        PaprikaFertilization::create($request->all());

        return back()->with('success', 'Data pemupukan berhasil disimpan.');
    }

    public function penyemprotan()
    {
        $sprayings = PaprikaSpraying::with('greenhouse')->latest()->get();
        $greenhouses = PaprikaGreenhouse::all();
        return view('paprika.penyemprotan', compact('sprayings', 'greenhouses'));
    }

    public function storePenyemprotan(Request $request)
    {
        $request->validate([
            'paprika_greenhouse_id' => 'required|exists:paprika_greenhouses,id',
            'pesticide_name' => 'required|string',
            'dose' => 'required|string',
            'date' => 'required|date',
            'worker_name' => 'required|string',
        ]);

        PaprikaSpraying::create($request->all());

        return back()->with('success', 'Data penyemprotan berhasil disimpan.');
    }
}
