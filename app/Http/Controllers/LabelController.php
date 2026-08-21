<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::with('parent')->orderBy('sort_order')->get();
        return view('master-data.labels', compact('labels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'parent_id' => 'nullable|exists:labels,id',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = $request->has('is_active');
        }

        Label::create($validated);

        return back()->with('success', 'Label berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'parent_id' => 'nullable|exists:labels,id',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'sometimes|boolean',
        ]);

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = $request->has('is_active');
        }

        $label = Label::findOrFail($id);
        $label->update($validated);

        return back()->with('success', 'Label berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $label = Label::findOrFail($id);
        $label->delete();

        return back()->with('success', 'Label berhasil dihapus.');
    }

    public function api()
    {
        return response()->json(Label::active()->orderBy('sort_order')->get());
    }
}
