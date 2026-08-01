<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PlantType;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('plant_types', 'semai_days')) {
            Schema::table('plant_types', function (Blueprint $table) {
                $table->integer('semai_days')->default(0)->after('growth_days');
                $table->integer('tanam_days')->default(0)->after('semai_days');
                $table->integer('remaja_days')->default(0)->after('tanam_days');
                $table->integer('dewasa_days')->default(0)->after('remaja_days');
                $table->string('harvested_by')->nullable()->after('dewasa_days');
            });
        }

        // Otomatis bagi growth_days yang ada menjadi 4 fase untuk data lama
        PlantType::where('growth_days', '>', 0)->get()->each(function ($plant) {
            $total = $plant->growth_days;
            // Distribusi: Semai 20%, Tanam 30%, Remaja 25%, Dewasa 25%
            $semai  = max(1, (int) round($total * 0.20));
            $tanam  = max(1, (int) round($total * 0.30));
            $remaja = max(1, (int) round($total * 0.25));
            $dewasa = $total - $semai - $tanam - $remaja;
            if ($dewasa < 1) $dewasa = 1;

            $plant->update([
                'semai_days'  => $semai,
                'tanam_days'  => $tanam,
                'remaja_days' => $remaja,
                'dewasa_days' => $dewasa,
            ]);
        });
    }

    public function down()
    {
        Schema::table('plant_types', function (Blueprint $table) {
            $table->dropColumn(['semai_days', 'tanam_days', 'remaja_days', 'dewasa_days', 'harvested_by']);
        });
    }
};
