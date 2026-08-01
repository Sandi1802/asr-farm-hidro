<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('plant_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Nama tanaman: Pakcoy, Selada, dll.
            $table->integer('growth_days')->default(30); // Durasi tumbuh dalam hari
            $table->string('description')->nullable();   // Keterangan tambahan
            $table->string('color')->nullable();         // Warna badge opsional
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plant_types');
    }
};
