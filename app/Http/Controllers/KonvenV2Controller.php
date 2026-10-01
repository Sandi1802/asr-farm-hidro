<?php

namespace App\Http\Controllers;

use App\Models\KonvenBedenganV2;
use App\Models\KonvenKodeV2;
use App\Models\KonvenLahanV2;
use App\Models\KonvenLubangTanamV2;
use App\Models\KonvenPosisiV2;
use App\Models\KonvenTanamLogV2;
use App\Models\KonvenZonaV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KonvenV2Controller extends Controller
{
    // ══════════════════════════════════════════════════════════════════════════
    // LAHAN
    // ══════════════════════════════════════════════════════════════════════════

    public function lahanIndex()
    {
        $lahans = KonvenLahanV2::with(['kodes' => fn($q) => $q->withCount('zonas')])
                               ->withCount('kodes')
                               ->orderByRaw('LENGTH(nama), nama')
                               ->get();

        foreach ($lahans as $lahan) {
            $stats = \Illuminate\Support\Facades\DB::table('konven_lubang_tanams')
                ->join('konven_bedengans', 'konven_lubang_tanams.bedengan_id', '=', 'konven_bedengans.id')
                ->join('konven_zonas', 'konven_bedengans.zona_id', '=', 'konven_zonas.id')
                ->join('konven_kodes', 'konven_zonas.kode_id', '=', 'konven_kodes.id')
                ->where('konven_kodes.lahan_id', $lahan->id)
                ->select(
                    \Illuminate\Support\Facades\DB::raw('COUNT(konven_lubang_tanams.id) as total_lubang'),
                    \Illuminate\Support\Facades\DB::raw('SUM(CASE WHEN konven_lubang_tanams.status = \'ditanam\' THEN 1 ELSE 0 END) as terisi')
                )->first();

            $lahan->total_lubang = $stats->total_lubang ?? 0;
            $lahan->terisi = $stats->terisi ?? 0;

            $lahan->total_bedengan = \Illuminate\Support\Facades\DB::table('konven_bedengans')
                ->join('konven_zonas', 'konven_bedengans.zona_id', '=', 'konven_zonas.id')
                ->join('konven_kodes', 'konven_zonas.kode_id', '=', 'konven_kodes.id')
                ->where('konven_kodes.lahan_id', $lahan->id)
                ->count();
        }

        return view('konvensional.v2.lahan', compact('lahans'));
    }

    public function lahanStore(Request $r)
    {
        $r->validate([
            'nama'    => 'required|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        KonvenLahanV2::create($r->only('nama', 'catatan'));
        return back()->with('success', 'Lahan berhasil ditambahkan.');
    }

    public function lahanUpdate(Request $r, $id)
    {
        $r->validate([
            'nama'    => 'required|string|max:100',
            'catatan' => 'nullable|string',
        ]);

        KonvenLahanV2::findOrFail($id)->update($r->only('nama', 'catatan'));
        return back()->with('success', 'Lahan berhasil diperbarui.');
    }

    public function lahanDestroy($id)
    {
        KonvenLahanV2::findOrFail($id)->delete();
        return back()->with('success', 'Lahan berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // POSISI (dalam lahan)
    // ══════════════════════════════════════════════════════════════════════════

    public function posisiIndex($lahan_id)
    {
        $lahan   = KonvenLahanV2::findOrFail($lahan_id);
        $posisis = KonvenPosisiV2::where('lahan_id', $lahan_id)
                                  ->withCount('kodes')
                                  ->orderByRaw('LENGTH(nama), nama')
                                  ->get();

        return view('konvensional.v2.posisi', compact('lahan', 'posisis'));
    }

    public function posisiStore(Request $r, $lahan_id)
    {
        KonvenLahanV2::findOrFail($lahan_id); // ensure exists
        $r->validate([
            'nama'        => 'required|string|max:100',
            'prefix_kode' => 'required|alpha|size:1',
        ]);

        KonvenPosisiV2::create([
            'lahan_id'    => $lahan_id,
            'nama'        => $r->nama,
            'prefix_kode' => strtoupper($r->prefix_kode),
        ]);

        return back()->with('success', 'Posisi berhasil ditambahkan.');
    }

    public function posisiUpdate(Request $r, $id)
    {
        $r->validate([
            'nama'        => 'required|string|max:100',
            'prefix_kode' => 'required|alpha|size:1',
        ]);

        $posisi = KonvenPosisiV2::findOrFail($id);
        $posisi->update([
            'nama'        => $r->nama,
            'prefix_kode' => strtoupper($r->prefix_kode),
        ]);

        return back()->with('success', 'Posisi berhasil diperbarui.');
    }

    public function posisiDestroy($id)
    {
        KonvenPosisiV2::findOrFail($id)->delete();
        return back()->with('success', 'Posisi berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // KODE (dalam posisi)
    // ══════════════════════════════════════════════════════════════════════════

    public function kodeIndex($posisi_id)
    {
        $posisi = KonvenPosisiV2::with('lahan')->findOrFail($posisi_id);
        $kodes  = KonvenKodeV2::where('posisi_id', $posisi_id)
                               ->withCount('zonas')
                               ->orderBy('nomor_urut')
                               ->get();

        return view('konvensional.v2.kode', compact('posisi', 'kodes'));
    }

    public function kodeStore(Request $r, $posisi_id)
    {
        $posisi = KonvenPosisiV2::findOrFail($posisi_id);
        $r->validate(['nomor_urut' => 'required|integer|min:1']);

        $kode = $posisi->prefix_kode . $r->nomor_urut;

        KonvenKodeV2::create([
            'posisi_id'  => $posisi_id,
            'kode'       => $kode,
            'nomor_urut' => $r->nomor_urut,
        ]);

        return back()->with('success', "Kode {$kode} berhasil ditambahkan.");
    }

    public function kodeUpdate(Request $r, $id)
    {
        $r->validate([
            'prefix_kode'  => 'required|alpha|size:1',
            'nomor_urut'   => 'required|integer|min:1',
            'label_posisi' => 'nullable|string|max:50',
        ]);

        $kodeModel = KonvenKodeV2::findOrFail($id);
        $kodeBaru  = strtoupper($r->prefix_kode) . $r->nomor_urut;

        $kodeModel->update([
            'kode'         => $kodeBaru,
            'nomor_urut'   => $r->nomor_urut,
            'label_posisi' => $r->label_posisi,
        ]);

        return back()->with('success', "Kode berhasil diperbarui menjadi {$kodeBaru}.");
    }

    public function kodeDestroy($id)
    {
        KonvenKodeV2::findOrFail($id)->delete();
        return back()->with('success', 'Kode berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // KODE LANGSUNG DARI LAHAN (tanpa Posisi) – metode baru
    // ══════════════════════════════════════════════════════════════════════════

    public function kodeByLahan($lahan_id)
    {
        $lahan = KonvenLahanV2::findOrFail($lahan_id);
        $kodes = KonvenKodeV2::where('lahan_id', $lahan_id)
                             ->withCount('zonas')
                             ->orderByRaw('LENGTH(kode), kode')
                             ->get();

        if ($kodes->count() === 1 && !request()->has('manage')) {
            return redirect()->route('konven.v2.zona', $kodes->first()->id);
        }
        return view('konvensional.v2.kode', compact('lahan', 'kodes'));
    }

    public function kodeStoreDirect(Request $r, $lahan_id)
    {
        KonvenLahanV2::findOrFail($lahan_id);
        $r->validate([
            'prefix_kode'  => 'required|alpha|size:1',
            'nomor_urut'   => 'required|integer|min:1',
            'label_posisi' => 'nullable|string|max:50',
        ]);

        $kode = strtoupper($r->prefix_kode) . $r->nomor_urut;

        KonvenKodeV2::create([
            'lahan_id'     => $lahan_id,
            'posisi_id'    => null,
            'kode'         => $kode,
            'nomor_urut'   => $r->nomor_urut,
            'label_posisi' => $r->label_posisi,
        ]);

        return back()->with('success', "Kode {$kode} berhasil ditambahkan.");
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ZONA (dalam kode)
    // ══════════════════════════════════════════════════════════════════════════

    public function zonaIndex($kode_id)
    {
        $kode  = KonvenKodeV2::with('posisi.lahan', 'lahan')->findOrFail($kode_id);
        $zonas = KonvenZonaV2::where('kode_id', $kode_id)
                              ->withCount(['bedengans', 'lubangTanams as total_lubang', 'lubangTanams as terisi' => function ($q) {
                                  $q->where('status', 'ditanam');
                              }])
                              ->orderByRaw('LENGTH(nama), nama')
                              ->get();

        return view('konvensional.v2.zona', compact('kode', 'zonas'));
    }

    public function zonaStore(Request $r, $kode_id)
    {
        KonvenKodeV2::findOrFail($kode_id);
        $r->validate([
            'prefix_nama'     => 'required|string|max:50',
            'jumlah_zona'     => 'required|integer|min:1|max:500',
            'jumlah_bedengan' => 'required|integer|min:0|max:500',
            'jumlah_lubang'   => 'required|integer|min:0|max:2000',
        ]);

        DB::transaction(function () use ($r, $kode_id) {
            $currentZonesCount = \App\Models\KonvenZonaV2::where('kode_id', $kode_id)->count();
            $jz = (int) $r->jumlah_zona;
            $jb = (int) $r->jumlah_bedengan;
            $jl = (int) $r->jumlah_lubang;
            $prefix = trim($r->prefix_nama);

            $suffix = 'A';
            for ($i = 0; $i < $currentZonesCount; $i++) {
                $suffix++;
            }

            for ($z = 1; $z <= $jz; $z++) {
                $namaZona = $jz == 1 && $prefix != 'Zona' && !preg_match('/[a-zA-Z]$/', $prefix) 
                            ? $prefix 
                            : $prefix . ' ' . $suffix;

                $zona = \App\Models\KonvenZonaV2::create([
                    'kode_id' => $kode_id,
                    'nama'    => $namaZona,
                ]);
                
                $suffix++; // Increment for next zone

                if ($jb > 0) {
                    for ($b = 1; $b <= $jb; $b++) {
                        $bedengan = \App\Models\KonvenBedenganV2::create([
                            'zona_id'               => $zona->id,
                            'nomor'                 => $b,
                            'nama_display'          => null,
                            'jumlah_lubang_rencana' => $jl,
                        ]);

                        if ($jl > 0) {
                            $rows = [];
                            $now = now();
                            for ($i = 1; $i <= $jl; $i++) {
                                $rows[] = [
                                    'bedengan_id'  => $bedengan->id,
                                    'nomor_lubang' => $i,
                                    'status'       => 'kosong',
                                    'created_at'   => $now,
                                    'updated_at'   => $now,
                                ];
                            }
                            // Insert holes in bulk for performance
                            \App\Models\KonvenLubangTanamV2::insert($rows);
                        }
                    }
                }
            }
        });

        return back()->with('success', $r->jumlah_zona . ' Zona berhasil ditambahkan beserta struktur bedengan & lubang.');
    }

    public function zonaUpdate(Request $r, $id)
    {
        $r->validate(['nama' => 'required|string|max:100']);
        KonvenZonaV2::findOrFail($id)->update(['nama' => $r->nama]);
        return back()->with('success', 'Zona berhasil diperbarui.');
    }

    public function zonaDestroy($id)
    {
        KonvenZonaV2::findOrFail($id)->delete();
        return back()->with('success', 'Zona berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // BEDENGAN (dalam zona)
    // ══════════════════════════════════════════════════════════════════════════

    public function bedenganIndex($zona_id)
    {
        $zona      = KonvenZonaV2::with('kode.posisi.lahan', 'kode.lahan')->findOrFail($zona_id);
        $bedengans = KonvenBedenganV2::where('zona_id', $zona_id)
            ->with(['lubangTanams'])
            ->orderBy('nomor')
            ->get()
            ->map(function ($b) {
                $b->lubang_count  = $b->lubangTanams->count();
                $b->terisi_count  = $b->lubangTanams->whereIn('status', ['ditanam'])->count();
                $b->kosong_count  = $b->lubangTanams->where('status', 'kosong')->count();
                return $b;
            });

        return view('konvensional.v2.bedengan', compact('zona', 'bedengans'));
    }

    public function bedenganStore(Request $r, $zona_id)
    {
        $zona = KonvenZonaV2::findOrFail($zona_id);
        $r->validate([
            'jumlah_bedengan'      => 'required|integer|min:1|max:100',
            'jumlah_lubang_rencana'=> 'required|integer|min:0|max:500',
        ]);

        DB::transaction(function () use ($r, $zona_id) {
            $currentMax = KonvenBedenganV2::where('zona_id', $zona_id)->max('nomor') ?? 0;
            $jumlahBedengan = (int) $r->jumlah_bedengan;
            $jumlahLubang = (int) $r->jumlah_lubang_rencana;

            for ($b = 1; $b <= $jumlahBedengan; $b++) {
                $newNomor = $currentMax + $b;
                
                $bedengan = KonvenBedenganV2::create([
                    'zona_id'               => $zona_id,
                    'nomor'                 => $newNomor,
                    'nama_display'          => null,
                    'jumlah_lubang_rencana' => $jumlahLubang,
                ]);

                if ($jumlahLubang > 0) {
                    $rows = [];
                    for ($i = 1; $i <= $jumlahLubang; $i++) {
                        $rows[] = [
                            'bedengan_id'  => $bedengan->id,
                            'nomor_lubang' => $i,
                            'status'       => 'kosong',
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ];
                    }
                    KonvenLubangTanamV2::insert($rows);
                }
            }
        });

        return back()->with('success', $r->jumlah_bedengan . ' Bedengan berhasil ditambahkan beserta lubang tanam.');
    }

    public function bedenganUpdate(Request $r, $id)
    {
        $r->validate([
            'nomor'        => 'required|integer|min:1',
            'nama_display' => 'nullable|string|max:100',
            'jumlah_lubang'=> 'nullable|integer|min:1',
        ]);

        $bedengan = KonvenBedenganV2::findOrFail($id);
        $bedengan->update($r->only('nomor', 'nama_display'));
        
        if ($r->filled('jumlah_lubang')) {
            $newTotal = (int) $r->jumlah_lubang;
            $currentHoles = $bedengan->lubangTanams()->count();
            
            if ($newTotal > $currentHoles) {
                // Add new holes
                $holes = [];
                for ($i = $currentHoles + 1; $i <= $newTotal; $i++) {
                    $holes[] = [
                        'bedengan_id' => $bedengan->id,
                        'nomor_lubang' => $i,
                        'status' => 'kosong',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                KonvenLubangTanamV2::insert($holes);
                $bedengan->update(['jumlah_lubang_rencana' => $newTotal]);
            } elseif ($newTotal < $currentHoles) {
                // Delete holes
                $holesToDelete = $bedengan->lubangTanams()->where('nomor_lubang', '>', $newTotal)->get();
                foreach ($holesToDelete as $hole) {
                    if ($hole->status !== 'kosong') {
                        return back()->with('error', "Gagal mengurangi lubang! Lubang nomor {$hole->nomor_lubang} masih memiliki status '{$hole->status}'. Kosongkan terlebih dahulu.");
                    }
                }
                $bedengan->lubangTanams()->where('nomor_lubang', '>', $newTotal)->delete();
                $bedengan->update(['jumlah_lubang_rencana' => $newTotal]);
            }
        }

        return back()->with('success', 'Bedengan berhasil diperbarui.');
    }

    public function bedenganDestroy($id)
    {
        KonvenBedenganV2::findOrFail($id)->delete();
        return back()->with('success', 'Bedengan berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // LUBANG TANAM (detail bedengan)
    // ══════════════════════════════════════════════════════════════════════════

    public function bedenganDetail($bedengan_id)
    {
        $bedengan = KonvenBedenganV2::with([
            'zona.kode.posisi.lahan',
            'zona.kode.lahan',
            'lubangTanams' => fn($q) => $q->orderBy('nomor_lubang'),
        ])->findOrFail($bedengan_id);

        $masterTanaman = \App\Models\KonvenTanaman::orderBy('nama')->get();
        $lubangStats = [
            'total'   => $bedengan->lubangTanams->count(),
            'kosong'  => $bedengan->lubangTanams->where('status', 'kosong')->count(),
            'ditanam' => $bedengan->lubangTanams->where('status', 'ditanam')->count(),
            'panen'   => $bedengan->lubangTanams->where('status', 'panen')->count(),
            'rusak'   => $bedengan->lubangTanams->where('status', 'rusak')->count(),
        ];

        return view('konvensional.v2.bedengan-detail', compact('bedengan', 'lubangStats', 'masterTanaman'));
    }

    public function lubangUpdate(Request $r, $id)
    {
        $r->validate([
            'status'               => 'required|in:kosong,ditanam,panen,rusak',
            'plant_name'           => 'nullable|string|max:100',
            'planted_at'           => 'nullable|date',
            'estimated_harvest_at' => 'nullable|date',
            'harvested_at'         => 'nullable|date',
            'catatan'              => 'nullable|string',
        ]);

        $lubang = KonvenLubangTanamV2::findOrFail($id);
        $lubang->update($r->only(
            'status', 'plant_name', 'planted_at',
            'estimated_harvest_at', 'harvested_at', 'catatan'
        ));

        // Log the action
        KonvenTanamLogV2::create([
            'lubang_id'   => $lubang->id,
            'action_type' => $r->status,
            'plant_name'  => $r->plant_name,
            'details'     => ['catatan' => $r->catatan],
            'created_by'  => Auth::id(),
        ]);

        if ($r->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Lubang tanam berhasil diperbarui.');
    }

    public function tanamMassal(Request $r, $bedengan_id)
    {
        $r->validate([
            'plant_name_1'         => 'required|string|max:100',
            'plant_name_2'         => 'nullable|string|max:100',
            'planted_at'           => 'required|date',
            'estimated_harvest_at' => 'nullable|date',
        ]);

        $combinedPlantName = $r->plant_name_1;
        if ($r->filled('plant_name_2')) {
            $combinedPlantName .= ', ' . $r->plant_name_2;
        }

        $bedengan = KonvenBedenganV2::findOrFail($bedengan_id);
        $lubangKosong = KonvenLubangTanamV2::where('bedengan_id', $bedengan_id)
            ->where('status', 'kosong')
            ->get();

        $updated = 0;
        DB::transaction(function () use ($lubangKosong, $r, $combinedPlantName, &$updated) {
            foreach ($lubangKosong as $lubang) {
                $lubang->update([
                    'status'               => 'ditanam',
                    'plant_name'           => $combinedPlantName,
                    'planted_at'           => $r->planted_at,
                    'estimated_harvest_at' => $r->estimated_harvest_at,
                ]);

                KonvenTanamLogV2::create([
                    'lubang_id'   => $lubang->id,
                    'action_type' => 'tanam',
                    'plant_name'  => $r->plant_name,
                    'details'     => ['planted_at' => $r->planted_at],
                    'created_by'  => Auth::id(),
                ]);
                $updated++;
            }
        });

        return back()->with('success', "{$updated} lubang berhasil ditanam massal.");
    }

    public function tanamMassalZona(Request $r, $zona_id)
    {
        $r->validate([
            'plant_name_1'         => 'required|string|max:100',
            'plant_name_2'         => 'nullable|string|max:100',
            'planted_at'           => 'required|date',
            'estimated_harvest_at' => 'nullable|date',
            'jumlah_tanaman'       => 'required|integer|min:1',
        ]);

        $combinedPlantName = $r->plant_name_1;
        if ($r->filled('plant_name_2')) {
            $combinedPlantName .= ', ' . $r->plant_name_2;
        }

        $zona = KonvenZonaV2::findOrFail($zona_id);
        
        // Ambil lubang kosong di semua bedengan dalam zona ini
        $lubangKosong = KonvenLubangTanamV2::whereHas('bedengan', function ($q) use ($zona_id) {
                $q->where('zona_id', $zona_id);
            })
            ->where('status', 'kosong')
            ->orderBy('bedengan_id')
            ->orderBy('nomor_lubang')
            ->limit($r->jumlah_tanaman)
            ->get();

        if ($lubangKosong->isEmpty()) {
            return back()->with('error', 'Tidak ada lubang kosong di zona ini.');
        }

        $updated = 0;
        \DB::transaction(function () use ($lubangKosong, $r, $combinedPlantName, &$updated) {
            foreach ($lubangKosong as $lubang) {
                $lubang->update([
                    'status'               => 'ditanam',
                    'plant_name'           => $combinedPlantName,
                    'planted_at'           => $r->planted_at,
                    'estimated_harvest_at' => $r->estimated_harvest_at,
                ]);

                KonvenTanamLogV2::create([
                    'lubang_id'   => $lubang->id,
                    'action_type' => 'tanam',
                    'plant_name'  => $combinedPlantName,
                    'created_by'  => \Auth::id(),
                ]);
                $updated++;
            }
        });

        if ($updated < $r->jumlah_tanaman) {
            return back()->with('success', "Tanam massal berhasil sebagian ($updated lubang) karena jumlah lubang kosong tidak mencukupi.");
        }

        return back()->with('success', "Berhasil menanam $updated tanaman secara acak (massal) di zona ini.");
    }
}