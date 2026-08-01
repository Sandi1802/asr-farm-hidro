<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DamageNote;
use App\Models\Greenhouse;
use App\Models\Hole;
use Carbon\Carbon;

class DamageNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = DamageNote::with(['user', 'hole.row.rack.greenhouse'])->latest('damaged_at');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }
        if ($request->filled('damage_type')) {
            $query->where('damage_type', $request->damage_type);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sub) use ($q) {
                $sub->where('plant_name', 'like', "%$q%")
                    ->orWhere('description', 'like', "%$q%")
                    ->orWhere('location', 'like', "%$q%");
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
        $totalOpen     = DamageNote::where('status', 'open')->count();
        $totalHandling = DamageNote::where('status', 'handling')->count();
        $totalResolved = DamageNote::where('status', 'resolved')->count();
        $totalBerat    = DamageNote::where('severity', 'berat')->where('status', '!=', 'resolved')->count();

        // All greenhouses for location picker
        $greenhouses = Greenhouse::with('racks')->get();

        return view('hydroponics.damage-notes', compact(
            'notes', 'totalOpen', 'totalHandling', 'totalResolved', 'totalBerat', 'greenhouses'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'severity'    => 'required|in:ringan,sedang,berat',
            'damage_type' => 'required|string',
            'damaged_at'  => 'nullable|date',
        ]);

        // Auto-build location string
        $location = null;
        if ($request->filled('hole_id')) {
            $hole = Hole::with(['row.rack.greenhouse'])->find($request->hole_id);
            if ($hole) {
                $location = optional(optional($hole->row)->rack->greenhouse)->name . ' › '
                    . optional($hole->row->rack)->name . ' › '
                    . $hole->name;
            }
        } elseif ($request->filled('location_manual')) {
            $location = $request->location_manual;
        }

        DamageNote::create([
            'hole_id'     => $request->hole_id ?: null,
            'user_id'     => auth()->id(),
            'plant_name'  => $request->plant_name,
            'damage_type' => $request->damage_type,
            'description' => $request->description,
            'severity'    => $request->severity,
            'location'    => $location,
            'damaged_at'  => $request->filled('damaged_at') ? Carbon::parse($request->damaged_at) : now(),
            'action_taken'=> $request->action_taken,
            'status'      => 'open',
        ]);

        return redirect()->back()->with('success', 'Catatan kerusakan berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $note = DamageNote::findOrFail($id);
        $request->validate([
            'status'       => 'required|in:open,handling,resolved',
            'action_taken' => 'nullable|string',
        ]);

        $note->update([
            'status'       => $request->status,
            'action_taken' => $request->action_taken,
        ]);

        return redirect()->back()->with('success', 'Status catatan kerusakan diperbarui.');
    }

    public function destroy($id)
    {
        DamageNote::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Catatan kerusakan dihapus.');
    }
}
