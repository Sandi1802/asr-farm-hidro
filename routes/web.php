<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Auth Routes (no middleware)
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Protected Routes (requires login)
Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return redirect('/hydroponics/dashboard');
    });

    Route::get('/dashboard', function () {
        return redirect('/hydroponics/dashboard');
    });

    // Fitur IT Diary
    Route::get('/it/diary', [\App\Http\Controllers\ItDiaryController::class, 'index'])->name('it.diary');
    Route::delete('/it/diary/delete-all', [\App\Http\Controllers\ItDiaryController::class, 'deleteAll'])->name('it.diary.delete-all');

    Route::get('/overview', function () {
        return redirect('/hydroponics/dashboard');
    });

    Route::prefix('hydroponics')->group(function () {
        
        // Dashboard Utama - Accessible by All Roles
        Route::get('/dashboard', [\App\Http\Controllers\HydroponicController::class, 'dashboard'])->name('hydroponics.dashboard');
        
        // Dashboard API routes
        Route::get('/dashboard/trend-chart', [\App\Http\Controllers\HydroponicController::class, 'getTrendChartData']);
        Route::get('/dashboard/summary-cards', [\App\Http\Controllers\HydroponicController::class, 'getSummaryCardsData']);
        Route::get('/dashboard/produksi-stats', [\App\Http\Controllers\HydroponicController::class, 'getProduksiStats']);
        Route::get('/dashboard/period-stats', [\App\Http\Controllers\HydroponicController::class, 'getDashboardPeriodStats']);
        
        // Profile Route
        Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
        Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

        // Master Data - IT Admin only
        Route::middleware('role:it_admin')->group(function () {
            Route::get('/master-data/plants', [\App\Http\Controllers\PlantTypeController::class, 'index'])->name('hydroponics.plants');
            Route::get('/master-data/plants/api', [\App\Http\Controllers\PlantTypeController::class, 'api'])->name('hydroponics.plants.api');
            Route::post('/master-data/plants', [\App\Http\Controllers\PlantTypeController::class, 'store'])->name('hydroponics.plants.store');
            Route::post('/master-data/plants/{id}', [\App\Http\Controllers\PlantTypeController::class, 'update'])->name('hydroponics.plants.update');
            Route::delete('/master-data/plants/{id}', [\App\Http\Controllers\PlantTypeController::class, 'destroy'])->name('hydroponics.plants.destroy');

            Route::get('/master-data/users', [\App\Http\Controllers\UserController::class, 'index'])->name('hydroponics.users');
            Route::post('/master-data/users', [\App\Http\Controllers\UserController::class, 'store'])->name('hydroponics.users.store');
            Route::post('/master-data/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('hydroponics.users.update');
            Route::delete('/master-data/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('hydroponics.users.destroy');
            
            Route::get('/master-data/employees', [\App\Http\Controllers\MasterDataController::class, 'employees'])->name('master-data.employees');
            Route::post('/master-data/employees', [\App\Http\Controllers\MasterDataController::class, 'storeEmployee'])->name('master-data.employees.store');
            Route::delete('/master-data/employees/{id}', [\App\Http\Controllers\MasterDataController::class, 'destroyEmployee'])->name('master-data.employees.delete');
            
            Route::get('/master-data/labels', [\App\Http\Controllers\LabelController::class, 'index'])->name('master-data.labels');
            Route::get('/master-data/labels/api', [\App\Http\Controllers\LabelController::class, 'api'])->name('master-data.labels.api');
            Route::post('/master-data/labels', [\App\Http\Controllers\LabelController::class, 'store'])->name('master-data.labels.store');
            Route::post('/master-data/labels/{id}', [\App\Http\Controllers\LabelController::class, 'update'])->name('master-data.labels.update');
            Route::delete('/master-data/labels/{id}', [\App\Http\Controllers\LabelController::class, 'destroy'])->name('master-data.labels.destroy');
        });

        // Hydroponic Module - Produksi, Produksi GH, Packing
        Route::middleware('role:produksi,produksi_gh,packing')->group(function () {
            Route::get('/greenhouses', [\App\Http\Controllers\HydroponicController::class, 'greenhouses'])->name('hydroponics.greenhouses');
            Route::post('/greenhouses', [\App\Http\Controllers\HydroponicController::class, 'storeGreenhouse'])->name('hydroponics.greenhouses.store');
            Route::get('/greenhouses/{id}', [\App\Http\Controllers\HydroponicController::class, 'showGreenhouse'])->name('hydroponics.greenhouses.show');
            Route::get('/greenhouses/{id}/print-qr', [\App\Http\Controllers\HydroponicController::class, 'printAllQr'])->name('hydroponics.greenhouses.print-qr');
            Route::post('/greenhouses/{id}/update', [\App\Http\Controllers\HydroponicController::class, 'updateGreenhouse'])->name('hydroponics.greenhouses.update');
            Route::post('/greenhouses/{id}/spray', [\App\Http\Controllers\HydroponicController::class, 'sprayGreenhouse'])->name('hydroponics.greenhouses.spray');
            Route::delete('/greenhouses/{id}', [\App\Http\Controllers\HydroponicController::class, 'destroyGreenhouse'])->name('hydroponics.greenhouses.destroy');
            Route::delete('/greenhouses/{id}/racks', [\App\Http\Controllers\HydroponicController::class, 'destroyAllRacks'])->name('hydroponics.racks.destroyAll');
            Route::get('/print-all-greenhouses-qr', [\App\Http\Controllers\HydroponicController::class, 'printAllGreenhousesQr'])->name('hydroponics.greenhouses.print-all-gh-qr');
            Route::get('/greenhouses/{id}/print-greenhouse-qr', [\App\Http\Controllers\HydroponicController::class, 'printGreenhouseQr'])->name('hydroponics.greenhouses.print-single-gh-qr');

            Route::post('/greenhouses/{id}/racks', [\App\Http\Controllers\HydroponicController::class, 'storeRack'])->name('hydroponics.racks.store');
            Route::get('/racks/{id}', [\App\Http\Controllers\HydroponicController::class, 'showRack'])->name('hydroponics.racks.show');
            Route::get('/racks/{id}/print-qr', [\App\Http\Controllers\HydroponicController::class, 'printQr'])->name('hydroponics.racks.print-qr');
            Route::post('/racks/{id}/ppm', [\App\Http\Controllers\HydroponicController::class, 'updatePpmPh'])->name('hydroponics.racks.updatePpmPh');
            Route::post('/racks/{id}/drain', [\App\Http\Controllers\HydroponicController::class, 'drainRack'])->name('hydroponics.racks.drain');
            Route::post('/racks/{id}/update', [\App\Http\Controllers\HydroponicController::class, 'updateRack'])->name('hydroponics.racks.update');
            Route::delete('/racks/{id}', [\App\Http\Controllers\HydroponicController::class, 'destroyRack'])->name('hydroponics.racks.destroy');
            
            // Scan QR Routes
            Route::get('/scan/gh/{id}', [\App\Http\Controllers\ScanController::class, 'scanGreenhouse'])->name('hydroponics.scan.gh');
            Route::get('/scan/rack/{id}', [\App\Http\Controllers\ScanController::class, 'scanRack'])->name('hydroponics.scan.rack');
            
            // Hole Update
            Route::post('/holes/bulk-update', [\App\Http\Controllers\HydroponicController::class, 'bulkUpdateHoles'])->name('hydroponics.holes.bulk');
            Route::post('/holes/{id}', [\App\Http\Controllers\HydroponicController::class, 'updateHole'])->name('hydroponics.holes.update');

            // Calendar
            Route::get('/calendar-data', [\App\Http\Controllers\HydroponicController::class, 'calendarData'])->name('hydroponics.calendar');
            Route::post('/calendar-events', [\App\Http\Controllers\HydroponicController::class, 'storeCalendarEvent'])->name('hydroponics.calendar.store');

            // Maintenance Logs
            Route::get('/maintenance-logs', [\App\Http\Controllers\MaintenanceLogController::class, 'index'])->name('hydroponics.maintenance-logs');
            Route::post('/maintenance-logs/destroy-all', [\App\Http\Controllers\MaintenanceLogController::class, 'destroyAll'])->name('hydroponics.maintenance-logs.destroyAll');
            Route::delete('/maintenance-logs/{id}', [\App\Http\Controllers\MaintenanceLogController::class, 'destroy'])->name('hydroponics.maintenance-logs.destroy');

            // Damage Notes
            Route::get('/damage-notes', [\App\Http\Controllers\DamageNoteController::class, 'index'])->name('hydroponics.damage-notes');
            Route::post('/damage-notes', [\App\Http\Controllers\DamageNoteController::class, 'store'])->name('hydroponics.damage-notes.store');
            Route::post('/damage-notes/{id}', [\App\Http\Controllers\DamageNoteController::class, 'update'])->name('hydroponics.damage-notes.update');
            Route::delete('/damage-notes/{id}', [\App\Http\Controllers\DamageNoteController::class, 'destroy'])->name('hydroponics.damage-notes.destroy');

            // Semai (Pembibitan)
            Route::get('/semai', [\App\Http\Controllers\SemaiController::class, 'index'])->name('hydroponics.semai');
            Route::post('/semai', [\App\Http\Controllers\SemaiController::class, 'store'])->name('hydroponics.semai.store');
            Route::patch('/semai/{id}/transfer', [\App\Http\Controllers\SemaiController::class, 'markTransferred'])->name('hydroponics.semai.transfer');
            Route::patch('/semai/{id}/fail', [\App\Http\Controllers\SemaiController::class, 'markFailed'])->name('hydroponics.semai.fail');
            Route::delete('/semai/{id}/delete', [\App\Http\Controllers\SemaiController::class, 'destroy'])->name('hydroponics.semai.destroy');

            Route::get('/notifications', [\App\Http\Controllers\HydroponicController::class, 'getNotifications'])->name('hydroponics.notifications');
            Route::post('/notifications/read', [\App\Http\Controllers\HydroponicController::class, 'markNotificationsRead']);
        });

        // Inventory - Produksi, Produksi GH, Produksi Konven, Keuangan, Pemasaran, Packing
        Route::middleware('role:produksi,produksi_gh,produksi_konven,keuangan,pemasaran,packing')->group(function () {
            Route::get('/inventory', [\App\Http\Controllers\InventoryController::class, 'index'])->name('hydroponics.inventory');
            Route::post('/inventory', [\App\Http\Controllers\InventoryController::class, 'store'])->name('hydroponics.inventory.store');
            Route::post('/inventory/{id}', [\App\Http\Controllers\InventoryController::class, 'update'])->name('hydroponics.inventory.update');
            Route::delete('/inventory/{id}', [\App\Http\Controllers\InventoryController::class, 'destroy'])->name('hydroponics.inventory.destroy');
            Route::get('/inventory/{id}/logs', [\App\Http\Controllers\InventoryController::class, 'logs'])->name('hydroponics.inventory.logs');
        });

        // Pusat Distribusi (Bandar) - Keuangan, Pemasaran, Packing
        Route::middleware('role:keuangan,pemasaran,packing')->group(function () {
            Route::prefix('bandar')->group(function () {
                Route::get('/', [\App\Http\Controllers\BandarController::class, 'index'])->name('hydroponics.bandar');
                
                Route::get('/partners', [\App\Http\Controllers\BandarController::class, 'partners'])->name('hydroponics.bandar.partners');
                Route::post('/partners', [\App\Http\Controllers\BandarController::class, 'storePartner']);
                Route::put('/partners/{id}', [\App\Http\Controllers\BandarController::class, 'updatePartner']);
                Route::delete('/partners/{id}', [\App\Http\Controllers\BandarController::class, 'destroyPartner']);
                
                Route::get('/products', [\App\Http\Controllers\BandarController::class, 'products'])->name('hydroponics.bandar.products');
                Route::post('/products', [\App\Http\Controllers\BandarController::class, 'storeProduct']);
                Route::put('/products/{id}', [\App\Http\Controllers\BandarController::class, 'updateProduct']);
                Route::delete('/products/{id}', [\App\Http\Controllers\BandarController::class, 'destroyProduct']);
                
                Route::get('/transactions', [\App\Http\Controllers\BandarController::class, 'transactions'])->name('hydroponics.bandar.transactions');
                Route::post('/transactions', [\App\Http\Controllers\BandarController::class, 'storeTransaction']);
                Route::delete('/transactions/{id}', [\App\Http\Controllers\BandarController::class, 'destroyTransaction']);
            });
        });
    });

    Route::prefix('konvensional')->middleware('role:produksi,produksi_konven,packing')->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\KonvensionalController::class, 'dashboard'])->name('konvensional.dashboard');
        Route::get('/dashboard/period-stats', [\App\Http\Controllers\KonvensionalController::class, 'getDashboardPeriodStats']);
        
        // Lahan
        Route::get('/lahan', [\App\Http\Controllers\KonvensionalController::class, 'lahanIndex'])->name('konvensional.lahan');
        Route::post('/lahan', [\App\Http\Controllers\KonvensionalController::class, 'lahanStore']);
        Route::post('/lahan/{id}', [\App\Http\Controllers\KonvensionalController::class, 'lahanUpdate']);
        Route::delete('/lahan/{id}', [\App\Http\Controllers\KonvensionalController::class, 'lahanDestroy']);

        // Bedengan
        Route::get('/lahan/{lahan_id}/bedengan', [\App\Http\Controllers\KonvensionalController::class, 'bedenganIndex'])->name('konvensional.bedengan');
        Route::post('/lahan/{lahan_id}/bedengan', [\App\Http\Controllers\KonvensionalController::class, 'bedenganStore']);
        Route::post('/bedengan/{id}', [\App\Http\Controllers\KonvensionalController::class, 'bedenganUpdate']);
        Route::delete('/bedengan/{id}', [\App\Http\Controllers\KonvensionalController::class, 'bedenganDestroy']);

        // Titik Tanam
        Route::get('/bedengan/{bedengan_id}/titik-tanam', [\App\Http\Controllers\KonvensionalController::class, 'titikTanamShow'])->name('konvensional.titik_tanam');
        Route::post('/bedengan/{bedengan_id}/titik-tanam', [\App\Http\Controllers\KonvensionalController::class, 'titikTanamStore']);
        Route::post('/titik-tanam/{id}', [\App\Http\Controllers\KonvensionalController::class, 'titikTanamUpdate']);
        Route::delete('/titik-tanam/{id}', [\App\Http\Controllers\KonvensionalController::class, 'titikTanamDestroy']);

        // Bibit
        Route::get('/bibit', [\App\Http\Controllers\KonvensionalController::class, 'bibitIndex'])->name('konvensional.bibit');
        Route::post('/bibit', [\App\Http\Controllers\KonvensionalController::class, 'bibitStore']);
        Route::post('/bibit/{id}', [\App\Http\Controllers\KonvensionalController::class, 'bibitUpdate']);
        Route::delete('/bibit/{id}', [\App\Http\Controllers\KonvensionalController::class, 'bibitDestroy']);

        // Pemupukan
        Route::get('/pemupukan', [\App\Http\Controllers\KonvensionalController::class, 'pemupukanIndex'])->name('konvensional.pemupukan');
        Route::post('/pemupukan', [\App\Http\Controllers\KonvensionalController::class, 'pemupukanStore']);
        Route::delete('/pemupukan/{id}', [\App\Http\Controllers\KonvensionalController::class, 'pemupukanDestroy'])->name('konvensional.pemupukan.destroy');

        // Penyemprotan
        Route::get('/penyemprotan', [\App\Http\Controllers\KonvensionalController::class, 'penyemprotanIndex'])->name('konvensional.penyemprotan');
        Route::post('/penyemprotan', [\App\Http\Controllers\KonvensionalController::class, 'penyemprotanStore']);
        Route::delete('/penyemprotan/{id}', [\App\Http\Controllers\KonvensionalController::class, 'penyemprotanDestroy'])->name('konvensional.penyemprotan.destroy');
    });

});