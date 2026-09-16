<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('paprika_greenhouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity')->default(1000);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('paprika_greenhouses');
    }
};
