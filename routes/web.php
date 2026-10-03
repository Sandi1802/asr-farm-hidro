<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Auth Routes (no middleware)
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login')->middleware('throttle:5,1');
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
                // Master Data - IT Admin & Produksi Global
        Route::middleware('role:it_admin,produksi')->group(function () {
            Route::get('/master-data/plants', [\App\Http\Controllers\PlantTypeController::class, 'index'])->name('hydroponics.plants');
            Route::get('/master-data/plants/api', [\App\Http\Controllers\PlantTypeController::class, 'api'])->name('hydroponics.plants.api');
            Route::post('/master-data/plants', [\App\Http\Controllers\PlantTypeController::class, 'store'])->name('hydroponics.plants.store');
            Route::match(['post', 'put'], '/master-data/plants/{id}', [\App\Http\Controllers\PlantTypeController::class, 'update'])->name('hydroponics.plants.update');
            Route::delete('/master-data/plants/{id}', [\App\Http\Controllers\PlantTypeController::class, 'destroy'])->name('hydroponics.plants.destroy');

            Route::get('/master-data/labels', [\App\Http\Controllers\LabelController::class, 'index'])->name('master-data.labels');
            Route::get('/master-data/labels/api', [\App\Http\Controllers\LabelController::class, 'api'])->name('master-data.labels.api');
            Route::post('/master-data/labels', [\App\Http\Controllers\LabelController::class, 'store'])->name('master-data.labels.store');
            Route::match(['post', 'put'], '/master-data/labels/{id}', [\App\Http\Controllers\LabelController::class, 'update'])->name('master-data.labels.update');
            Route::delete('/master-data/labels/{id}', [\App\Http\Controllers\LabelController::class, 'destroy'])->name('master-data.labels.destroy');

            Route::get('/master-data/daily-tasks', [\App\Http\Controllers\DailyTaskTemplateController::class, 'index'])->name('master-data.daily-tasks');
            Route::post('/master-data/daily-tasks', [\App\Http\Controllers\DailyTaskTemplateController::class, 'store'])->name('master-data.daily-tasks.store');
            Route::put('/master-data/daily-tasks/{id}', [\App\Http\Controllers\DailyTaskTemplateController::class, 'update'])->name('master-data.daily-tasks.update');
            Route::delete('/master-data/daily-tasks/{id}', [\App\Http\Controllers\DailyTaskTemplateController::class, 'destroy'])->name('master-data.daily-tasks.destroy');
        });

        // Master Data - IT Admin ONLY
        Route::middleware('role:it_admin')->group(function () {
            

                        // Settings
            Route::get('/master-data/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
            Route::put('/master-data/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

            Route::get('/master-data/users', [\App\Http\Controllers\UserController::class, 'index'])->name('hydroponics.users');
            Route::post('/master-data/users', [\App\Http\Controllers\UserController::class, 'store'])->name('hydroponics.users.store');
            Route::post('/master-data/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('hydroponics.users.update');
            Route::delete('/master-data/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('hydroponics.users.destroy');
            
            Route::get('/master-data/employees', [\App\Http\Controllers\MasterDataController::class, 'employees'])->name('master-data.employees');

            Route::get('/master-data/sync-employees', function () {
                // 1. Fix existing names
                $users = \App\Models\User::all();
                foreach ($users as $user) {
                    if (str_contains($user->name, '@')) {
                        $newName = explode('@', $user->name)[0];
                        $newName = ucwords(str_replace('.', ' ', $newName));
                        $user->name = $newName;
                        $user->save();
                    }
                }
                
                $employees = \App\Models\Employee::all();
                foreach ($employees as $employee) {
                    if (str_contains($employee->name, '@')) {
                        $newName = explode('@', $employee->name)[0];
                        $newName = ucwords(str_replace('.', ' ', $newName));
                        $employee->name = $newName;
                        $employee->save();
                    }
                }

                $users = \App\Models\User::all();
                $count = 0;
                foreach ($users as $user) {
                    if (!\App\Models\Employee::where('email', $user->email)->exists()) {
                        $baseNip = 'EMP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
                        $nip = $baseNip;
                        $counter = 1;
                        while (\App\Models\Employee::where('nip', $nip)->exists()) {
                            $nip = $baseNip . '-' . $counter;
                            $counter++;
                        }
                        
                        \App\Models\Employee::create([
                            'nip' => $nip,
                            'name' => $user->name ?: 'Unknown',
                            'position' => 'Staff',
                            'department' => 'Umum',
                            'email' => $user->email,
                            'status' => 'Active'
                        ]);
                        $count++;
                    }
                }
                return redirect()->route('master-data.employees')->with('success', "Berhasil mensinkronisasi $count data pengguna baru & merapikan nama.");
            });

            Route::post('/master-data/employees', [\App\Http\Controllers\MasterDataController::class, 'storeEmployee'])->name('master-data.employees.store');
            Route::put('/master-data/employees/{id}', [\App\Http\Controllers\MasterDataController::class, 'updateEmployee'])->name('master-data.employees.update');
            Route::delete('/master-data/employees/{id}', [\App\Http\Controllers\MasterDataController::class, 'destroyEmployee'])->name('master-data.employees.delete');
            
            

            
            Route::get('/master-data/fix-db-sequences', function () {
                if (DB::connection()->getDriverName() === 'pgsql') {
                    try {
                        $maxTemplateId = DB::table('daily_task_templates')->max('id') ?? 1;
                        DB::statement("SELECT setval('daily_task_templates_id_seq', $maxTemplateId)");
                        $maxTaskId = DB::table('daily_tasks')->max('id') ?? 1;
                        DB::statement("SELECT setval('daily_tasks_id_seq', $maxTaskId)");
                        return 'Done resetting Postgres sequences!';
                    } catch (\Exception $e) {
                        return 'Error: ' . $e->getMessage();
                    }
                }
                return 'Not using Postgres, skipping sequence reset.';
            });

            // Daily Task Templates
                    });

        // Hydroponic Module - Produksi, Produksi GH, Packing
        Route::middleware('role:produksi,produksi_gh,packing,keuangan,pemasaran')->group(function () {
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
            Route::put('/maintenance-logs/{id}', [\App\Http\Controllers\MaintenanceLogController::class, 'update'])->name('hydroponics.maintenance-logs.update');

            // Damage Notes (Plants)
            Route::get('/damage-notes', [\App\Http\Controllers\DamageNoteController::class, 'index'])->name('hydroponics.damage-notes');
            Route::post('/damage-notes', [\App\Http\Controllers\DamageNoteController::class, 'store'])->name('hydroponics.damage-notes.store');
            Route::post('/damage-notes/{id}', [\App\Http\Controllers\DamageNoteController::class, 'update'])->name('hydroponics.damage-notes.update');
            Route::delete('/damage-notes/{id}', [\App\Http\Controllers\DamageNoteController::class, 'destroy'])->name('hydroponics.damage-notes.destroy');

            // Asset Damage Notes
            Route::get('/asset-damage-notes', [\App\Http\Controllers\AssetDamageNoteController::class, 'index'])->name('hydroponics.asset-damage-notes');
            Route::post('/asset-damage-notes', [\App\Http\Controllers\AssetDamageNoteController::class, 'store'])->name('hydroponics.asset-damage-notes.store');
            Route::post('/asset-damage-notes/{id}', [\App\Http\Controllers\AssetDamageNoteController::class, 'update'])->name('hydroponics.asset-damage-notes.update');
            Route::delete('/asset-damage-notes/{id}', [\App\Http\Controllers\AssetDamageNoteController::class, 'destroy'])->name('hydroponics.asset-damage-notes.destroy');

            // Semai (Pembibitan)
            Route::get('/semai', [\App\Http\Controllers\SemaiController::class, 'index'])->name('hydroponics.semai');
            Route::post('/semai', [\App\Http\Controllers\SemaiController::class, 'store'])->name('hydroponics.semai.store');
            Route::patch('/semai/{id}/transfer', [\App\Http\Controllers\SemaiController::class, 'markTransferred'])->name('hydroponics.semai.transfer');
            Route::patch('/semai/{id}/fail', [\App\Http\Controllers\SemaiController::class, 'markFailed'])->name('hydroponics.semai.fail');
            Route::delete('/semai/{id}/delete', [\App\Http\Controllers\SemaiController::class, 'destroy'])->name('hydroponics.semai.destroy');

            Route::get('/notifications', [\App\Http\Controllers\HydroponicController::class, 'getNotifications'])->name('hydroponics.notifications');
            Route::post('/notifications/read', [\App\Http\Controllers\HydroponicController::class, 'markNotificationsRead']);

    


            // Daily Tasks
            Route::get('/daily-tasks', [\App\Http\Controllers\DailyTaskController::class, 'webIndex'])->name('hydroponics.daily-tasks');
            Route::get('/daily-tasks-api', [\App\Http\Controllers\DailyTaskController::class, 'apiIndex']);
            Route::post('/daily-tasks-api', [\App\Http\Controllers\DailyTaskController::class, 'apiStore']);
            Route::post('/daily-tasks-api/catatan', [\App\Http\Controllers\DailyTaskController::class, 'apiUpdateCatatan']);
            Route::post('/daily-tasks-api/{id}/complete', [\App\Http\Controllers\DailyTaskController::class, 'apiToggleComplete']);
            Route::post('/daily-tasks-api/{id}/note', [\App\Http\Controllers\DailyTaskController::class, 'apiUpdateNote']);
            Route::delete('/daily-tasks-api/{id}', [\App\Http\Controllers\DailyTaskController::class, 'apiDelete']);
        });

        // Inventory - Produksi, Produksi GH, Produksi Konven, Keuangan, Pemasaran, Packing
        Route::middleware('role:produksi,produksi_gh,produksi_konvensional,produksi_paprika,keuangan,pemasaran,packing')->group(function () {
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

    Route::prefix('konvensional')->middleware('role:produksi,produksi_konvensional,packing,keuangan,pemasaran')->group(function () {
    // --- NEW KEBUN MODULE ---
    Route::prefix('kebun')->group(function () {
        Route::get('/', [\App\Http\Controllers\KonvenKebunController::class, 'dashboard'])->name('konvensional.kebun');
        Route::get('/tanam', [\App\Http\Controllers\KonvenKebunController::class, 'tanamForm']);
        Route::post('/tanam', [\App\Http\Controllers\KonvenKebunController::class, 'tanamStore'])->name('konvensional.kebun.tanam.store');
        Route::get('/tanam/get-bedeng', [\App\Http\Controllers\KonvenKebunController::class, 'getBedengByPola']);
        Route::get('/panen', [\App\Http\Controllers\KonvenKebunController::class, 'panenIndex']);
        Route::post('/panen', [\App\Http\Controllers\KonvenKebunController::class, 'panenStore'])->name('konvensional.kebun.panen.store');
        Route::post('/gagal', [\App\Http\Controllers\KonvenKebunController::class, 'gagalStore']);
        
        Route::get('/riwayat/{id}', [\App\Http\Controllers\KonvenKebunController::class, 'riwayatBedeng']);
        
        Route::get('/master-tanaman', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanaman']);
        Route::post('/master-tanaman', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanamanStore'])->name('konvensional.kebun.tanaman.store');
        Route::match(['post', 'put'], '/master-tanaman/{id}', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanamanUpdate']);
        Route::delete('/master-tanaman/{id}', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanamanDestroy'])->name('konvensional.kebun.tanaman.destroy');
    });
    
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
        Route::post('/titik-tanam/massal', [\App\Http\Controllers\KonvensionalController::class, 'titikTanamMassal'])->name('konvensional.titik_tanam.massal');
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

        // ── Konven V2 – Manajemen Struktur Kebun ─────────────────────────────
        Route::prefix('v2')->group(function () {
            // Lahan
            Route::get('/logs', [\App\Http\Controllers\KonvenLogController::class, 'index'])->name('konven.v2.logs');
            Route::delete('/logs/{id}', [\App\Http\Controllers\KonvenLogController::class, 'destroy'])->name('konven.v2.logs.destroy');
    Route::get('/lahan', [\App\Http\Controllers\KonvenV2Controller::class, 'lahanIndex'])->name('konven.v2.lahan');
            Route::post('/lahan', [\App\Http\Controllers\KonvenV2Controller::class, 'lahanStore'])->name('konven.v2.lahan.store');
            Route::put('/lahan/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'lahanUpdate'])->name('konven.v2.lahan.update');
            Route::delete('/lahan/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'lahanDestroy'])->name('konven.v2.lahan.destroy');

            // Posisi
            Route::get('/lahan/{lahan_id}/posisi', [\App\Http\Controllers\KonvenV2Controller::class, 'posisiIndex'])->name('konven.v2.posisi');
            Route::post('/lahan/{lahan_id}/posisi', [\App\Http\Controllers\KonvenV2Controller::class, 'posisiStore'])->name('konven.v2.posisi.store');
            Route::put('/posisi/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'posisiUpdate'])->name('konven.v2.posisi.update');
            Route::delete('/posisi/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'posisiDestroy'])->name('konven.v2.posisi.destroy');

            // Kode
            Route::get('/posisi/{posisi_id}/kode', [\App\Http\Controllers\KonvenV2Controller::class, 'kodeIndex'])->name('konven.v2.kode');
            Route::post('/posisi/{posisi_id}/kode', [\App\Http\Controllers\KonvenV2Controller::class, 'kodeStore'])->name('konven.v2.kode.store');
            Route::put('/kode/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'kodeUpdate'])->name('konven.v2.kode.update');
            Route::delete('/kode/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'kodeDestroy'])->name('konven.v2.kode.destroy');

            // Kode langsung dari Lahan (tanpa Posisi)
            Route::get('/lahan/{lahan_id}/kode', [\App\Http\Controllers\KonvenV2Controller::class, 'kodeByLahan'])->name('konven.v2.kode.bylahan');
            Route::post('/lahan/{lahan_id}/kode', [\App\Http\Controllers\KonvenV2Controller::class, 'kodeStoreDirect'])->name('konven.v2.kode.store.direct');

            // Zona
            Route::get('/kode/{kode_id}/zona', [\App\Http\Controllers\KonvenV2Controller::class, 'zonaIndex'])->name('konven.v2.zona');
            Route::post('/kode/{kode_id}/zona', [\App\Http\Controllers\KonvenV2Controller::class, 'zonaStore'])->name('konven.v2.zona.store');
            Route::put('/zona/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'zonaUpdate'])->name('konven.v2.zona.update');
            Route::delete('/zona/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'zonaDestroy'])->name('konven.v2.zona.destroy');

            // Bedengan
            Route::get('/zona/{zona_id}/bedengan', [\App\Http\Controllers\KonvenV2Controller::class, 'bedenganIndex'])->name('konven.v2.bedengan');
    Route::post('/zona/{zona_id}/tanam-massal', [\App\Http\Controllers\KonvenV2Controller::class, 'tanamMassalZona'])->name('konven.v2.zona.tanam.massal');
            Route::post('/zona/{zona_id}/bedengan', [\App\Http\Controllers\KonvenV2Controller::class, 'bedenganStore'])->name('konven.v2.bedengan.store');
            Route::put('/bedengan/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'bedenganUpdate'])->name('konven.v2.bedengan.update');
            Route::delete('/bedengan/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'bedenganDestroy'])->name('konven.v2.bedengan.destroy');

            // Lubang Tanam
            Route::get('/bedengan/{bedengan_id}', [\App\Http\Controllers\KonvenV2Controller::class, 'bedenganDetail'])->name('konven.v2.bedengan.detail');
            Route::put('/lubang/{id}', [\App\Http\Controllers\KonvenV2Controller::class, 'lubangUpdate'])->name('konven.v2.lubang.update');
            Route::post('/bedengan/{bedengan_id}/tanam-massal', [\App\Http\Controllers\KonvenV2Controller::class, 'tanamMassal'])->name('konven.v2.tanam.massal');
        });
    });


    // Mobile Konven V2
    Route::group(['prefix' => 'm/konven', 'middleware' => ['role:it_admin,produksi_gh,kepala_produksi,produksi_konvensional']], function () {
        Route::get('/', [\App\Http\Controllers\MobileKonvenController::class, 'index'])->name('m.konven.index');
        Route::get('/lahan/{id}', [\App\Http\Controllers\MobileKonvenController::class, 'lahan'])->name('m.konven.lahan');
        Route::get('/kode/{id}', [\App\Http\Controllers\MobileKonvenController::class, 'kode'])->name('m.konven.kode');
        Route::get('/zona/{id}', [\App\Http\Controllers\MobileKonvenController::class, 'zona'])->name('m.konven.zona');
        Route::get('/bedengan/{id}', [\App\Http\Controllers\MobileKonvenController::class, 'bedengan'])->name('m.konven.bedengan');

        Route::post('/bedengan/{id}/tanam', [\App\Http\Controllers\MobileKonvenController::class, 'tanamMassal'])->name('m.konven.tanam');
        Route::post('/bedengan/{id}/panen', [\App\Http\Controllers\MobileKonvenController::class, 'panen'])->name('m.konven.panen');
        Route::post('/bedengan/{id}/perawatan', [\App\Http\Controllers\MobileKonvenController::class, 'perawatan'])->name('m.konven.perawatan');
        Route::post('/bedengan/{id}/kerusakan', [\App\Http\Controllers\MobileKonvenController::class, 'kerusakan'])->name('m.konven.kerusakan');
    });

    // Paprika Routes
    Route::prefix('paprika')->middleware('role:produksi,produksi_paprika,packing,keuangan,pemasaran')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\PaprikaController::class, 'dashboard'])->name('paprika.dashboard');
        Route::get('/greenhouses', [\App\Http\Controllers\PaprikaController::class, 'greenhouses'])->name('paprika.greenhouses');
        Route::get('/greenhouses/{id}', [\App\Http\Controllers\PaprikaController::class, 'greenhouseDetail'])->name('paprika.greenhouses.detail');
        Route::post('/greenhouses/bulk-update', [\App\Http\Controllers\PaprikaController::class, 'bulkUpdatePlants'])->name('paprika.greenhouses.bulk-update');
        Route::get('/pemupukan', [\App\Http\Controllers\PaprikaController::class, 'pemupukan'])->name('paprika.pemupukan');
        Route::post('/pemupukan', [\App\Http\Controllers\PaprikaController::class, 'storePemupukan'])->name('paprika.pemupukan.store');
        Route::get('/penyemprotan', [\App\Http\Controllers\PaprikaController::class, 'penyemprotan'])->name('paprika.penyemprotan');
        Route::post('/penyemprotan', [\App\Http\Controllers\PaprikaController::class, 'storePenyemprotan'])->name('paprika.penyemprotan.store');

        // Paprika V2 Routes
        Route::prefix('v2')->group(function () {
            Route::get('/', [\App\Http\Controllers\PaprikaV2Controller::class, 'index'])->name('paprika.v2.index');
            Route::post('/gh', [\App\Http\Controllers\PaprikaV2Controller::class, 'storeGh'])->name('paprika.v2.gh.store');
            Route::get('/gh/{gh_id}/baris', [\App\Http\Controllers\PaprikaV2Controller::class, 'baris'])->name('paprika.v2.baris');
            Route::post('/gh/{gh_id}/baris', [\App\Http\Controllers\PaprikaV2Controller::class, 'storeBaris'])->name('paprika.v2.baris.store');
            Route::get('/baris/{baris_id}/pot', [\App\Http\Controllers\PaprikaV2Controller::class, 'pot'])->name('paprika.v2.pot');
            Route::post('/baris/{baris_id}/tanam', [\App\Http\Controllers\PaprikaV2Controller::class, 'tanamMassal'])->name('paprika.v2.tanam.massal');
            Route::post('/pot/{pot_id}/action', [\App\Http\Controllers\PaprikaV2Controller::class, 'actionPot'])->name('paprika.v2.pot.action');
            Route::delete('/gh/{id}', [\App\Http\Controllers\PaprikaV2Controller::class, 'destroyGh'])->name('paprika.v2.gh.destroy');
            Route::delete('/baris/{id}', [\App\Http\Controllers\PaprikaV2Controller::class, 'destroyBaris'])->name('paprika.v2.baris.destroy');
            Route::delete('/pot/{id}', [\App\Http\Controllers\PaprikaV2Controller::class, 'destroyPot'])->name('paprika.v2.pot.destroy');
        });
    });
});




