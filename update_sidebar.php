<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

$old = <<<EOF
                  <a href="{{ route('konven.v2.lahan') }}" class="submenu-item {{ request()->is('konvensional/v2*') ? 'active' : '' }}">
                      <i class="ph ph-map-trifold" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Manajemen Lahan
                  </a>
EOF;

$new = <<<EOF
                  <a href="{{ route('konven.v2.lahan') }}" class="submenu-item {{ request()->is('konvensional/v2/lahan*') || request()->is('konvensional/v2/kode*') || request()->is('konvensional/v2/zona*') || request()->is('konvensional/v2/bedengan*') ? 'active' : '' }}">
                      <i class="ph ph-map-trifold" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Manajemen Lahan
                  </a>
                  <a href="{{ route('konven.v2.logs') }}" class="submenu-item {{ request()->is('konvensional/v2/logs*') ? 'active' : '' }}">
                      <i class="ph ph-clipboard-text" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Laporan Aktivitas
                  </a>
EOF;

$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "Sidebar updated.\n";
