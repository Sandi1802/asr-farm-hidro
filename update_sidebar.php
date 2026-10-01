<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

$settingsMenuItem = <<<'EOF'
                  <a href="{{ route('settings.index') }}" class="submenu-item {{ request()->is('hydroponics/master-data/settings') ? 'active' : '' }}">
                      <i class="ph ph-gear" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Pengaturan Umum
                  </a>
EOF;

// Insert after Employees in sidebar
$c = str_replace(
    'Karyawan
                  </a>',
    'Karyawan
                  </a>
' . $settingsMenuItem,
    $c
);

file_put_contents($f, $c);
echo "Sidebar updated.\n";
