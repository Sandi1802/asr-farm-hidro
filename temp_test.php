use App\Http\Controllers\HydroponicController;
use Illuminate\Http\Request;
use App\Models\Semai;
use App\Models\Hole;

// Ensure we have some empty holes
Hole::where('status', 'ditanam')->take(2)->update(['status' => 'kosong']);
$holes = Hole::where('status', 'kosong')->take(2)->pluck('id')->toArray();

// Create a mock request
$request = new Request();
$request->merge([
    'hole_ids' => $holes,
    'status' => 'ditanam',
    'plant_name' => 'TestingStock'
]);

// Call controller directly
$controller = new HydroponicController();

// This should fail because 'TestingStock' does not exist in Semai
$res = $controller->bulkUpdateHoles($request);
dump($res->getContent());

// Now add 1 Semai (not enough for 2 holes)
Semai::create(['plant_name' => 'TestingStock', 'quantity' => 1, 'status' => 'aktif']);
$res2 = $controller->bulkUpdateHoles($request);
dump($res2->getContent());

// Add another Semai (now we have 1 + 2 = 3 stock, we need 2)
Semai::create(['plant_name' => 'TestingStock', 'quantity' => 2, 'status' => 'aktif']);
$res3 = $controller->bulkUpdateHoles($request);
dump($res3->getContent()); // Should succeed

// Check balance
$balance = Semai::where('plant_name', 'TestingStock')->where('status', 'aktif')->sum('quantity');
dump("Balance after planting 2: " . $balance); // Should be 1

// Clean up
Semai::where('plant_name', 'TestingStock')->delete();

