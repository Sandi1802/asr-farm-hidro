<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Mobile App API Routes
Route::post('/login', [\App\Http\Controllers\Api\MobileApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\MobileApiController::class, 'logout']);
    Route::get('/racks', [\App\Http\Controllers\Api\MobileApiController::class, 'getAllRacks']);
    Route::get('/gh/{id}', [\App\Http\Controllers\Api\MobileApiController::class, 'getGHDetail']);
    Route::get('/racks/{id}', [\App\Http\Controllers\Api\MobileApiController::class, 'getRackDetail']);
    Route::post('/racks/{id}/ppm-ph', [\App\Http\Controllers\Api\MobileApiController::class, 'updatePpmPh']);
    Route::post('/racks/{id}/drain', [\App\Http\Controllers\Api\MobileApiController::class, 'drainRack']);
    Route::post('/holes/bulk', [\App\Http\Controllers\Api\MobileApiController::class, 'bulkUpdateHoles']);
    Route::post('/holes/{id}', [\App\Http\Controllers\Api\MobileApiController::class, 'updateHole']);
});
