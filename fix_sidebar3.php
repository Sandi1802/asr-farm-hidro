<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

if (strpos($c, 'asset-damage-notes" class="submenu-item') === false) {
    $c = preg_replace(
        '#<a href="/hydroponics/damage-notes".*?</a>#s',
        "$0\n                <a href=\"/hydroponics/asset-damage-notes\" class=\"submenu-item {{ request()->is('hydroponics/asset-damage-notes*') ? 'active' : '' }}\">\n                    <i class=\"ph ph-hard-drives\" style=\"margin-right: 0.5rem; font-size: 1.1rem;\"></i> Kerusakan Aset\n                </a>",
        $c
    );
}

file_put_contents($f, $c);
echo "Sidebar fully fixed 3.\n";
