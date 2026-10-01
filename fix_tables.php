<?php

function replaceGridWithTable($file, $tableHeaders, $tableRowCallback, $emptyColspan, $emptyText) {
    if (!file_exists($file)) return;
    $c = file_get_contents($file);
    
    // Find the start of grid <div style="display:grid... and the end before {{-- Modal
    // This is a bit tricky with regex, let's use explode or preg_replace
    $pattern = '/<div style="display:grid; grid-template-columns:repeat.*?@endforelse\s*<\/div>/s';
    
    preg_match($pattern, $c, $matches);
    if (!empty($matches)) {
        $grid = $matches[0];
        
        $table = '<div style="background:white; border-radius:12px; border:1px solid var(--border-color); overflow-x:auto;">
    <table style="width:100%; border-collapse:collapse; text-align:left; min-width:600px;">
        <thead style="background:#f8fafc; border-bottom:1px solid var(--border-color);">
            <tr>
' . $tableHeaders . '
            </tr>
        </thead>
        <tbody>
            @forelse($' . getForelseVar($grid) . ' as $' . getForelseItemVar($grid) . ')
' . $tableRowCallback('$' . getForelseItemVar($grid)) . '
            @empty
            <tr>
                <td colspan="' . $emptyColspan . '" style="padding:3rem; text-align:center; color:var(--text-muted);">' . $emptyText . '</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>';
        
        $c = str_replace($grid, $table, $c);
        file_put_contents($file, $c);
        echo "Updated $file\n";
    } else {
        echo "Grid not found in $file\n";
    }
}

function getForelseVar($grid) {
    preg_match('/@forelse\(\$([a-zA-Z0-9_]+)\s+as/', $grid, $m);
    return $m[1] ?? 'items';
}
function getForelseItemVar($grid) {
    preg_match('/@forelse\(\$[a-zA-Z0-9_]+\s+as\s+\$([a-zA-Z0-9_]+)\)/', $grid, $m);
    return $m[1] ?? 'item';
}

// 1. LAHAN
replaceGridWithTable(
    'resources/views/konvensional/v2/lahan.blade.php',
    '                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Nama Lahan</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Jumlah Kode</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Catatan</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Aksi</th>',
    function($item) {
        return '            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:600;">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <i class="ph ph-plant" style="color:var(--asr-green);"></i> {{ ' . $item . '->nama }}
                    </div>
                </td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ ' . $item . '->kodes_count }} kode</td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ ' . $item . '->catatan ?? \'-\' }}</td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route(\'konven.v2.kode.bylahan\', ' . $item . '->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-arrow-right"></i> Kelola Kode</a>
                        <button onclick="openEditLahan({{ ' . $item . '->id }}, \'{{ addslashes(' . $item . '->nama) }}\', \'{{ addslashes(' . $item . '->catatan ?? \'\') }}\')" style="padding:0.4rem 0.6rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:6px; cursor:pointer;"><i class="ph ph-pencil"></i></button>
                        <form method="POST" action="{{ route(\'konven.v2.lahan.destroy\', ' . $item . '->id) }}" onsubmit="return confirm(\'Hapus lahan?\')" style="margin:0;">
                            @csrf @method(\'DELETE\')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>';
    },
    4,
    'Belum ada lahan. Tambahkan lahan baru untuk memulai.'
);

// 2. KODE
replaceGridWithTable(
    'resources/views/konvensional/v2/kode.blade.php',
    '                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Kode</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Posisi</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Jumlah Zona</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Aksi</th>',
    function($item) {
        return '            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:700; font-size:1.1rem; color:var(--asr-green);">{{ ' . $item . '->kode }}</td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ ' . $item . '->label_posisi ?? \'-\' }}</td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ ' . $item . '->zonas_count }} zona</td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route(\'konven.v2.zona\', ' . $item . '->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-squares-four"></i> Kelola Zona</a>
                        <button onclick="openEditKode({{ ' . $item . '->id }}, \'{{ preg_replace(\'/[0-9]/\',\'\',' . $item . '->kode) }}\', \'{{ ' . $item . '->nomor_urut }}\', \'{{ addslashes(' . $item . '->label_posisi ?? \'\') }}\')" style="padding:0.4rem 0.6rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:6px; cursor:pointer;"><i class="ph ph-pencil"></i></button>
                        <form method="POST" action="{{ route(\'konven.v2.kode.destroy\', ' . $item . '->id) }}" onsubmit="return confirm(\'Hapus kode ini?\')" style="margin:0;">
                            @csrf @method(\'DELETE\')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>';
    },
    4,
    'Belum ada kode lahan.'
);

// 3. ZONA
replaceGridWithTable(
    'resources/views/konvensional/v2/zona.blade.php',
    '                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Nama Zona</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Jumlah Bedengan</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:200px;">Aksi</th>',
    function($item) {
        return '            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:600; color:var(--text-main);">
                    <div style="display:flex; align-items:center; gap:0.5rem;">
                        <i class="ph ph-squares-four" style="color:var(--asr-green);"></i> {{ ' . $item . '->nama }}
                    </div>
                </td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ ' . $item . '->bedengans_count }} bedengan</td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route(\'konven.v2.bedengan\', ' . $item . '->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;"><i class="ph ph-rows"></i> Kelola Bedengan</a>
                        <button onclick="openEditZona({{ ' . $item . '->id }}, \'{{ addslashes(' . $item . '->nama) }}\')" style="padding:0.4rem 0.6rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:6px; cursor:pointer;"><i class="ph ph-pencil"></i></button>
                        <form method="POST" action="{{ route(\'konven.v2.zona.destroy\', ' . $item . '->id) }}" onsubmit="return confirm(\'Hapus zona ini?\')" style="margin:0;">
                            @csrf @method(\'DELETE\')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>';
    },
    3,
    'Belum ada zona.'
);

// 4. BEDENGAN
replaceGridWithTable(
    'resources/views/konvensional/v2/bedengan.blade.php',
    '                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">No. Bedengan</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Nama Display</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Kapasitas / Ditanam</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem;">Progress</th>
                <th style="padding:1rem; font-weight:600; color:var(--text-main); font-size:0.85rem; width:180px;">Aksi</th>',
    function($item) {
        return '            <tr style="border-bottom:1px solid var(--border-color);">
                <td style="padding:1rem; font-weight:700; font-size:1.1rem; color:var(--text-main);">#{{ ' . $item . '->nomor }}</td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">{{ ' . $item . '->nama_display ?? \'-\' }}</td>
                <td style="padding:1rem; color:var(--text-muted); font-size:0.85rem;">
                    <span style="color:var(--asr-green); font-weight:600;">{{ ' . $item . '->terisi }}</span> dari {{ ' . $item . '->lubang_tanams_count }} lubang
                </td>
                <td style="padding:1rem;">
                    @php 
                        $pct = ' . $item . '->lubang_tanams_count > 0 ? round((' . $item . '->terisi / ' . $item . '->lubang_tanams_count)*100) : 0; 
                    @endphp
                    <div style="display:flex; align-items:center; gap:0.5rem; font-size:0.75rem; color:var(--text-muted);">
                        <div style="flex:1; height:6px; background:#e2e8f0; border-radius:3px; overflow:hidden;">
                            <div style="height:100%; width:{{ $pct }}%; background:var(--asr-green);"></div>
                        </div>
                        <span>{{ $pct }}%</span>
                    </div>
                </td>
                <td style="padding:1rem;">
                    <div style="display:flex; gap:0.5rem;">
                        <a href="{{ route(\'konven.v2.bedengan.detail\', ' . $item . '->id) }}" style="padding:0.4rem 0.75rem; background:var(--asr-green); color:white; border-radius:6px; text-decoration:none; font-size:0.75rem; font-weight:600;">Detail Tanam</a>
                        <button onclick="openEditBedengan({{ ' . $item . '->id }}, {{ ' . $item . '->nomor }}, \'{{ addslashes(' . $item . '->nama_display ?? \'\') }}\')" style="padding:0.4rem 0.6rem; background:#f1f5f9; border:1px solid var(--border-color); border-radius:6px; cursor:pointer;"><i class="ph ph-pencil"></i></button>
                        <form method="POST" action="{{ route(\'konven.v2.bedengan.destroy\', ' . $item . '->id) }}" onsubmit="return confirm(\'Hapus bedengan ini?\')" style="margin:0;">
                            @csrf @method(\'DELETE\')
                            <button type="submit" style="padding:0.4rem 0.6rem; background:#fee2e2; border:1px solid #fca5a5; border-radius:6px; color:#dc2626; cursor:pointer;"><i class="ph ph-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>';
    },
    5,
    'Belum ada bedengan.'
);

?>
