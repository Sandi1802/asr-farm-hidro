<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\GreenhouseApiController;
use App\Http\Controllers\Api\RackApiController;
use App\Http\Controllers\Api\HoleApiController;
use App\Http\Controllers\Api\SemaiApiController;
use App\Http\Controllers\Api\DamageApiController;
use App\Http\Controllers\Api\PlantTypeApiController;
use App\Http\Controllers\Api\MaintenanceLogController;

// Public
Route::post('/login', [MobileApiController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [MobileApiController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());

    // Read-only endpoints (all authenticated roles)
    Route::get('/dashboard', [DashboardApiController::class, 'index']);
    Route::get('/greenhouses', [GreenhouseApiController::class, 'index']);
    Route::get('/greenhouses/{id}', [GreenhouseApiController::class, 'show']);
    Route::get('/racks', [RackApiController::class, 'index']);
    Route::get('/racks/{id}', [RackApiController::class, 'show']);
    Route::get('/konvensional/dashboard', [\App\Http\Controllers\Api\KonvensionalApiController::class, 'dashboard']);
    Route::get('/semai', [SemaiApiController::class, 'index']);
    Route::get('/plant-types', [PlantTypeApiController::class, 'index']);
    Route::get('/damage-notes', [DamageApiController::class, 'index']);
    Route::get('/damage-notes/stats', [DamageApiController::class, 'stats']);
    Route::get('/holes/{id}/history', [HoleApiController::class, 'history']);
    Route::get('/maintenance-logs', [MaintenanceLogController::class, 'index']);

    // Write endpoints (produksi roles only)
    Route::middleware('api.role:produksi,produksi_gh')->group(function () {
        // Greenhouse
        Route::post('/greenhouses/{id}/spray', [GreenhouseApiController::class, 'spray']);
        Route::post('/greenhouses/{id}/bulk-plant', [GreenhouseApiController::class, 'bulkPlant']);
        Route::post('/greenhouses/{id}/bulk-harvest', [GreenhouseApiController::class, 'bulkHarvest']);
        Route::post('/greenhouses/{id}/bulk-damage', [GreenhouseApiController::class, 'bulkDamage']);
        
        // Rack
        Route::post('/racks/{id}/ppm-ph', [RackApiController::class, 'updatePpmPh']);
        Route::post('/racks/{id}/drain', [RackApiController::class, 'drain']);
        Route::post('/racks/{id}/plant', [RackApiController::class, 'plant']);
        Route::post('/racks/{id}/harvest', [RackApiController::class, 'harvest']);
        Route::post('/racks/{id}/damage', [RackApiController::class, 'damage']);
        Route::post('/racks/{id}/update-age', [RackApiController::class, 'updateAge']);
        
        // Holes
        Route::post('/holes/{id}', [HoleApiController::class, 'update']);
        Route::post('/holes/bulk', [HoleApiController::class, 'bulkUpdate']);
        
        // Semai
        Route::post('/semai', [SemaiApiController::class, 'store']);
        Route::put('/semai/{id}', [SemaiApiController::class, 'update']);
        Route::post('/semai/{id}/transfer', [SemaiApiController::class, 'transfer']);
        Route::post('/semai/{id}/gagal', [SemaiApiController::class, 'gagal']);
        
        // Damage Notes
        Route::post('/damage-notes', [DamageApiController::class, 'store']);
        Route::put('/damage-notes/{id}', [DamageApiController::class, 'update']);
        
        // Maintenance Logs
        Route::post('/maintenance-logs', [MaintenanceLogController::class, 'store']);
    });
});
