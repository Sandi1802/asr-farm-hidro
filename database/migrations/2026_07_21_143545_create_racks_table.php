<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('racks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('greenhouse_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->float('ppm_level')->nullable();
            $table->float('ph_level')->nullable();
            $table->timestamp('ppm_ph_updated_at')->nullable();
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('racks');
    }
};
