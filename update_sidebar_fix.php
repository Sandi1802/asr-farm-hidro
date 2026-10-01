<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

$settingsMenuItem = <<<'EOF'
                <a href="{{ route('settings.index') }}" class="submenu-item {{ request()->is('master-data/settings') ? 'active' : '' }}">
                    <i class="ph ph-gear" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Pengaturan Umum
                </a>
EOF;

// Insert before the closing </div> of nav-submenu
$c = preg_replace('/(Karyawan\s*<\/a>\s*)(@endif\s*<\/div>)/i', '$1' . $settingsMenuItem . "\n                " . '$2', $c);

file_put_contents($f, $c);
echo "Sidebar updated successfully.\n";
