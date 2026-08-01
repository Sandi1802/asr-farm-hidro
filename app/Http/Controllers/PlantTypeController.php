<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlantType;

class PlantTypeController extends Controller
{
    public function index()
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Akses ditolak.');
        $plantTypes = PlantType::orderBy('name')->get();
        return view('hydroponics.plants', compact('plantTypes'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Akses ditolak.');
        $request->validate([
            'name'         => 'required|string|max:100|unique:plant_types,name',
            'semai_days'   => 'required|integer|min:1|max:365',
            'tanam_days'   => 'required|integer|min:1|max:365',
            'remaja_days'  => 'required|integer|min:1|max:365',
            'dewasa_days'  => 'required|integer|min:1|max:365',
            'semai_ppm'    => 'nullable|integer|min:0',
            'semai_ph'     => 'nullable|numeric|between:0,14',
            'tanam_ppm'    => 'nullable|integer|min:0',
            'tanam_ph'     => 'nullable|numeric|between:0,14',
            'remaja_ppm'   => 'nullable|integer|min:0',
            'remaja_ph'    => 'nullable|numeric|between:0,14',
            'dewasa_ppm'   => 'nullable|integer|min:0',
            'dewasa_ph'    => 'nullable|numeric|between:0,14',
            'description'  => 'nullable|string|max:255',
        ]);

        $totalDays = $request->semai_days + $request->tanam_days + $request->remaja_days + $request->dewasa_days;

        PlantType::create([
            'name'         => $request->name,
            'growth_days'  => $totalDays,
            'semai_days'   => $request->semai_days,
            'tanam_days'   => $request->tanam_days,
            'remaja_days'  => $request->remaja_days,
            'dewasa_days'  => $request->dewasa_days,
            'semai_ppm'    => $request->semai_ppm,
            'semai_ph'     => $request->semai_ph,
            'tanam_ppm'    => $request->tanam_ppm,
            'tanam_ph'     => $request->tanam_ph,
            'remaja_ppm'   => $request->remaja_ppm,
            'remaja_ph'    => $request->remaja_ph,
            'dewasa_ppm'   => $request->dewasa_ppm,
            'dewasa_ph'    => $request->dewasa_ph,
            'description'  => $request->description,
        ]);

        return redirect()->back()->with('success', "Jenis tanaman \"{$request->name}\" berhasil ditambahkan (total {$totalDays} hari).");
    }

    public function update(Request $request, $id)
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Akses ditolak.');
        $plant = PlantType::findOrFail($id);
        $request->validate([
            'name'         => 'required|string|max:100|unique:plant_types,name,' . $id,
            'semai_days'   => 'required|integer|min:1|max:365',
            'tanam_days'   => 'required|integer|min:1|max:365',
            'remaja_days'  => 'required|integer|min:1|max:365',
            'dewasa_days'  => 'required|integer|min:1|max:365',
            'semai_ppm'    => 'nullable|integer|min:0',
            'semai_ph'     => 'nullable|numeric|between:0,14',
            'tanam_ppm'    => 'nullable|integer|min:0',
            'tanam_ph'     => 'nullable|numeric|between:0,14',
            'remaja_ppm'   => 'nullable|integer|min:0',
            'remaja_ph'    => 'nullable|numeric|between:0,14',
            'dewasa_ppm'   => 'nullable|integer|min:0',
            'dewasa_ph'    => 'nullable|numeric|between:0,14',
            'description'  => 'nullable|string|max:255',
        ]);

        $totalDays = $request->semai_days + $request->tanam_days + $request->remaja_days + $request->dewasa_days;

        $plant->update([
            'name'         => $request->name,
            'growth_days'  => $totalDays,
            'semai_days'   => $request->semai_days,
            'tanam_days'   => $request->tanam_days,
            'remaja_days'  => $request->remaja_days,
            'dewasa_days'  => $request->dewasa_days,
            'semai_ppm'    => $request->semai_ppm,
            'semai_ph'     => $request->semai_ph,
            'tanam_ppm'    => $request->tanam_ppm,
            'tanam_ph'     => $request->tanam_ph,
            'remaja_ppm'   => $request->remaja_ppm,
            'remaja_ph'    => $request->remaja_ph,
            'dewasa_ppm'   => $request->dewasa_ppm,
            'dewasa_ph'    => $request->dewasa_ph,
            'description'  => $request->description,
        ]);

        return redirect()->back()->with('success', "Jenis tanaman \"{$plant->name}\" berhasil diperbarui (total {$totalDays} hari).");
    }

    public function destroy($id)
    {
        abort_if(auth()->user()->role !== 'super_admin', 403, 'Akses ditolak.');
        $plant = PlantType::findOrFail($id);
        $name  = $plant->name;
        $plant->delete();

        return redirect()->back()->with('success', "Jenis tanaman \"{$name}\" berhasil dihapus.");
    }

    /**
     * API endpoint — return all plant types as JSON (for AJAX dropdowns).
     */
    public function api()
    {
        return response()->json(PlantType::orderBy('name')->get());
    }
}
