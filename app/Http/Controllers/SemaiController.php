<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Semai;
use App\Models\PlantType;
use App\Models\Greenhouse;
use App\Models\Hole;
use Carbon\Carbon;

class SemaiController extends Controller
{
    public function index()
    {
        $semais        = Semai::with(['plantType', 'targetGreenhouse', 'user'])
                            ->orderByRaw("CASE status WHEN 'aktif' THEN 1 WHEN 'sudah_pindah' THEN 2 WHEN 'gagal' THEN 3 ELSE 4 END")
                            ->orderByDesc('semai_date')
                            ->get();
        $plantTypes    = PlantType::orderBy('name')->get();
        $greenhouses   = Greenhouse::orderBy('name')->get();

        // Summary stats
        $totalAktif       = $semais->where('status', 'aktif')->count();
        $totalSiapPindah  = $semais->filter(fn($s) => $s->isReadyToTransfer())->count();
        $totalSudahPindah = $semais->where('status', 'sudah_pindah')->count();
        $totalGagal       = $semais->where('status', 'gagal')->count();
        $totalBenih       = $semais->where('status', 'aktif')->sum('quantity');



        return view('hydroponics.semai.index', compact(
            'semais', 'plantTypes', 'greenhouses',
            'totalAktif', 'totalSiapPindah', 'totalSudahPindah', 'totalGagal', 'totalBenih'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'plant_name'  => 'required|string|max:100',
            'quantity'    => 'required|integer|min:1',
            'semai_date'  => 'required|date',
        ]);

        $pt = PlantType::where('name', $request->plant_name)->first();
        $semaiDays = $pt ? (int)($pt->semai_days ?? 7) : 7;

        Semai::create([
            'plant_type_id'           => $pt?->id,
            'plant_name'              => $request->plant_name,
            'quantity'                => $request->quantity,
            'semai_date'              => $request->semai_date,
            'estimated_transfer_date' => Carbon::parse($request->semai_date)->addDays($semaiDays)->toDateString(),
            'target_greenhouse_id'    => $request->target_greenhouse_id ?: null,
            'notes'                   => $request->notes,
            'status'                  => 'aktif',
            'user_id'                 => auth()->id(),
        ]);

        return redirect()->back()->with('success', "Batch semai {$request->plant_name} ({$request->quantity} lubang) berhasil dicatat.");
    }

    public function markTransferred(Request $request, $id)
    {
        $semai = Semai::findOrFail($id);
        $semai->update([
            'status'           => 'sudah_pindah',
            'transferred_date' => $request->transferred_date ?? now()->toDateString(),
        ]);
        return redirect()->back()->with('success', "Batch {$semai->plant_name} berhasil ditandai sudah pindah ke GH.");
    }

    public function markFailed($id)
    {
        $semai = Semai::findOrFail($id);
        $semai->update(['status' => 'gagal']);
        return redirect()->back()->with('success', "Batch {$semai->plant_name} ditandai gagal semai.");
    }

    public function destroy($id)
    {
        $semai = Semai::findOrFail($id);
        $name  = $semai->plant_name;
        $semai->delete();
        return redirect()->back()->with('success', "Catatan semai {$name} dihapus.");
    }
}
