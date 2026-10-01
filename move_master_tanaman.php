<?php
$f = 'resources/views/layouts/sidebar.blade.php';
$c = file_get_contents($f);

// 1. Update the masterDataActive condition
$c = preg_replace(
    "/\\\$masterDataActive = request\(\)->is\('hydroponics\/master-data\/\*'\);/",
    '$masterDataActive = request()->is(\'hydroponics/master-data/*\') || request()->is(\'master-data/*\') || request()->is(\'konvensional/kebun/master-tanaman*\');',
    $c
);

// 2. Extract Master Tanaman from Konvensional
$masterTanamanHtml = <<<'EOF'
                <a href="/konvensional/kebun/master-tanaman" class="submenu-item {{ request()->is('konvensional/kebun/master-tanaman*') ? 'active' : '' }}">
                    <i class="ph ph-list-bullets" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Master Tanaman
                </a>
EOF;

// Since there are formatting differences (like indentation), we can use regex to remove it
$c = preg_replace(
    '/\s*<a href="\/konvensional\/kebun\/master-tanaman" class="submenu-item \{\{ request\(\)->is\(\'konvensional\/kebun\/master-tanaman\*\'\) \? \'active\' : \'\' \}\}">\s*<i class="ph ph-list-bullets" style="margin-right: 0\.5rem; font-size: 1\.1rem;"><\/i> Master Tanaman\s*<\/a>/s',
    '',
    $c
);

// 3. Inject it into Master Data, just after Jenis Tanaman
$jenisTanamanHtml = <<<'EOF'
                <a href="/hydroponics/master-data/plants" class="submenu-item {{ request()->is('hydroponics/master-data/plants') ? 'active' : '' }}">
                    <i class="ph ph-list-bullets" style="margin-right: 0.5rem; font-size: 1.1rem;"></i> Jenis Tanaman
                </a>
EOF;

// We will use str_replace
$c = preg_replace(
    '/(<a href="\/hydroponics\/master-data\/plants".*?<\/a>)/s',
    '$1' . "\n" . $masterTanamanHtml,
    $c
);

file_put_contents($f, $c);
echo "Sidebar master tanaman updated.\n";
