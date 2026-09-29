<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KonvenLahan;
use App\Models\KonvenZona;
use App\Models\KonvenPola;
use App\Models\KonvenBedeng;
use App\Models\KonvenTanaman;
use App\Models\KonvenTanam;
use App\Models\KonvenPanen;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KonvenKebunController extends Controller
{
    /**
     * Main Dashboard Konvensional
     */
    public function dashboard()
    {
        $today = Carbon::today();
        
        // 1. Statistik Bedeng
        $total_bedeng = KonvenBedeng::count();
        $terpakai = KonvenBedeng::whereHas('tanamAktif')->count();
        $kosong = $total_bedeng - $terpakai;
        $persentase = $total_bedeng > 0 ? round(($terpakai / $total_bedeng) * 100, 1) : 0;

        // 2. Status Panen
        // Mendekati panen: H+1 sampai H+7
        $mendekati_panen = KonvenTanam::aktif()
            ->whereDate('estimasi_tanggal_panen', '>', $today)
            ->whereDate('estimasi_tanggal_panen', '<=', $today->copy()->addDays(7))
            ->count();
            
        // Siap panen: H-0 sampai H-7 (lewat sampai 7 hari)
        $siap_panen = KonvenTanam::aktif()
            ->whereDate('estimasi_tanggal_panen', '<=', $today)
            ->whereDate('estimasi_tanggal_panen', '>=', $today->copy()->subDays(7))
            ->count();
            
        // Terlambat: lewat dari 7 hari
        $terlambat = KonvenTanam::aktif()
            ->whereDate('estimasi_tanggal_panen', '<', $today->copy()->subDays(7))
            ->count();

        // 3. Estimasi vs Realisasi Bulan Ini
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();
        
        $estimasi_bulan_ini = KonvenTanam::whereBetween('estimasi_tanggal_panen', [$startOfMonth, $endOfMonth])
            ->sum('estimasi_hasil');
            
        $realisasi_bulan_ini = KonvenPanen::whereBetween('tanggal_panen', [$startOfMonth, $endOfMonth])
            ->sum('jumlah_hasil');

        // 4. Bed Map Data
        $bed_map = KonvenLahan::with(['zona.pola.bedeng' => function($q) {
            $q->orderBy('nomor');
        }, 'zona.pola.bedeng.tanamAktif.tanaman'])->get();

        // 5. Agenda Panen (14 hari ke depan)
        $agenda_panen = KonvenTanam::aktif()
            ->with(['bedeng.pola.zona.lahan', 'tanaman'])
            ->whereBetween('estimasi_tanggal_panen', [$today, $today->copy()->addDays(14)])
            ->orderBy('estimasi_tanggal_panen', 'asc')
            ->get();

        // 6. Summary Tanaman Aktif
        $tanaman_aktif_summary = DB::table('konven_tanam')
            ->join('konven_tanaman', 'konven_tanam.tanaman_id', '=', 'konven_tanaman.id')
            ->whereIn('konven_tanam.status', ['tumbuh', 'panen_sebagian'])
            ->selectRaw('konven_tanaman.nama, konven_tanaman.satuan_hasil, COUNT(konven_tanam.id) as bedeng_count, SUM(konven_tanam.jumlah_tanam) as total_populasi, SUM(konven_tanam.estimasi_hasil) as total_estimasi')
            ->groupBy('konven_tanaman.id', 'konven_tanaman.nama', 'konven_tanaman.satuan_hasil')
            ->orderByDesc('bedeng_count')
            ->get();

        // 7. Aktivitas Terbaru
        $tanam_terbaru = KonvenTanam::with(['bedeng', 'tanaman'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return (object)[
                    'jenis' => 'tanam',
                    'tanggal' => $item->tanggal_tanam,
                    'tanaman' => $item->tanaman->nama,
                    'bedeng' => $item->bedeng->kode,
                    'jumlah' => $item->jumlah_tanam,
                    'created_at' => $item->created_at
                ];
            });
            
        $panen_terbaru = KonvenPanen::with(['tanam.bedeng', 'tanam.tanaman'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return (object)[
                    'jenis' => 'panen',
                    'tanggal' => $item->tanggal_panen,
                    'tanaman' => $item->tanam->tanaman->nama,
                    'bedeng' => $item->tanam->bedeng->kode,
                    'hasil' => $item->jumlah_hasil,
                    'satuan' => $item->satuan,
                    'created_at' => $item->created_at
                ];
            });
            
        $aktivitas_terbaru = collect($tanam_terbaru)->concat($panen_terbaru)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        // 8. Chart Data: Panen per bulan (6 bulan terakhir)
        $chart_panen = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $start = $month->copy()->startOfMonth();
            $end = $month->copy()->endOfMonth();
            
            $total = KonvenPanen::whereBetween('tanggal_panen', [$start, $end])->sum('jumlah_hasil');
            
            $chart_panen['labels'][] = $month->translatedFormat('M Y');
            $chart_panen['data'][] = (float) $total;
        }

        // 9. Chart Data: Pemanfaatan per zona
        $zonas = KonvenZona::withCount(['bedeng as total_bedeng', 'bedeng as terpakai' => function($q) {
            $q->whereHas('tanamAktif');
        }])->get();
        
        $chart_pemanfaatan = [
            'labels' => $zonas->pluck('nama')->toArray(),
            'total' => $zonas->pluck('total_bedeng')->toArray(),
            'terpakai' => $zonas->pluck('terpakai')->toArray(),
        ];

        return view('konvensional.kebun.dashboard', compact(
            'total_bedeng', 'terpakai', 'kosong', 'persentase',
            'mendekati_panen', 'siap_panen', 'terlambat',
            'estimasi_bulan_ini', 'realisasi_bulan_ini',
            'bed_map', 'agenda_panen', 'tanaman_aktif_summary',
            'aktivitas_terbaru', 'chart_panen', 'chart_pemanfaatan'
        ));
    }

    /**
     * Form Tanam Baru
     */
    public function tanamForm(Request $request)
    {
        $lahans = KonvenLahan::with('zona.pola')->get();
        $tanaman_master = KonvenTanaman::orderBy('nama')->get();
        
        $selected_lahan = $request->query('lahan_id');
        $selected_zona = $request->query('zona_id');
        $selected_pola = $request->query('pola_id');
        
        $bedengs = [];
        if ($selected_pola) {
            $bedengs = KonvenBedeng::where('pola_id', $selected_pola)
                ->where('aktif', true)
                ->with('tanamAktif.tanaman')
                ->orderBy('nomor')
                ->get();
        }

        return view('konvensional.kebun.tanam-form', compact(
            'lahans', 'tanaman_master', 
            'selected_lahan', 'selected_zona', 'selected_pola',
            'bedengs'
        ));
    }

    /**
     * AJAX endpoint: Get Bedeng by Pola
     */
    public function getBedengByPola(Request $request)
    {
        $request->validate(['pola_id' => 'required|exists:konven_pola,id']);
        
        $bedengs = KonvenBedeng::where('pola_id', $request->pola_id)
            ->where('aktif', true)
            ->with('tanamAktif.tanaman')
            ->orderBy('nomor')
            ->get();
            
        return response()->json($bedengs->map(function($bedeng) {
            $is_occupied = $bedeng->tanamAktif !== null;
            return [
                'id' => $bedeng->id,
                'kode' => $bedeng->kode,
                'nomor' => $bedeng->nomor,
                'luas_m2' => $bedeng->luas_m2,
                'status' => $is_occupied ? 'occupied' : 'kosong',
                'tanam_aktif' => $is_occupied ? [
                    'tanaman' => $bedeng->tanamAktif->tanaman->nama,
                    'tanggal_tanam' => $bedeng->tanamAktif->tanggal_tanam,
                    'estimasi_panen' => $bedeng->tanamAktif->estimasi_tanggal_panen,
                ] : null
            ];
        }));
    }

    /**
     * Proses Simpan Tanam
     */
    public function tanamStore(Request $request)
    {
        $request->validate([
            'bedeng_ids' => 'required|array',
            'bedeng_ids.*' => 'exists:konven_bedeng,id',
            'tanaman_id' => 'required|exists:konven_tanaman,id',
            'tanggal_tanam' => 'required|date',
            'jumlah_tanam' => 'required', // bisa integer atau array
            'sumber_benih' => 'nullable|string|max:100',
            'jarak_tanam' => 'nullable|string|max:50',
            'estimasi_tanggal_panen' => 'nullable|date',
            'estimasi_hasil' => 'nullable|numeric',
            'catatan' => 'nullable|string'
        ]);

        $tanaman = KonvenTanaman::findOrFail($request->tanaman_id);
        $batch_id = Str::uuid()->toString();
        $tanggal_tanam = Carbon::parse($request->tanggal_tanam);
        
        $user_name = auth()->check() ? auth()->user()->name : 'System';

        DB::beginTransaction();
        try {
            foreach ($request->bedeng_ids as $bedeng_id) {
                $bedeng = KonvenBedeng::findOrFail($bedeng_id);
                
                // Pastikan bedeng kosong
                if ($bedeng->tanamAktif) {
                    throw new \Exception("Bedeng {$bedeng->kode} sudah memiliki tanaman aktif.");
                }

                // Ambil jumlah tanam spesifik atau global
                $jumlah = is_array($request->jumlah_tanam) 
                    ? ($request->jumlah_tanam[$bedeng_id] ?? 0) 
                    : $request->jumlah_tanam;

                if ($jumlah <= 0) continue;

                // Kalkulasi estimasi jika kosong
                $est_panen = $request->estimasi_tanggal_panen 
                    ? $request->estimasi_tanggal_panen 
                    : $tanggal_tanam->copy()->addDays($tanaman->lama_hari_ke_panen)->format('Y-m-d');
                    
                $est_hasil = $request->estimasi_hasil 
                    ? $request->estimasi_hasil 
                    : ($jumlah * $tanaman->rata2_hasil_per_tanaman);

                KonvenTanam::create([
                    'bedeng_id' => $bedeng_id,
                    'tanaman_id' => $tanaman->id,
                    'batch_id' => $batch_id,
                    'tanggal_tanam' => $tanggal_tanam->format('Y-m-d'),
                    'jumlah_tanam' => $jumlah,
                    'jarak_tanam' => $request->jarak_tanam,
                    'sumber_benih' => $request->sumber_benih,
                    'estimasi_tanggal_panen' => $est_panen,
                    'estimasi_hasil' => $est_hasil,
                    'status' => 'tumbuh',
                    'catatan' => $request->catatan,
                    'dibuat_oleh' => $user_name
                ]);
            }
            
            DB::commit();
            return redirect()->route('konvensional.kebun.dashboard')->with('success', 'Data tanam berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan data tanam: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Daftar Tanaman untuk Dipanen
     */
    public function panenIndex()
    {
        $aktif = KonvenTanam::aktif()
            ->with(['bedeng.pola.zona.lahan', 'tanaman', 'panen'])
            ->orderBy('estimasi_tanggal_panen', 'asc')
            ->get();
            
        return view('konvensional.kebun.panen', compact('aktif'));
    }

    /**
     * Simpan Data Panen
     */
    public function panenStore(Request $request)
    {
        $request->validate([
            'tanam_id' => 'required|exists:konven_tanam,id',
            'tanggal_panen' => 'required|date',
            'jumlah_hasil' => 'required|numeric|min:0.01',
            'kualitas' => 'nullable|in:A,B,C',
            'catatan' => 'nullable|string'
        ]);

        $tanam = KonvenTanam::findOrFail($request->tanam_id);
        
        DB::beginTransaction();
        try {
            // Catat panen
            KonvenPanen::create([
                'tanam_id' => $tanam->id,
                'tanggal_panen' => $request->tanggal_panen,
                'jumlah_hasil' => $request->jumlah_hasil,
                'satuan' => $tanam->tanaman->satuan_hasil ?? 'kg',
                'kualitas' => $request->kualitas,
                'catatan' => $request->catatan
            ]);

            // Update status tanam
            $selesai = $request->has('selesai') && $request->selesai;
            $tanam->update([
                'status' => $selesai ? 'selesai' : 'panen_sebagian'
            ]);

            DB::commit();
            return back()->with('success', 'Data panen berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menyimpan data panen: ' . $e->getMessage());
        }
    }

    /**
     * Tandai Tanam Gagal
     */
    public function gagalStore(Request $request)
    {
        $request->validate([
            'tanam_id' => 'required|exists:konven_tanam,id',
            'alasan_gagal' => 'required|string|max:255'
        ]);

        $tanam = KonvenTanam::findOrFail($request->tanam_id);
        $tanam->update([
            'status' => 'gagal',
            'alasan_gagal' => $request->alasan_gagal
        ]);

        return back()->with('success', 'Status tanaman berhasil diubah menjadi gagal.');
    }

    /**
     * Detail Bedeng (bisa dipakai untuk modal/AJAX atau page)
     */
    public function bedengDetail($id)
    {
        $bedeng = KonvenBedeng::with([
            'pola.zona.lahan',
            'tanamAktif.tanaman',
            'tanam' => function($q) {
                $q->orderBy('tanggal_tanam', 'desc');
            },
            'tanam.panen',
            'tanam.tanaman'
        ])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json($bedeng);
        }

        return view('konvensional.kebun.bedeng-detail', compact('bedeng'));
    }

    /**
     * Edit Penanaman
     */
    public function tanamEdit($id)
    {
        $tanam = KonvenTanam::with(['bedeng', 'tanaman'])->findOrFail($id);
        return view('konvensional.kebun.tanam-edit', compact('tanam'));
    }

    /**
     * Update Penanaman
     */
    public function tanamUpdate(Request $request, $id)
    {
        $request->validate([
            'tanggal_tanam' => 'required|date',
            'jumlah_tanam' => 'required|integer|min:1',
            'estimasi_tanggal_panen' => 'required|date',
            'estimasi_hasil' => 'required|numeric|min:0',
            'catatan' => 'nullable|string'
        ]);

        $tanam = KonvenTanam::findOrFail($id);
        $tanam->update($request->only([
            'tanggal_tanam', 'jumlah_tanam', 'estimasi_tanggal_panen', 'estimasi_hasil', 'catatan'
        ]));

        return redirect()->route('konvensional.kebun.panenIndex')->with('success', 'Data tanam berhasil diperbarui.');
    }

    /**
     * Hapus Penanaman beserta Panen
     */
    public function tanamDestroy($id)
    {
        $tanam = KonvenTanam::findOrFail($id);
        
        DB::beginTransaction();
        try {
            // Hapus panen terkait
            $tanam->panen()->delete();
            $tanam->delete();
            
            DB::commit();
            return back()->with('success', 'Data tanam berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus data tanam: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Data Panen Tunggal
     */
    public function panenDestroy($id)
    {
        $panen = KonvenPanen::findOrFail($id);
        $tanam = $panen->tanam;
        
        DB::beginTransaction();
        try {
            $panen->delete();
            
            // Cek apakah masih ada panen lain
            if ($tanam->status === 'panen_sebagian' && $tanam->panen()->count() === 0) {
                $tanam->update(['status' => 'tumbuh']);
            }
            
            DB::commit();
            return back()->with('success', 'Data panen berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal menghapus data panen: ' . $e->getMessage());
        }
    }

    /**
     * Riwayat Bedeng
     */
    public function riwayatBedeng($id)
    {
        $bedeng = KonvenBedeng::with(['pola.zona.lahan'])->findOrFail($id);
        $riwayats = KonvenTanam::where('bedeng_id', $id)
            ->with(['tanaman', 'panen'])
            ->orderBy('tanggal_tanam', 'desc')
            ->get();
            
        return view('konvensional.kebun.riwayat-bedeng', compact('bedeng', 'riwayats'));
    }

    /**
     * MASTER TANAMAN: Menampilkan daftar
     */
    public function masterTanaman()
    {
        $tanaman = KonvenTanaman::orderBy('nama')->get();
        return view('konvensional.kebun.master-tanaman', compact('tanaman'));
    }

    /**
     * MASTER TANAMAN: Simpan baru
     */
    public function masterTanamanStore(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'varietas' => 'nullable|string|max:100',
            'lama_hari_ke_panen' => 'required|integer|min:1',
            'satuan_hasil' => 'required|string|max:20',
            'rata2_hasil_per_tanaman' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string'
        ]);

        KonvenTanaman::create($request->all());
        
        return back()->with('success', 'Master tanaman berhasil ditambahkan.');
    }

    /**
     * MASTER TANAMAN: Update
     */
    public function masterTanamanUpdate(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'varietas' => 'nullable|string|max:100',
            'lama_hari_ke_panen' => 'required|integer|min:1',
            'satuan_hasil' => 'required|string|max:20',
            'rata2_hasil_per_tanaman' => 'required|numeric|min:0.01',
            'catatan' => 'nullable|string'
        ]);

        $tanaman = KonvenTanaman::findOrFail($id);
        $tanaman->update($request->all());
        
        return back()->with('success', 'Master tanaman berhasil diperbarui.');
    }

    /**
     * MASTER TANAMAN: Hapus
     */
    public function masterTanamanDestroy($id)
    {
        $tanaman = KonvenTanaman::findOrFail($id);
        
        // Cek apakah sudah digunakan
        if (KonvenTanam::where('tanaman_id', $id)->exists()) {
            return back()->with('error', 'Tanaman tidak dapat dihapus karena sudah digunakan dalam data tanam.');
        }
        
        $tanaman->delete();
        return back()->with('success', 'Master tanaman berhasil dihapus.');
    }
}

