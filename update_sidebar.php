<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);
$old = <<<'EOF'
                <a href="/hydroponics/damage-notes" class="submenu-item {{ request()->is('hydroponics/damage-notes*') ? 'active' : '' }}">
                    <i class="ph ph-warning-octagon" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Catatan Kerusakan
                </a>
EOF;
$new = <<<'EOF'
                <a href="/hydroponics/damage-notes" class="submenu-item {{ request()->is('hydroponics/damage-notes*') ? 'active' : '' }}">
                    <i class="ph ph-warning-octagon" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Kerusakan Tanaman
                </a>
                
                <a href="/hydroponics/asset-damage-notes" class="submenu-item {{ request()->is('hydroponics/asset-damage-notes*') ? 'active' : '' }}">
                    <i class="ph ph-hard-drives" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Kerusakan Aset
                </a>
EOF;
$c = str_replace($old, $new, $c);

// Also add it to $hidroponikActive
$oldActive = "request()->is('hydroponics/damage-notes*');";
$newActive = "request()->is('hydroponics/damage-notes*') || request()->is('hydroponics/asset-damage-notes*');";
$c = str_replace($oldActive, $newActive, $c);

file_put_contents($f, $c);
echo "Sidebar updated.\n";
