<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetDamageNote;
use Carbon\Carbon;

class AssetDamageNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetDamageNote::with(['user'])->latest('damaged_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('asset_name', 'ilike', "%$q%")
                    ->orWhere('description', 'ilike', "%$q%")
                    ->orWhere('location', 'ilike', "%$q%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('damaged_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('damaged_at', '<=', $request->date_to);
        }

        $notes = $query->get();

        // Summary stats
        $totalOpen     = AssetDamageNote::where('status', 'open')->count();
        $totalHandling = AssetDamageNote::where('status', 'handling')->count();
        $totalResolved = AssetDamageNote::where('status', 'resolved')->count();
        $totalBerat    = AssetDamageNote::where('severity', 'berat')->where('status', '!=', 'resolved')->count();

        return view('hydroponics.asset-damage-notes', compact(
            'notes', 'totalOpen', 'totalHandling', 'totalResolved', 'totalBerat'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'asset_name'  => 'required|string',
            'description' => 'required|string',
            'severity'    => 'required|in:ringan,sedang,berat',
            'damaged_at'  => 'nullable|date',
        ]);

        AssetDamageNote::create([
            'user_id'     => auth()->id(),
            'asset_name'  => $request->asset_name,
            'description' => $request->description,
            'severity'    => $request->severity,
            'location'    => $request->location,
            'damaged_at'  => $request->filled('damaged_at') ? Carbon::parse($request->damaged_at) : now(),
            'action_taken'=> $request->action_taken,
            'status'      => 'open',
        ]);

        return redirect()->back()->with('success', 'Catatan kerusakan aset berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $note = AssetDamageNote::findOrFail($id);
        $request->validate([
            'status'       => 'required|in:open,handling,resolved',
            'action_taken' => 'nullable|string',
        ]);

        $note->update([
            'status'       => $request->status,
            'action_taken' => $request->action_taken,
        ]);

        return redirect()->back()->with('success', 'Status kerusakan aset diperbarui.');
    }

    public function destroy($id)
    {
        AssetDamageNote::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Catatan kerusakan aset dihapus.');
    }
}
