use App\Models\PlantType;
use Illuminate\Support\Facades\Schema;

Schema::disableForeignKeyConstraints();
PlantType::truncate();
Schema::enableForeignKeyConstraints();

$plants = [
    ['name' => 'Bayam', 'growth_days' => 25, 'semai_days' => 10, 'color' => '#10b981'],
    ['name' => 'Caisim', 'growth_days' => 30, 'semai_days' => 10, 'color' => '#34d399'],
    ['name' => 'Kangkung', 'growth_days' => 25, 'semai_days' => 7, 'color' => '#059669'],
    ['name' => 'Pakcoy', 'growth_days' => 35, 'semai_days' => 14, 'color' => '#14b8a6'],
    ['name' => 'Pakcoy (varian)', 'growth_days' => 35, 'semai_days' => 14, 'color' => '#0d9488'],
    ['name' => 'Romaine (Romen)', 'growth_days' => 40, 'semai_days' => 14, 'color' => '#84cc16'],
    ['name' => 'Sawi Hijau', 'growth_days' => 30, 'semai_days' => 10, 'color' => '#65a30d'],
    ['name' => 'Selada', 'growth_days' => 45, 'semai_days' => 14, 'color' => '#a3e635'],
];

foreach ($plants as $p) {
    PlantType::create([
        'name' => $p['name'],
        'growth_days' => $p['growth_days'],
        'semai_days' => $p['semai_days'],
        'color' => $p['color'],
    ]);
}
echo 'Master Data Tanaman inserted successfully';
