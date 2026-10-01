<?php
$file = 'resources/views/layouts/sidebar.blade.php';
$content = file_get_contents($file);
$content = str_replace(
    "<a href=\"{{ route('konvensional.penyemprotan') }}\" class=\"submenu-item {{ request()->is('konvensional/penyemprotan*') ? 'active' : '' }}\">\n                    <i class=\"ph ph-drop\" style=\"margin-right: 0.5rem; font-size: 1.1rem;\"></i> Jadwal Penyemprotan\n                </a>",
    "<a href=\"{{ route('konvensional.penyemprotan') }}\" class=\"submenu-item {{ request()->is('konvensional/penyemprotan*') ? 'active' : '' }}\">\n                    <i class=\"ph ph-drop\" style=\"margin-right: 0.5rem; font-size: 1.1rem;\"></i> Jadwal Penyemprotan\n                </a>\n                <a href=\"{{ route('konven.v2.logs') }}\" class=\"submenu-item {{ request()->is('konvensional/v2/logs') ? 'active' : '' }}\">\n                    <i class=\"ph ph-clipboard-text\" style=\"margin-right: 0.5rem; font-size: 1.1rem;\"></i> Laporan Pemeliharaan\n                </a>",
    $content
);
file_put_contents($file, $content);
