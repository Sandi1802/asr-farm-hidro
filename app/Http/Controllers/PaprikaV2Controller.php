<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaprikaGh;
use App\Models\PaprikaBaris;
use App\Models\PaprikaPot;
use App\Models\PaprikaLog;
use Carbon\Carbon;

class PaprikaV2Controller extends Controller
{
    // GH List
    public function index()
    {
        $ghs = PaprikaGh::all();
        return view('paprika.v2.index', compact('ghs'));
    }

    public function storeGh(Request $request)
    {
        $request->validate(['nama_gh' => 'required']);
        PaprikaGh::create($request->all());
        return redirect()->route('paprika.v2.index')->with('success', 'GH berhasil ditambahkan');
    }

    // Baris List in a GH
    public function baris($gh_id)
    {
        $gh = PaprikaGh::findOrFail($gh_id);
        $baris = PaprikaBaris::withCount('pots')->where('gh_id', $gh_id)->get();
        return view('paprika.v2.baris', compact('gh', 'baris'));
    }

    public function storeBaris(Request $request, $gh_id)
    {
        $request->validate([
            'nama_baris' => 'required',
            'jumlah_pot' => 'required|integer|min:1'
        ]);

        $baris = PaprikaBaris::create([
            'gh_id' => $gh_id,
            'nama_baris' => $request->nama_baris
        ]);

        // Auto create pots
        for ($i = 1; $i <= $request->jumlah_pot; $i++) {
            PaprikaPot::create([
                'baris_id' => $baris->id,
                'nomor_pot' => $i,
                'status' => 'kosong'
            ]);
        }

        return redirect()->route('paprika.v2.baris', $gh_id)->with('success', 'Baris dan Pot berhasil ditambahkan');
    }

    // Pot List in a Baris
    public function pot($baris_id)
    {
        $baris = PaprikaBaris::with('gh')->findOrFail($baris_id);
        $pots = PaprikaPot::where('baris_id', $baris_id)->get();
        return view('paprika.v2.pot', compact('baris', 'pots'));
    }

    // Tanam Massal
    public function tanamMassal(Request $request, $baris_id)
    {
        $request->validate([
            'plant_name' => 'required',
            'estimated_harvest_at' => 'required|date'
        ]);

        $pots = PaprikaPot::where('baris_id', $baris_id)->where('status', '!=', 'ditanam')->get();
        
        foreach ($pots as $pot) {
            $pot->update([
                'status' => 'ditanam',
                'plant_name' => $request->plant_name,
                'planted_at' => Carbon::now(),
                'estimated_harvest_at' => $request->estimated_harvest_at
            ]);

            PaprikaLog::create([
                'pot_id' => $pot->id,
                'action_type' => 'tanam',
                'details' => 'Tanam massal ' . $request->plant_name
            ]);
        }

        return redirect()->route('paprika.v2.pot', $baris_id)->with('success', 'Tanam massal berhasil');
    }

    // Action Pot (Panen, Perawatan, Rusak)
    public function destroyGh($id)
    {
        PaprikaGh::findOrFail($id)->delete();
        return back()->with('success', 'GH berhasil dihapus');
    }

    public function destroyBaris($id)
    {
        PaprikaBaris::findOrFail($id)->delete();
        return back()->with('success', 'Baris beserta Pot berhasil dihapus');
    }

    public function destroyPot($id)
    {
        PaprikaPot::findOrFail($id)->delete();
        return back()->with('success', 'Pot berhasil dihapus');
    }

    public function actionPot(Request $request, $pot_id)
    {
        $pot = PaprikaPot::findOrFail($pot_id);
        $action = $request->action_type;
        
        $logDetails = $request->details ?? $action;

        if ($action == 'panen') {
            $pot->update(['status' => 'panen']);
        } elseif ($action == 'rusak') {
            $pot->update(['status' => 'rusak']);
        }

        PaprikaLog::create([
            'pot_id' => $pot->id,
            'action_type' => $action,
            'details' => $logDetails
        ]);

        return back()->with('success', 'Aksi ' . $action . ' berhasil dicatat');
    }
}
