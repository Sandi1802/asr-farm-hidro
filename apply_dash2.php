<?php
$f = 'D:/ASR GROUP/ASR-APPS/ASR_GREEN_WEB/resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);

// 1. click
$find1 = '[\'id\' => \'card-siap-panen\', \'label\' => \'Siap Panen\',    \'value\' => number_format($readyToHarvestCount,0,\',\',\'.\'),    \'icon\' => \'ph-trophy\',        \'class\' => \'sbc-gold\',        \'sub\' => $readyTypesCount.\' Jenis Tanaman\'],';
$rep1 = '[\'id\' => \'card-siap-panen\', \'label\' => \'Siap Panen\',    \'value\' => number_format($readyToHarvestCount,0,\',\',\'.\'),    \'icon\' => \'ph-trophy\',        \'class\' => \'sbc-gold\',        \'sub\' => $readyTypesCount.\' Jenis Tanaman\', \'onClick\' => \'showSiapPanenModal()\'],';
$c = str_replace($find1, $rep1, $c);

// 2. anchor
$find2 = '<a @if(isset($s[\'link\'])) href="{{ $s[\'link\'] }}" @endif style="text-decoration:none;">';
$rep2 = '<a @if(isset($s[\'link\'])) href="{{ $s[\'link\'] }}" @elseif(isset($s[\'onClick\'])) onclick="{{ $s[\'onClick\'] }}" style="cursor:pointer;" @endif style="text-decoration:none; @if(!isset($s[\'link\']) && !isset($s[\'onClick\'])) cursor:default; @endif">';
$c = str_replace($find2, $rep2, $c);

$find3 = '@if(isset($s[\'link\']))
                <div class="sbc-link">Selengkapnya <i class="ph ph-arrow-right"></i></div>
                @endif';
$rep3 = '@if(isset($s[\'link\']) || isset($s[\'onClick\']))
                <div class="sbc-link">Selengkapnya <i class="ph ph-arrow-right"></i></div>
                @endif';
$c = str_replace($find3, $rep3, $c);

// 3. modal
$find4 = '    {{-- INVENTORY SECTION --}}';
$rep4 = <<<'EOT'
    <!-- Modal for Siap Panen Details -->
    <div id="siapPanenModal" style="display:none; position:fixed; z-index:9999; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.4); backdrop-filter: blur(2px);">
        <div style="background:var(--card-bg, #ffffff); width:650px; max-width:95%; margin: 60px auto; border-radius:12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; flex-direction: column; max-height:85vh;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between;">
                <h3 style="margin:0; color:var(--text-main); font-size: 1.15rem; font-weight: 700;"><i class="ph ph-trophy" style="color:#ca8a04; margin-right:8px;"></i> Rincian Tanaman Siap Panen</h3>
                <button onclick="document.getElementById('siapPanenModal').style.display='none'" style="border:none; background:transparent; font-size:1.2rem; cursor:pointer; color: var(--text-muted);"><i class="ph ph-x"></i></button>
            </div>
            <div style="padding: 1.5rem; overflow-y:auto;">
                @if(!isset($readyToHarvestItems) || $readyToHarvestItems->isEmpty())
                    <div style="text-align:center; padding:2rem; color:var(--text-muted);">
                        <i class="ph ph-leaf" style="font-size:3rem; opacity:0.3; margin-bottom:1rem; display:block;"></i>
                        Belum ada tanaman yang memasuki masa panen.
                    </div>
                @else
                    @foreach($readyToHarvestItems as $plantName => $holes)
                        @php
                            $totalHoles = $holes->count();
                            $locations = [];
                            foreach($holes as $hole) {
                                $gh = optional(optional(optional($hole->row)->rack)->greenhouse)->name ?? 'GH Unknown';
                                $rack = optional(optional($hole->row)->rack)->name ?? 'Rak Unknown';
                                $age = \Carbon\Carbon::parse($hole->planted_at)->diffInDays(now());
                                $locKey = $gh . ' - ' . $rack;
                                if(!isset($locations[$locKey])) {
                                    $locations[$locKey] = ['count' => 0, 'ages' => []];
                                }
                                $locations[$locKey]['count']++;
                                $locations[$locKey]['ages'][] = $age;
                            }
                        @endphp
                        <div style="margin-bottom:1.5rem; border:1px solid var(--border-color); border-radius:8px; overflow:hidden;">
                            <div style="background:var(--bg-light); padding:0.75rem 1rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
                                <strong style="color:var(--text-main); font-size:1.05rem;">{{ $plantName ?? 'Tanaman Tidak Diketahui' }}</strong>
                                <span style="background:rgba(202, 138, 4, 0.15); color:#ca8a04; padding:3px 10px; border-radius:20px; font-weight:700; font-size:0.85rem;">{{ $totalHoles }} Lubang</span>
                            </div>
                            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                                <thead>
                                    <tr style="background:var(--card-bg, #fff); border-bottom:1px solid var(--border-color); color:var(--text-muted);">
                                        <th style="padding:0.75rem 1rem; text-align:left; font-weight:600;">Lokasi (GH / Rak)</th>
                                        <th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Jumlah</th>
                                        <th style="padding:0.75rem 1rem; text-align:center; font-weight:600;">Usia Tanaman</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locations as $loc => $data)
                                    @php 
                                        $minAge = min($data['ages']);
                                        $maxAge = max($data['ages']);
                                        $ageStr = $minAge == $maxAge ? $minAge . ' hari' : $minAge . ' - ' . $maxAge . ' hari';
                                    @endphp
                                    <tr style="border-bottom:1px solid var(--border-color);">
                                        <td style="padding:0.75rem 1rem; color:var(--text-main);">{{ $loc }}</td>
                                        <td style="padding:0.75rem 1rem; text-align:center; font-weight:600; color:var(--text-main);">{{ $data['count'] }}</td>
                                        <td style="padding:0.75rem 1rem; text-align:center; color:var(--text-muted);">{{ $ageStr }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- INVENTORY SECTION --}}
EOT;
$c = preg_replace('/' . preg_quote($find4, '/') . '/', $rep4, $c, 1);

// 4. script
$find5 = 'function renderCalendar() {';
$rep5 = <<<'EOT'
function showSiapPanenModal() {
    document.getElementById('siapPanenModal').style.display = 'block';
}

function renderCalendar() {
EOT;
$c = str_replace($find5, $rep5, $c);

file_put_contents($f, $c);
echo 'Done';
