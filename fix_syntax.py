import re

with open('app/Http/Controllers/KonvenKebunController.php', 'r', encoding='utf-8') as f:
    content = f.read()

bad_compact = """        return view('konvensional.kebun.dashboard', [
            'totalBedeng' => \\,
            'terpakai' => \\,
            'kosong' => \\,
            'pctPemanfaatan' => \\,
            'mendekatiPanen' => \\,
            'siapPanen' => \\,
            'terlambatPanen' => \\,
            'estimasiBulanIni' => \\,
            'realisasiBulanIni' => \\,
            'bedengMap' => \\,
            'agendaPanen' => \\,
            'tanamanAktifSummary' => \\,
            'aktivitasTerbaru' => \\,
            'chartPanenBulanan' => \\,
            'chartPerZona' =>         ]);"""

good_compact = """        return view('konvensional.kebun.dashboard', [
            'totalBedeng' => $total_bedeng,
            'terpakai' => $terpakai,
            'kosong' => $kosong,
            'pctPemanfaatan' => $persentase,
            'mendekatiPanen' => $mendekati_panen,
            'siapPanen' => $siap_panen,
            'terlambatPanen' => $terlambat,
            'estimasiBulanIni' => $estimasi_bulan_ini,
            'realisasiBulanIni' => $realisasi_bulan_ini,
            'bedengMap' => $bed_map,
            'agendaPanen' => $agenda_panen,
            'tanamanAktifSummary' => $tanaman_aktif_summary,
            'aktivitasTerbaru' => $aktivitas_terbaru,
            'chartPanenBulanan' => $chart_panen,
            'chartPerZona' => $chart_pemanfaatan
        ]);"""

content = content.replace(bad_compact, good_compact)

with open('app/Http/Controllers/KonvenKebunController.php', 'w', encoding='utf-8') as f:
    f.write(content)
