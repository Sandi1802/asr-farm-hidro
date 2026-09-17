<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyTaskTemplate;

class DailyTaskTemplateController extends Controller
{
    public function index()
    {
        $templates = DailyTaskTemplate::orderBy("shift")->orderBy("id")->get();
        return view("master-data.daily-tasks", compact("templates"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "shift" => "required|in:opening,siang,closing",
            "task_name" => "required|string|max:255"
        ]);

        DailyTaskTemplate::create($request->only(["shift", "task_name"]));

        return redirect()->back()->with("success", "Tugas berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            "shift" => "required|in:opening,siang,closing",
            "task_name" => "required|string|max:255"
        ]);

        $t = DailyTaskTemplate::findOrFail($id);
        $t->update($request->only(["shift", "task_name"]));

        return redirect()->back()->with("success", "Tugas berhasil diubah.");
    }

    public function destroy($id)
    {
        $t = DailyTaskTemplate::findOrFail($id);
        $t->delete();

        return redirect()->back()->with("success", "Tugas berhasil dihapus.");
    }
}

