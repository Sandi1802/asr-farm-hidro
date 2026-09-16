<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paprika_fertilizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paprika_greenhouse_id')->constrained()->cascadeOnDelete();
            $table->string('fertilizer_name');
            $table->string('dose');
            $table->date('date');
            $table->string('worker_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paprika_fertilizations');
    }
};
