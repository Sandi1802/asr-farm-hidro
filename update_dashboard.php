<?php
$f = 'resources/views/hydroponics/dashboard.blade.php';
$c = file_get_contents($f);
$c = str_replace(
    "\$totalDamage = \App\Models\DamageNote::where('status','!=','resolved')->count();",
    "\$totalDamage = \App\Models\AssetDamageNote::where('status','!=','resolved')->count();",
    $c
);
$c = str_replace(
    "['label' => 'Perbaikan Aset',   'value' => \$totalDamage, 'icon' => 'ph-warning-octagon','class' => 'sbc-rust', 'link' => '/hydroponics/damage-notes', 'sub' => 'Kasus Menunggu']",
    "['label' => 'Perbaikan Aset',   'value' => \$totalDamage, 'icon' => 'ph-warning-octagon','class' => 'sbc-rust', 'link' => '/hydroponics/asset-damage-notes', 'sub' => 'Kasus Menunggu']",
    $c
);
file_put_contents($f, $c);
echo "Dashboard updated.\n";
