<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plant_types', function (Blueprint $table) {
            $table->dropColumn('harvested_by');

            $table->integer('semai_ppm')->nullable()->after('semai_days');
            $table->decimal('semai_ph', 4, 2)->nullable()->after('semai_ppm');

            $table->integer('tanam_ppm')->nullable()->after('tanam_days');
            $table->decimal('tanam_ph', 4, 2)->nullable()->after('tanam_ppm');

            $table->integer('remaja_ppm')->nullable()->after('remaja_days');
            $table->decimal('remaja_ph', 4, 2)->nullable()->after('remaja_ppm');

            $table->integer('dewasa_ppm')->nullable()->after('dewasa_days');
            $table->decimal('dewasa_ph', 4, 2)->nullable()->after('dewasa_ppm');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plant_types', function (Blueprint $table) {
            $table->string('harvested_by')->nullable();
            
            $table->dropColumn([
                'semai_ppm', 'semai_ph',
                'tanam_ppm', 'tanam_ph',
                'remaja_ppm', 'remaja_ph',
                'dewasa_ppm', 'dewasa_ph'
            ]);
        });
    }
};
