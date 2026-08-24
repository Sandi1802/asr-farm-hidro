<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceLog;
use Carbon\Carbon;

class MaintenanceLogController extends Controller
{
    public function index(Request $request)
    {
        $query = MaintenanceLog::with(['user', 'loggable'])->latest();

        // Filters
        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        return view('hydroponics.maintenance-logs', compact('logs'));
    }

    public function destroyAll(Request $request)
    {
        $type = $request->input("type");

        if ($type == "panen") {
            MaintenanceLog::where("action_type", "panen")->delete();
            \App\Models\Hole::where('status', 'panen')->update([
                'status' => 'ditanam',
                'harvested_at' => null
            ]);
            \App\Models\TitikTanam::where('status', 'panen')->update([
                'status' => 'ditanam',
                'tanggal_panen' => null
            ]);
            return back()->with("success", "Seluruh Log Panen berhasil dihapus dan status dikembalikan ke 'ditanam'.");
        } elseif ($type == "tanam") {
            MaintenanceLog::whereIn("action_type", ["tanam", "pindah_tanam"])->delete();
            \App\Models\Hole::where('status', 'ditanam')->update([
                'status' => 'kosong',
                'plant_name' => null,
                'planted_at' => null,
                'harvested_at' => null
            ]);
            \App\Models\TitikTanam::where('status', 'ditanam')->update([
                'status' => 'kosong',
                'nama_tanaman' => null,
                'tanggal_tanam' => null
            ]);
            // Bersihkan juga jadwal di kalender jika ada
            \App\Models\CalendarEvent::where('title', 'like', '%Penanaman%')->delete();
            return back()->with("success", "Seluruh Log Tanam berhasil dihapus dan status lubang di-reset menjadi kosong.");
        }
        
        MaintenanceLog::truncate();
        return back()->with("success", "Seluruh Log berhasil dihapus.");
    }

    public function destroy($id)
    {
        $log = MaintenanceLog::findOrFail($id);
        $log->delete();
        return back()->with("success", "Log pemeliharaan (satu aktivitas) berhasil dihapus.");
    }
}
