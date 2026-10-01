<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

if (strpos($c, 'asset-damage-notes') === false) {
    $search = '<a href="/hydroponics/damage-notes" class="submenu-item {{ request()->is(\'hydroponics/damage-notes*\') ? \'active\' : \'\' }}">' . "\n" .
              '                    <i class="ph ph-warning-octagon" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Kerusakan Tanaman' . "\n" .
              '                </a>';
    
    $replace = $search . "\n\n" .
               '                <a href="/hydroponics/asset-damage-notes" class="submenu-item {{ request()->is(\'hydroponics/asset-damage-notes*\') ? \'active\' : \'\' }}">' . "\n" .
               '                    <i class="ph ph-hard-drives" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Kerusakan Aset' . "\n" .
               '                </a>';
               
    $c = str_replace($search, $replace, $c);
}

file_put_contents($f, $c);
echo "Sidebar fully fixed.\n";
