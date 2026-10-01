<?php
$files = [
    'resources/views/konvensional/v2/lahan.blade.php',
    'resources/views/konvensional/v2/kode.blade.php',
    'resources/views/konvensional/v2/zona.blade.php',
    'resources/views/konvensional/v2/bedengan.blade.php'
];

foreach ($files as $f) {
    if (file_exists($f)) {
        $c = file_get_contents($f);
        // Add class="datatable"
        $c = str_replace(
            '<table style="width:100%; border-collapse:collapse; text-align:left; min-width:600px;">',
            '<table class="table datatable" style="width:100%; border-collapse:collapse; text-align:left; min-width:600px;">',
            $c
        );
        file_put_contents($f, $c);
        echo "Updated $f\n";
    }
}
