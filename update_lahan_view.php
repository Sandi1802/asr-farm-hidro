<?php
$f = 'resources/views/konvensional/v2/lahan.blade.php';
$c = file_get_contents($f);

$oldTh = '<th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Catatan</th>';
$newTh = '<th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Progres Keterisian Lahan</th>';
$c = str_replace($oldTh, $newTh, $c);

$oldTd = '<td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ $lahan->catatan ?? \'-\' }}</td>';
$newTd = <<<'EOF'
                <td style="padding:1rem;">
                    @php
                        $pct = $lahan->total_lubang > 0 ? round(($lahan->terisi / $lahan->total_lubang)*100) : 0; 
                    @endphp
                    <div style="font-size:0.75rem; color:var(--text-muted); margin-bottom:4px; font-weight:500;">
                        {{ number_format($lahan->terisi, 0, ',', '.') }} dari {{ number_format($lahan->total_lubang, 0, ',', '.') }} lubang terisi
                    </div>
                    <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.75rem; color:var(--text-muted);">
                        <div style="flex:1; height:6px; background:#e2e8f0; border-radius:3px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:var(--asr-green);"></div>
                        </div>
                        <span>{{ $pct }}%</span>
                    </div>
                </td>
EOF;
$c = str_replace($oldTd, $newTd, $c);

file_put_contents($f, $c);
echo "View updated.\n";
