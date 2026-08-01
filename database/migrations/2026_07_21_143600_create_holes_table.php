<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('holes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('row_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('plant_name')->nullable();
            $table->string('status')->default('kosong'); // kosong, ditanam, rusak, panen
            $table->timestamp('planted_at')->nullable();
            $table->timestamp('harvested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('holes');
    }
};
