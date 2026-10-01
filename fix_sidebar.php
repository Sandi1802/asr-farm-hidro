<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

// Replace "Catatan Kerusakan" -> "Kerusakan Tanaman"
$c = str_replace('<i class="ph ph-warning-octagon" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Catatan Kerusakan', '<i class="ph ph-warning-octagon" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Kerusakan Tanaman', $c);

// Add "Kerusakan Aset" after "Kerusakan Tanaman" block
$oldBlock = '<a href="/hydroponics/damage-notes" class="submenu-item {{ request()->is(\'hydroponics/damage-notes*\') ? \'active\' : \'\' }}">' . "\n" .
            '                    <i class="ph ph-warning-octagon" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Kerusakan Tanaman' . "\n" .
            '                </a>';

if (strpos($c, 'asset-damage-notes') === false) {
    // We will just do a regex replace
    $c = preg_replace(
        '/<a href="\/hydroponics\/damage-notes".*?<\/a>/s',
        '$0' . "\n" . '                <a href="/hydroponics/asset-damage-notes" class="submenu-item {{ request()->is(\'hydroponics/asset-damage-notes*\') ? \'active\' : \'\' }}">' . "\n" .
        '                    <i class="ph ph-hard-drives" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Kerusakan Aset' . "\n" .
        '                </a>',
        $c
    );
}

// Update Active check
if (strpos($c, "request()->is('hydroponics/asset-damage-notes*')") === false) {
    $c = str_replace("request()->is('hydroponics/damage-notes*');", "request()->is('hydroponics/damage-notes*') || request()->is('hydroponics/asset-damage-notes*');", $c);
}

file_put_contents($f, $c);
echo "Sidebar fixed.\n";
