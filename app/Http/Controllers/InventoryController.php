<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\InventoryLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::query();
        if ($request->has('cat') && $request->cat != '') {
            $query->where('type', $request->cat);
        }
        $inventories = $query->orderBy('type')->orderBy('name')->get();
        $currentCat  = $request->cat ?? 'all';
        $plantTypes  = \App\Models\PlantType::orderBy('name')->get(['id', 'name']);

        return view('hydroponics.inventory', compact('inventories', 'currentCat', 'plantTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required',
            'type'     => 'required',
            'quantity' => 'required|numeric',
            'unit'     => 'required',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only('name', 'type', 'quantity', 'unit', 'description');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('inventories', 'public');
        }

        $inventory = Inventory::create($data);

        // Buat log awal barang masuk
        InventoryLog::create([
            'inventory_id' => $inventory->id,
            'type'         => 'in',
            'quantity'     => $request->quantity,
            'description'  => 'Stok awal ditambahkan',
            'user_id'      => Auth::id(),
        ]);

        return redirect()->back()->with('success', "Barang '{$request->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $inventory = Inventory::findOrFail($id);
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->only('name', 'type', 'unit', 'description');

        // Handle foto baru
        if ($request->hasFile('image')) {
            if ($inventory->image) {
                Storage::disk('public')->delete($inventory->image);
            }
            $data['image'] = $request->file('image')->store('inventories', 'public');
        }

        // Log perubahan kuantitas
        $oldQty = $inventory->quantity;
        $newQty = (float) $request->quantity;
        $data['quantity'] = $newQty;

        if ($newQty != $oldQty) {
            $diff = $newQty - $oldQty;
            InventoryLog::create([
                'inventory_id' => $inventory->id,
                'type'         => $diff > 0 ? 'in' : 'out',
                'quantity'     => abs($diff),
                'description'  => $request->log_description ?? ($diff > 0 ? 'Penambahan stok' : 'Pengurangan stok'),
                'user_id'      => Auth::id(),
            ]);
        }

        $inventory->update($data);
        return redirect()->back()->with('success', "Barang '{$inventory->name}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $inv = Inventory::findOrFail($id);
        $name = $inv->name;
        if ($inv->image) {
            Storage::disk('public')->delete($inv->image);
        }
        $inv->delete();
        return redirect()->back()->with('success', "Barang '{$name}' berhasil dihapus.");
    }

    public function logs($id)
    {
        $inventory = Inventory::with(['logs.user'])->findOrFail($id);
        return response()->json([
            'name' => $inventory->name,
            'unit' => $inventory->unit,
            'logs' => $inventory->logs->map(function ($log) {
                return [
                    'id'          => $log->id,
                    'type'        => $log->type,
                    'quantity'    => $log->quantity,
                    'description' => $log->description,
                    'user'        => $log->user ? $log->user->name : 'Sistem',
                    'date'        => $log->created_at->format('d M Y, H:i'),
                ];
            }),
        ]);
    }
}
