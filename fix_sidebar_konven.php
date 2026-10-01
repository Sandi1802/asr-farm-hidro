<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

$oldSubmenu = '<a href="{{ route(\'konvensional.lahan\') }}" class="submenu-item {{ request()->is(\'konvensional/lahan*\') || request()->is(\'konvensional/bedengan*\') || request()->is(\'konvensional/titik-tanam*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-map-trifold" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Manajemen Lahan' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="{{ route(\'konvensional.pemupukan\') }}" class="submenu-item {{ request()->is(\'konvensional/pemupukan*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-flask" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jadwal Pemupukan' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="{{ route(\'konvensional.penyemprotan\') }}" class="submenu-item {{ request()->is(\'konvensional/penyemprotan*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-drop" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jadwal Penyemprotan' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="/konvensional/kebun" class="submenu-item {{ request()->is(\'konvensional/kebun\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-squares-four" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Peta Bedeng' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="/konvensional/kebun/tanam" class="submenu-item {{ request()->is(\'konvensional/kebun/tanam*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-plant" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Tanam Baru' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="/konvensional/kebun/panen" class="submenu-item {{ request()->is(\'konvensional/kebun/panen*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-basket" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Catat Panen' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="/konvensional/kebun/master-tanaman" class="submenu-item {{ request()->is(\'konvensional/kebun/master-tanaman*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-list-bullets" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Master Tanaman' . "\r\n" .
'                </a>';

$newSubmenu = '<a href="{{ route(\'konven.v2.lahan\') }}" class="submenu-item {{ request()->is(\'konvensional/v2*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-map-trifold" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Manajemen Lahan' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="{{ route(\'konvensional.pemupukan\') }}" class="submenu-item {{ request()->is(\'konvensional/pemupukan*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-flask" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jadwal Pemupukan' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="{{ route(\'konvensional.penyemprotan\') }}" class="submenu-item {{ request()->is(\'konvensional/penyemprotan*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-drop" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jadwal Penyemprotan' . "\r\n" .
'                </a>' . "\r\n" .
'                <a href="/konvensional/kebun/master-tanaman" class="submenu-item {{ request()->is(\'konvensional/kebun/master-tanaman*\') ? \'active\' : \'\' }}">' . "\r\n" .
'                    <i class="ph ph-list-bullets" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Master Tanaman' . "\r\n" .
'                </a>';

$result = str_replace($oldSubmenu, $newSubmenu, $c);
if ($result === $c) {
    // Try trimmed match
    $c = preg_replace('/(<a href="{{ route\(\'konvensional\.lahan\'\) }}".*?Master Tanaman.*?<\/a>)/s', $newSubmenu, $c);
    file_put_contents($f, $c);
    echo "Used regex replace.\n";
} else {
    file_put_contents($f, $result);
    echo "Sidebar updated.\n";
}
