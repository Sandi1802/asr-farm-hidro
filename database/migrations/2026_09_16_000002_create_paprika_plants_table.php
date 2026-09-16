<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paprika_plants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paprika_greenhouse_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->enum('status', ['kosong', 'ditanam', 'proses', 'panen', 'gagal'])->default('kosong');
            $table->dateTime('planted_at')->nullable();
            $table->dateTime('harvested_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paprika_plants');
    }
};
