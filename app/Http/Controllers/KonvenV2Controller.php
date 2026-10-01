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
        $lahans = KonvenLahanV2::withCount('posisis')->orderBy('nama')->get();
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
                                  ->orderBy('nama')
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
        $r->validate(['nomor_urut' => 'required|integer|min:1']);

        $kodeModel  = KonvenKodeV2::with('posisi')->findOrFail($id);
        $kodeBaru   = $kodeModel->posisi->prefix_kode . $r->nomor_urut;

        $kodeModel->update([
            'kode'       => $kodeBaru,
            'nomor_urut' => $r->nomor_urut,
        ]);

        return back()->with('success', "Kode berhasil diperbarui menjadi {$kodeBaru}.");
    }

    public function kodeDestroy($id)
    {
        KonvenKodeV2::findOrFail($id)->delete();
        return back()->with('success', 'Kode berhasil dihapus.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // ZONA (dalam kode)
    // ══════════════════════════════════════════════════════════════════════════

    public function zonaIndex($kode_id)
    {
        $kode  = KonvenKodeV2::with('posisi.lahan')->findOrFail($kode_id);
        $zonas = KonvenZonaV2::where('kode_id', $kode_id)
                              ->withCount('bedengans')
                              ->orderBy('nama')
                              ->get();

        return view('konvensional.v2.zona', compact('kode', 'zonas'));
    }

    public function zonaStore(Request $r, $kode_id)
    {
        KonvenKodeV2::findOrFail($kode_id);
        $r->validate(['nama' => 'required|string|max:100']);

        KonvenZonaV2::create(['kode_id' => $kode_id, 'nama' => $r->nama]);
        return back()->with('success', 'Zona berhasil ditambahkan.');
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
        $zona      = KonvenZonaV2::with('kode.posisi.lahan')->findOrFail($zona_id);
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
            'nomor'                => 'required|integer|min:1',
            'nama_display'         => 'nullable|string|max:100',
            'jumlah_lubang_rencana'=> 'required|integer|min:0|max:500',
        ]);

        DB::transaction(function () use ($r, $zona_id) {
            $bedengan = KonvenBedenganV2::create([
                'zona_id'               => $zona_id,
                'nomor'                 => $r->nomor,
                'nama_display'          => $r->nama_display,
                'jumlah_lubang_rencana' => $r->jumlah_lubang_rencana,
            ]);

            // Auto-generate lubang
            $jumlah = (int) $r->jumlah_lubang_rencana;
            if ($jumlah > 0) {
                $rows = [];
                for ($i = 1; $i <= $jumlah; $i++) {
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
        });

        return back()->with('success', 'Bedengan berhasil ditambahkan beserta lubang tanam.');
    }

    public function bedenganUpdate(Request $r, $id)
    {
        $r->validate([
            'nomor'        => 'required|integer|min:1',
            'nama_display' => 'nullable|string|max:100',
        ]);

        KonvenBedenganV2::findOrFail($id)->update($r->only('nomor', 'nama_display'));
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
            'lubangTanams' => fn($q) => $q->orderBy('nomor_lubang'),
        ])->findOrFail($bedengan_id);

        $lubangStats = [
            'total'   => $bedengan->lubangTanams->count(),
            'kosong'  => $bedengan->lubangTanams->where('status', 'kosong')->count(),
            'ditanam' => $bedengan->lubangTanams->where('status', 'ditanam')->count(),
            'panen'   => $bedengan->lubangTanams->where('status', 'panen')->count(),
            'rusak'   => $bedengan->lubangTanams->where('status', 'rusak')->count(),
        ];

        return view('konvensional.v2.bedengan-detail', compact('bedengan', 'lubangStats'));
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
            'plant_name'           => 'required|string|max:100',
            'planted_at'           => 'required|date',
            'estimated_harvest_at' => 'nullable|date',
        ]);

        $bedengan = KonvenBedenganV2::findOrFail($bedengan_id);
        $lubangKosong = KonvenLubangTanamV2::where('bedengan_id', $bedengan_id)
            ->where('status', 'kosong')
            ->get();

        $updated = 0;
        DB::transaction(function () use ($lubangKosong, $r, &$updated) {
            foreach ($lubangKosong as $lubang) {
                $lubang->update([
                    'status'               => 'ditanam',
                    'plant_name'           => $r->plant_name,
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
}
