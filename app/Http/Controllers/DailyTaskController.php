<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyTask;
use Carbon\Carbon;

class DailyTaskController extends Controller
{
    private $template = [
        'opening' => [
            'Pintu/akses kebun aman',
            'Area umum tidak banjir/genangan',
            'Pompa utama menyala normal',
            'Aliran air semua jalur aktif',
            'Volume tandon cukup',
            'pH larutan dicatat',
            'EC/PPM larutan dicatat',
            'Alat ukur siap pakai',
            'Tanaman layu/abnormal dicek',
            'Tanda hama dicek',
            'Area semai dicek',
            'Order hari ini dicek',
            'Stok kemasan dicek',
            'Area packing bersih',
            'Briefing singkat dilakukan'
        ],
        'siang' => [
            'Tanaman tidak stres panas berlebihan',
            'Aliran air tetap rata',
            'Area kerja tetap bersih',
            'Panen sesuai order',
            'Sortasi dilakukan',
            'Packing tidak telat',
            'Komunikasi pelanggan aktif'
        ],
        'closing' => [
            'Sisa panen ditangani',
            'Area packing dibersihkan',
            'Sampah organik dibuang/diolah',
            'Pompa dan aliran akhir dicek',
            'Volume tandon cukup untuk malam',
            'Panel listrik/stop kontak aman',
            'Alat dikembalikan',
            'Alat ukur dibersihkan',
            'Stok kritis dicatat',
            'Kas kecil/struk hari ini aman',
            'Foto closing diambil',
            'Pintu/gembok ditutup',
            'Serah terima shift dibuat'
        ]
    ];

    private function generateTemplateIfNeeded($dateStr)
    {
        $date = Carbon::parse($dateStr)->format('Y-m-d');
        
        $count = DailyTask::whereDate('date', $date)->where('is_pr', false)->count();
        
        if ($count == 0) {
            foreach ($this->template as $shift => $tasks) {
                foreach ($tasks as $taskName) {
                    DailyTask::create([
                        'date' => $date,
                        'shift' => $shift,
                        'task_name' => $taskName,
                        'status' => 'pending',
                        'is_pr' => false,
                        'created_by' => auth()->id() ?? 1
                    ]);
                }
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
            'shift' => 'required|in:opening,siang,closing,pr',
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
}
