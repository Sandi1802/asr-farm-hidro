<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lahan;
use App\Models\Bedengan;
use App\Models\TitikTanam;
use App\Models\Pemupukan;
use App\Models\Penyemprotan;
use App\Models\BibitKonvensional;

class KonvensionalController extends Controller
{
    public function dashboard()
    {
        // 1. Kapasitas & Aset
        $totalLahan = Lahan::count();
        $totalBedengan = Bedengan::count();
        $totalTitik = TitikTanam::count();
        $titikKosong = TitikTanam::where('status', 'kosong')->count();

        // 2. Status Produksi
        $titikTerisi = TitikTanam::where('status', 'ditanam')->count();
        
        $totalJenisBibit = BibitKonvensional::count();
        $rataPanenBibit = BibitKonvensional::avg('estimasi_panen_hari') ?? 0;

        $siapPanen = TitikTanam::where('status', 'ditanam')
            ->whereNotNull('tanggal_panen')
            ->whereDate('tanggal_panen', '<=', now())
            ->count();

        $panenBulanIni = TitikTanam::where('status', 'panen')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();

        // 3. Perawatan & Kendala
        $gagalPanen = TitikTanam::where('status', 'gagal')->count();
        
        $pemupukanBulanIni = Pemupukan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();
            
        $penyemprotanBulanIni = Penyemprotan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        // Data untuk Grafik Keterisian per Lahan
        $lahanList = Lahan::all();
        $chartKeterisian = [
            'labels' => [],
            'terisi' => [],
            'kosong' => []
        ];

        foreach ($lahanList as $lahan) {
            $chartKeterisian['labels'][] = $lahan->nama_lahan;
            $terisi = TitikTanam::whereHas('bedengan', function($q) use ($lahan) {
                $q->where('lahan_id', $lahan->id);
            })->where('status', 'ditanam')->count();
            
            $kosong = TitikTanam::whereHas('bedengan', function($q) use ($lahan) {
                $q->where('lahan_id', $lahan->id);
            })->where('status', 'kosong')->count();

            $chartKeterisian['terisi'][] = $terisi;
            $chartKeterisian['kosong'][] = $kosong;
        }

        // Data untuk Grafik Tren Perawatan (4 minggu terakhir)
        $chartPerawatan = [
            'labels' => [],
            'pemupukan' => [],
            'penyemprotan' => []
        ];
        
        for ($i = 3; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $label = $startDate->format('d M') . ' - ' . $endDate->format('d M');
            
            $chartPerawatan['labels'][] = $label;
            $chartPerawatan['pemupukan'][] = Pemupukan::whereBetween('tanggal', [$startDate, $endDate])->count();
            $chartPerawatan['penyemprotan'][] = Penyemprotan::whereBetween('tanggal', [$startDate, $endDate])->count();
        }

        $calendarJson = json_encode($this->buildCalendarEvents());

        return view('konvensional.dashboard', compact(
            'totalLahan', 'totalBedengan', 'totalTitik', 'titikKosong',
            'titikTerisi', 'totalJenisBibit', 'rataPanenBibit', 'siapPanen', 'panenBulanIni',
            'gagalPanen', 'pemupukanBulanIni', 'penyemprotanBulanIni',
            'chartKeterisian', 'chartPerawatan', 'calendarJson'
        ));
    }

    private function buildCalendarEvents()
    {
        $bibits = BibitKonvensional::all()->keyBy('nama_bibit');
        $events = collect();
        
        $titikTanam = TitikTanam::with(['bedengan.lahan'])->where('status', 'ditanam')->whereNotNull('tanggal_tanam')->get();
        
        foreach ($titikTanam as $titik) {
            $bibit = $bibits->get($titik->nama_tanaman);
            $estimasiPanen = $bibit ? (int)$bibit->estimasi_panen_hari : 30; // default 30 hari if not found
            
            $base = \Carbon\Carbon::parse($titik->tanggal_tanam);
            $lahanName = optional(optional($titik->bedengan)->lahan)->nama_lahan ?? 'Lahan';
            $bedenganName = optional($titik->bedengan)->nama_bedengan ?? 'Bedengan';
            $locationBase = $lahanName . ' › ' . $bedenganName;
            $location = $locationBase . ' › ' . $titik->nama_titik;
            
            // Fase Semai/Tanam (Hari ke-0)
            $events->push([
                'date' => $base->format('Y-m-d'),
                'type' => 'semai',
                'plant_name' => $titik->nama_tanaman ?? 'Tanaman',
                'location' => $location,
                'location_base' => $locationBase,
                'time' => $base->format('H:i'),
                'stage_day' => 0
            ]);
            
            // Fase Panen (Hari ke-estimasiPanen)
            $events->push([
                'date' => $base->copy()->addDays($estimasiPanen)->format('Y-m-d'),
                'type' => 'panen',
                'plant_name' => $titik->nama_tanaman ?? 'Tanaman',
                'location' => $location,
                'location_base' => $locationBase,
                'time' => '07:00', // Default morning harvest
                'stage_day' => $estimasiPanen
            ]);
        }
        
        // Group by date
        $grouped = $events->groupBy('date')->map(function ($items) {
            return $items->toArray();
        });

        return $grouped->toArray();
    }

    public function getDashboardPeriodStats(Request $request)
    {
        $period = $request->query('period', 'month');
        $now = \Carbon\Carbon::now();

        switch ($period) {
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $periodLabel = 'Tahun ' . $now->year;
                break;
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $periodLabel = 'Minggu Ini';
                break;
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $periodLabel = 'Hari Ini (' . $now->translatedFormat('d M Y') . ')';
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $periodLabel = $now->translatedFormat('F Y');
                break;
        }

        $panenBulanIni = \App\Models\TitikTanam::where('status', 'panen')->whereBetween('updated_at', [$start, $end])->count();
        $gagalPanen = \App\Models\TitikTanam::where('status', 'gagal')->whereBetween('updated_at', [$start, $end])->count();
        $pemupukanCount = \App\Models\Pemupukan::whereBetween('tanggal', [$start, $end])->count();
        $penyemprotanCount = \App\Models\Penyemprotan::whereBetween('tanggal', [$start, $end])->count();
        $titikDitanam = \App\Models\TitikTanam::where('status', 'ditanam')->whereBetween('tanggal_tanam', [$start, $end])->count();

        return response()->json([
            'period_label' => $periodLabel,
            'panen' => $panenBulanIni,
            'gagal' => $gagalPanen,
            'pemupukan' => $pemupukanCount,
            'penyemprotan' => $penyemprotanCount,
            'ditanam' => $titikDitanam,
        ]);
    }

    public function lahanIndex()
    {
        $lahans = Lahan::withCount('bedengan')->get();
        return view('konvensional.lahan', compact('lahans'));
    }

    public function lahanStore(Request $request)
    {
        $request->validate(['nama_lahan' => 'required']);
        Lahan::create($request->all());
        return back()->with('success', 'Lahan berhasil ditambahkan');
    }

    public function lahanUpdate(Request $request, $id)
    {
        $lahan = Lahan::findOrFail($id);
        $lahan->update($request->all());
        return back()->with('success', 'Lahan berhasil diupdate');
    }

    public function lahanDestroy($id)
    {
        Lahan::destroy($id);
        return back()->with('success', 'Lahan berhasil dihapus');
    }

    public function bedenganIndex($lahan_id)
    {
        $lahan = Lahan::with('bedengan.titik_tanam')->findOrFail($lahan_id);
        return view('konvensional.bedengan', compact('lahan'));
    }

    public function bedenganStore(Request $request, $lahan_id)
    {
        $request->validate(['nama_bedengan' => 'required']);
        
        $data = $request->all();
        $data['lahan_id'] = $lahan_id;
        $data['pakai_mulsa'] = $request->has('pakai_mulsa');
        
        Bedengan::create($data);
        return back()->with('success', 'Bedengan berhasil ditambahkan');
    }

    public function bedenganUpdate(Request $request, $id)
    {
        $bedengan = Bedengan::findOrFail($id);
        
        $data = $request->all();
        $data['pakai_mulsa'] = $request->has('pakai_mulsa');
        
        $bedengan->update($data);
        return back()->with('success', 'Bedengan berhasil diupdate');
    }

    public function bedenganDestroy($id)
    {
        Bedengan::destroy($id);
        return back()->with('success', 'Bedengan berhasil dihapus');
    }

    public function titikTanamShow($bedengan_id)
    {
        $bedengan = Bedengan::with('titik_tanam')->findOrFail($bedengan_id);
        $bibits = BibitKonvensional::all();
        return view('konvensional.titik_tanam', compact('bedengan', 'bibits'));
    }

    public function titikTanamStore(Request $request, $bedengan_id)
    {
        $request->validate(['jumlah_titik' => 'required|integer|min:1']);
        $jumlah = $request->jumlah_titik;
        
        $lastTitik = TitikTanam::where('bedengan_id', $bedengan_id)->count();
        
        for ($i = 1; $i <= $jumlah; $i++) {
            $nomor = $lastTitik + $i;
            TitikTanam::create([
                'bedengan_id' => $bedengan_id,
                'nama_titik' => 'Titik ' . $nomor,
            ]);
        }
        
        return back()->with('success', $jumlah . ' Titik Tanam berhasil ditambahkan');
    }

    public function titikTanamUpdate(Request $request, $id)
    {
        $titik = TitikTanam::findOrFail($id);
        $titik->update($request->all());
        return back()->with('success', 'Titik Tanam berhasil diupdate');
    }
    
    public function titikTanamDestroy($id)
    {
        TitikTanam::destroy($id);
        return back()->with('success', 'Titik Tanam berhasil dihapus');
    }

    public function bibitIndex()
    {
        $bibits = BibitKonvensional::all();
        return view('konvensional.bibit', compact('bibits'));
    }
    
    public function bibitStore(Request $request)
    {
        $request->validate([
            'nama_bibit' => 'required',
            'estimasi_panen_hari' => 'required|integer'
        ]);
        BibitKonvensional::create($request->all());
        return back()->with('success', 'Bibit berhasil ditambahkan');
    }
    
    public function bibitUpdate(Request $request, $id)
    {
        $bibit = BibitKonvensional::findOrFail($id);
        $bibit->update($request->all());
        return back()->with('success', 'Bibit berhasil diupdate');
    }
    
    public function bibitDestroy($id)
    {
        BibitKonvensional::destroy($id);
        return back()->with('success', 'Bibit berhasil dihapus');
    }
    
    public function pemupukanIndex()
    {
        $pemupukan = Pemupukan::with(['lahan', 'bedengan'])->orderBy('tanggal', 'desc')->get();
        $lahans = Lahan::with('bedengan')->get();
        return view('konvensional.pemupukan', compact('pemupukan', 'lahans'));
    }
    
    public function pemupukanStore(Request $request)
    {
        Pemupukan::create($request->all());
        return back()->with('success', 'Catatan Pemupukan ditambahkan');
    }
    
    public function pemupukanDestroy($id)
    {
        Pemupukan::destroy($id);
        return back()->with('success', 'Catatan Pemupukan dihapus');
    }
    
    public function penyemprotanIndex()
    {
        $penyemprotan = Penyemprotan::with(['lahan', 'bedengan'])->orderBy('tanggal', 'desc')->get();
        $lahans = Lahan::with('bedengan')->get();
        return view('konvensional.penyemprotan', compact('penyemprotan', 'lahans'));
    }
    
    public function penyemprotanStore(Request $request)
    {
        Penyemprotan::create($request->all());
        return back()->with('success', 'Catatan Penyemprotan ditambahkan');
    }
    
    public function penyemprotanDestroy($id)
    {
        Penyemprotan::destroy($id);
        return back()->with('success', 'Catatan Penyemprotan dihapus');
    }
}
