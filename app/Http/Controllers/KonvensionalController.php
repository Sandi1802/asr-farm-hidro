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
        // 1. Kapasitas & Aset (V2)
        $totalLahan = \App\Models\KonvenLahanV2::count();
        $totalBedengan = \App\Models\KonvenBedenganV2::count();
        $totalTitik = \App\Models\KonvenLubangTanamV2::count();
        $idleHolesCount = \App\Models\KonvenLubangTanamV2::where('status', 'kosong')
            ->where('updated_at', '<=', now()->subDays(5))
            ->count();

        $titikKosong = \App\Models\KonvenLubangTanamV2::where('status', 'kosong')->count();

        // 2. Status Produksi (V2)
        $titikTerisi = \App\Models\KonvenLubangTanamV2::where('status', 'ditanam')->count();
        
        $totalJenisBibit = \App\Models\BibitKonvensional::count(); // Master lama
        $rataPanenBibit = \App\Models\BibitKonvensional::avg('estimasi_panen_hari') ?? 0;

        $siapPanen = \App\Models\KonvenLubangTanamV2::where('status', 'ditanam')
            ->whereNotNull('estimated_harvest_at')
            ->whereDate('estimated_harvest_at', '<=', now())
            ->count();

        $panenBulanIni = \App\Models\KonvenLubangTanamV2::where('status', 'panen')
            ->whereMonth('harvested_at', now()->month)
            ->whereYear('harvested_at', now()->year)
            ->count();

        // 3. Perawatan & Kendala (V2)
        $gagalPanen = \App\Models\KonvenLubangTanamV2::where('status', 'rusak')->count();

        $pemupukanBulanIni = \App\Models\Pemupukan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $penyemprotanBulanIni = \App\Models\Penyemprotan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        // Chart Keterisian (V2)
        $chartKeterisian = ['labels' => [], 'terisi' => [], 'kosong' => []];
        $lahans = \App\Models\KonvenLahanV2::all();
        
        foreach ($lahans as $lahan) {
            $chartKeterisian['labels'][] = $lahan->nama;
            
            // Get all hole counts for this lahan
            $terisi = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
                ->join('konven_bedengans', 'konven_lubang_tanams.bedengan_id', '=', 'konven_bedengans.id')
                ->join('konven_zonas', 'konven_bedengans.zona_id', '=', 'konven_zonas.id')
                ->join('konven_kodes', 'konven_zonas.kode_id', '=', 'konven_kodes.id')
                ->where('konven_kodes.lahan_id', $lahan->id)
                ->where('konven_lubang_tanams.status', 'ditanam')
                ->count();
                
            $kosong = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
                ->join('konven_bedengans', 'konven_lubang_tanams.bedengan_id', '=', 'konven_bedengans.id')
                ->join('konven_zonas', 'konven_bedengans.zona_id', '=', 'konven_zonas.id')
                ->join('konven_kodes', 'konven_zonas.kode_id', '=', 'konven_kodes.id')
                ->where('konven_kodes.lahan_id', $lahan->id)
                ->where('konven_lubang_tanams.status', 'kosong')
                ->count();
                
            $chartKeterisian['terisi'][] = $terisi;
            $chartKeterisian['kosong'][] = $kosong;
        }

        // Chart Perawatan (Old Data)
        $chartPerawatan = ['labels' => [], 'pemupukan' => [], 'penyemprotan' => []];
        for ($i = 3; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $label = $startDate->format('d M') . ' - ' . $endDate->format('d M');
            
            $chartPerawatan['labels'][] = $label;
            $chartPerawatan['pemupukan'][] = \App\Models\Pemupukan::whereBetween('tanggal', [$startDate, $endDate])->count();
            $chartPerawatan['penyemprotan'][] = \App\Models\Penyemprotan::whereBetween('tanggal', [$startDate, $endDate])->count();
        }

        // Top Tanam & Top Panen (V2 Tumpang Sari Support)
        $allTanam = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
            ->whereNotNull('plant_name')->where('plant_name', '!=', '')
            ->get();
        
        $tanamCounts = [];
        $panenCounts = [];
        foreach ($allTanam as $row) {
            $plants = explode(', ', $row->plant_name);
            foreach ($plants as $p) {
                $p = trim($p);
                if (empty($p)) continue;

                if (!isset($tanamCounts[$p])) $tanamCounts[$p] = 0;
                $tanamCounts[$p]++;

                if ($row->status === 'panen') {
                    if (!isset($panenCounts[$p])) $panenCounts[$p] = 0;
                    $panenCounts[$p]++;
                }
            }
        }
        
        arsort($tanamCounts);
        $topTanamList = array_slice($tanamCounts, 0, 8);
        $chartTopTanam = [
            'labels' => array_keys($topTanamList),
            'data' => array_values($topTanamList)
        ];

        arsort($panenCounts);
        $topPanenList = array_slice($panenCounts, 0, 8);
        $chartTopPanen = [
            'labels' => array_keys($topPanenList),
            'data' => array_values($topPanenList)
        ];

        // Kalender (V2)
        $calendarData = [];

        // Penanaman
        $tanamGroups = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
            ->where('status', 'ditanam')
            ->whereNotNull('planted_at')
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(planted_at) as date'), 'plant_name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(planted_at)'), 'plant_name')
            ->get();
            
        foreach ($tanamGroups as $g) {
            $date = $g->date;
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'tanam',
                'gh_name' => 'Penanaman ' . ($g->plant_name ?: 'Tanaman'),
                'hole_count' => $g->count
            ];
        }
        
        // Panen
        $panenGroups = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
            ->where('status', 'ditanam')
            ->whereNotNull('estimated_harvest_at')
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(estimated_harvest_at) as date'), 'plant_name', \Illuminate\Support\Facades\DB::raw('COUNT(*) as count'))
            ->groupBy(\Illuminate\Support\Facades\DB::raw('DATE(estimated_harvest_at)'), 'plant_name')
            ->get();
            
        foreach ($panenGroups as $g) {
            $date = $g->date;
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'harvest',
                'gh_name' => 'Estimasi Panen ' . ($g->plant_name ?: 'Tanaman'),
                'hole_count' => $g->count
            ];
        }
        
        // Pemupukan (Old Data)
        $pemupukans = \App\Models\Pemupukan::whereNotNull('tanggal')->get();
        foreach ($pemupukans as $p) {
            $date = \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d');
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'custom',
                'gh_name' => 'Pemupukan ' . ($p->jenis_pupuk ?: ''),
                'hole_count' => 0
            ];
        }
        
        // Penyemprotan (Old Data)
        $penyemprotans = \App\Models\Penyemprotan::whereNotNull('tanggal')->get();
        foreach ($penyemprotans as $p) {
            $date = \Carbon\Carbon::parse($p->tanggal)->format('Y-m-d');
            if (!isset($calendarData[$date])) $calendarData[$date] = [];
            $calendarData[$date][] = [
                'type' => 'custom',
                'gh_name' => 'Penyemprotan ' . ($p->jenis_obat ?: ''),
                'hole_count' => 0
            ];
        }

        $calendarJson = json_encode($calendarData);

        return view('konvensional.dashboard', compact(
            'totalLahan', 'totalBedengan', 'totalTitik', 'titikKosong', 'idleHolesCount',
            'titikTerisi', 'totalJenisBibit', 'rataPanenBibit', 'siapPanen', 'panenBulanIni',
            'gagalPanen', 'pemupukanBulanIni', 'penyemprotanBulanIni',
            'chartKeterisian', 'chartPerawatan', 'calendarJson', 'chartTopTanam', 'chartTopPanen'
        ));
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

        $panenBulanIni = \App\Models\KonvenLubangTanamV2::where('status', 'panen')->whereBetween('harvested_at', [$start, $end])->count();
        $gagalPanen = \App\Models\KonvenLubangTanamV2::where('status', 'rusak')->whereBetween('updated_at', [$start, $end])->count();
        $pemupukanCount = \App\Models\Pemupukan::whereBetween('tanggal', [$start, $end])->count();
        $penyemprotanCount = \App\Models\Penyemprotan::whereBetween('tanggal', [$start, $end])->count();
        $titikDitanam = \App\Models\KonvenLubangTanamV2::where('status', 'ditanam')->whereBetween('planted_at', [$start, $end])->count();

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


    public function titikTanamMassal(Request $request)
    {
        $request->validate([
            'bedengan_ids' => 'required|array',
            'nama_tanaman' => 'required|string',
            'tanaman_sekunder' => 'nullable|string'
        ]);

        $bedenganIds = $request->bedengan_ids;
        $updated = \App\Models\TitikTanam::whereIn('bedengan_id', $bedenganIds)
            ->where('status', 'kosong')
            ->update([
                'status' => 'ditanam',
                'nama_tanaman' => $request->nama_tanaman,
                'tanaman_sekunder' => $request->tanaman_sekunder,
                'tanggal_tanam' => now(),
                'kosong_sejak' => null
            ]);

        return back()->with('success', $updated . ' Titik Tanam berhasil ditanami massal!');
    }

    public function titikTanamUpdate(Request $request, $id)
    {
        $titik = TitikTanam::findOrFail($id);
        
        if ($request->status == 'panen') {
            $jenisPanen = $request->input('jenis_panen', 'cabut');
            
            // Simpan riwayat panen
            \App\Models\RiwayatPanenKonvensional::create([
                'titik_tanam_id' => $titik->id,
                'jenis_panen' => $jenisPanen,
                'jumlah_kg' => $request->input('jumlah_kg'),
                'catatan' => 'Panen ' . $titik->nama_tanaman
            ]);

            if ($jenisPanen == 'cabut') {
                $titik->update([
                    'status' => 'kosong',
                    'nama_tanaman' => null,
                    'tanaman_sekunder' => null,
                    'tanggal_tanam' => null,
                    'tanggal_panen' => null,
                    'kosong_sejak' => now()
                ]);
            } else {
                // Panen Petik -> Status tetap ditanam
                $titik->update([
                    'status' => 'ditanam'
                ]);
            }
        } else {
            // Update biasa
            $data = $request->except(['jenis_panen', 'jumlah_kg']);
            
            if ($request->status == 'ditanam' && $titik->status != 'ditanam') {
                $data['kosong_sejak'] = null; // Menghapus status kosong
                if (!$titik->tanggal_tanam) {
                    $data['tanggal_tanam'] = now();
                }
            }
            if ($request->status == 'kosong' && $titik->status != 'kosong') {
                $data['kosong_sejak'] = now();
            }
            
            $titik->update($data);
        }

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
