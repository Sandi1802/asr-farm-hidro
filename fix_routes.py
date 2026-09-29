with open('routes/web.php', 'r', encoding='utf-8') as f:
    content = f.read()

content = content.replace("Route::post('/tanam', [\App\Http\Controllers\KonvenKebunController::class, 'tanamStore']);", "Route::post('/tanam', [\App\Http\Controllers\KonvenKebunController::class, 'tanamStore'])->name('konvensional.kebun.tanam.store');")
content = content.replace("Route::post('/panen', [\App\Http\Controllers\KonvenKebunController::class, 'panenStore']);", "Route::post('/panen', [\App\Http\Controllers\KonvenKebunController::class, 'panenStore'])->name('konvensional.kebun.panen.store');")
content = content.replace("Route::post('/master-tanaman', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanamanStore']);", "Route::post('/master-tanaman', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanamanStore'])->name('konvensional.kebun.tanaman.store');")
content = content.replace("Route::delete('/master-tanaman/{id}', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanamanDestroy']);", "Route::delete('/master-tanaman/{id}', [\App\Http\Controllers\KonvenKebunController::class, 'masterTanamanDestroy'])->name('konvensional.kebun.tanaman.destroy');")

with open('routes/web.php', 'w', encoding='utf-8') as f:
    f.write(content)
