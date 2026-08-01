use App\Models\Hole;
use App\Models\Activity;
use Carbon\Carbon;

// 1. Ready to harvest
// Set 20 holes to be planted 45 days ago (Selada)
$holes = Hole::where('status', 'kosong')->take(20)->get();
foreach($holes as $hole) {
    $hole->update([
        'status' => 'ditanam',
        'plant_name' => 'Selada',
        'planted_at' => Carbon::now()->subDays(45)
    ]);
    Activity::create([
        'hole_id' => $hole->id,
        'user_id' => 1,
        'type' => 'tanam',
        'description' => 'Selada ditanam di ' . $hole->name,
        'created_at' => Carbon::now()->subDays(45)
    ]);
}

// 2. Already Harvested this month
// Set 30 holes to harvested
$holes = Hole::where('status', 'kosong')->take(30)->get();
foreach($holes as $hole) {
    $hole->update([
        'status' => 'panen',
        'plant_name' => 'Kangkung',
        'planted_at' => Carbon::now()->subDays(25),
    ]);
    Activity::create([
        'hole_id' => $hole->id,
        'user_id' => 1,
        'type' => 'panen',
        'description' => 'Panen Kangkung di ' . $hole->name,
        'created_at' => Carbon::now()->subDays(2) // Harvested 2 days ago
    ]);
}

// 3. Gagal Panen (Rusak)
// Set 10 holes to rusak
$holes = Hole::where('status', 'kosong')->take(10)->get();
foreach($holes as $hole) {
    $hole->update([
        'status' => 'rusak',
        'plant_name' => 'Bayam',
        'planted_at' => Carbon::now()->subDays(10),
    ]);
    Activity::create([
        'hole_id' => $hole->id,
        'user_id' => 1,
        'type' => 'rusak',
        'description' => 'Gagal panen bayam di ' . $hole->name . ' (Kena Hama)',
        'created_at' => Carbon::now()->subDays(1)
    ]);
}
echo 'More data injected';
