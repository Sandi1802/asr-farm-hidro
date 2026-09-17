<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyTask;
use Carbon\Carbon;

class DailyTaskController extends Controller
{
    private function generateTemplateIfNeeded($dateStr)
    {
        $date = Carbon::parse($dateStr)->format("Y-m-d");
        
        $templates = \App\Models\DailyTaskTemplate::all();
        $templateNames = $templates->pluck("task_name")->toArray();

        // Hapus tugas pending hari ini yang sudah tidak ada di master template
        DailyTask::whereDate("date", $date)
            ->where("is_pr", false)
            ->where("status", "pending")
            ->where("shift", "!=", "catatan")
            ->whereNotIn("task_name", $templateNames)
            ->delete();

        // Tambahkan tugas dari template yang belum ada di hari ini
        $existingTasks = DailyTask::whereDate("date", $date)
            ->where("is_pr", false)
            ->pluck("task_name")
            ->toArray();

        foreach ($templates as $t) {
            if (!in_array($t->task_name, $existingTasks)) {
                DailyTask::create([
                    "date" => $date,
                    "shift" => $t->shift,
                    "task_name" => $t->task_name,
                    "status" => "pending",
                    "is_pr" => false,
                    "created_by" => auth()->id() ?? 1
                ]);
            }
        }
    }

    public function webIndex(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $this->generateTemplateIfNeeded($date);
        return view('hydroponics.daily_tasks', compact('date'));
    }

    public function apiIndex(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $this->generateTemplateIfNeeded($date);

        $tasks = DailyTask::whereDate('date', $date)
            ->orWhere(function($q) {
                $q->where('is_pr', true)->where('status', 'pending');
            })
            ->with(['creator:id,name', 'completer:id,name'])
            ->orderBy('shift')
            ->orderBy('id')
            ->get();

        return response()->json($tasks);
    }

    public function apiToggleComplete($id)
    {
        $task = DailyTask::findOrFail($id);
        
        if ($task->status == 'pending') {
            $task->status = 'completed';
            $task->completed_by = auth()->id();
            $task->completed_at = now();
        } else {
            $task->status = 'pending';
            $task->completed_by = null;
            $task->completed_at = null;
        }
        
        $task->save();
        $task->load(['creator:id,name', 'completer:id,name']);
        
        return response()->json(['success' => true, 'task' => $task]);
    }

    public function apiUpdateNote(Request $request, $id)
    {
        $task = DailyTask::findOrFail($id);
        $task->notes = $request->notes;
        $task->save();
        
        return response()->json(['success' => true]);
    }

    public function apiStore(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'shift' => 'required|in:opening,siang,closing,pr,tambahan',
            'date' => 'required|date',
            'is_pr' => 'boolean'
        ]);

        $task = DailyTask::create([
            'date' => $request->date,
            'shift' => $request->shift,
            'task_name' => $request->task_name,
            'status' => 'pending',
            'is_pr' => $request->is_pr ?? false,
            'created_by' => auth()->id(),
            'notes' => $request->notes
        ]);

        $task->load(['creator:id,name', 'completer:id,name']);

        return response()->json(['success' => true, 'task' => $task]);
    }

    public function apiDelete($id)
    {
        $task = DailyTask::findOrFail($id);
        $task->delete();
        return response()->json(['success' => true]);
    }

    public function apiUpdateCatatan(Request $request)
    {
        $date = $request->date;
        $notes = $request->notes;

        $task = DailyTask::updateOrCreate(
            ["date" => $date, "shift" => "catatan", "task_name" => "Catatan Tambahan"],
            ["notes" => $notes, "status" => "completed", "created_by" => auth()->id()]
        );

        return response()->json(["success" => true]);
    }
}
