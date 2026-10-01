<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    App\Models\KonvenTanaman::create(['nama' => 'Test', 'lama_hari_ke_panen' => 30, 'satuan_hasil' => 'kg', 'rata2_hasil_per_tanaman' => 0.5]);
    echo "Success\n";
} catch (\Exception $e) {
    echo $e->getMessage() . "\n";
}
