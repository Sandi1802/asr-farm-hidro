<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KonvenTanamLogV2;
use App\Models\KonvenPerawatanV2;
use Illuminate\Support\Facades\DB;

class KonvenLogController extends Controller
{
    public function index(Request $request)
    {
        $dateStart = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $dateEnd = $request->input('end_date', now()->format('Y-m-d'));

        // Ambil data Tanam Log
        $tanamLogs = KonvenTanamLogV2::with(['creator', 'lubang.bedengan.zona.kode.lahan'])
            ->whereDate('created_at', '>=', $dateStart)
            ->whereDate('created_at', '<=', $dateEnd)
            ->get()
            ->map(function($log) {
                $lokasi = '';
                if ($log->lubang && $log->lubang->bedengan) {
                    $b = $log->lubang->bedengan;
                    $lokasi = $b->zona->kode->lahan->nama . ' - ' . $b->zona->kode->kode . ' - Zona ' . $b->zona->nama . ' - Bedengan ' . $b->nomor;
                }
                return [
                    'id' => 'T' . $log->id,
                    'created_at' => $log->created_at,
                    'actor' => $log->creator ? $log->creator->name : 'Sistem',
                    'action' => ucfirst($log->action_type) . ' (' . ($log->plant_name ?? '-') . ')',
                    'lokasi' => $lokasi,
                    'detail' => 'Lubang No: ' . ($log->lubang->nomor_lubang ?? '-'),
                    'type' => 'tanam'
                ];
            });

        // Ambil data Perawatan
        $perawatanLogs = KonvenPerawatanV2::with(['creator', 'bedengan.zona.kode.lahan'])
            ->whereDate('created_at', '>=', $dateStart)
            ->whereDate('created_at', '<=', $dateEnd)
            ->get()
            ->map(function($log) {
                $lokasi = '';
                if ($log->bedengan) {
                    $b = $log->bedengan;
                    $lokasi = $b->zona->kode->lahan->nama . ' - ' . $b->zona->kode->kode . ' - Zona ' . $b->zona->nama . ' - Bedengan ' . $b->nomor;
                }
                
                $detail = 'Bahan: ' . $log->nama_bahan . ' | Dosis: ' . ($log->dosis ?? '-');
                if ($log->keterangan) $detail .= ' | Ket: ' . $log->keterangan;
                
                return [
                    'id' => 'P' . $log->id,
                    'created_at' => $log->created_at,
                    'actor' => $log->creator ? $log->creator->name : 'Sistem',
                    'action' => ucfirst($log->jenis),
                    'lokasi' => $lokasi,
                    'detail' => $detail,
                    'type' => 'perawatan'
                ];
            });

        // Gabungkan dan urutkan berdasarkan created_at descending
        $allLogs = $tanamLogs->concat($perawatanLogs)->sortByDesc('created_at')->values();

        return view('konvensional.v2.logs', compact('allLogs', 'dateStart', 'dateEnd'));
    }

    public function destroy($id)
    {
        $type = substr($id, 0, 1);
        $realId = substr($id, 1);

        if ($type === 'T') {
            KonvenTanamLogV2::findOrFail($realId)->delete();
            return back()->with('success', 'Log aktivitas tanam berhasil dihapus.');
        } elseif ($type === 'P') {
            KonvenPerawatanV2::findOrFail($realId)->delete();
            return back()->with('success', 'Log pemeliharaan berhasil dihapus.');
        }

        return back()->with('error', 'Format ID log tidak valid.');
    }
}
