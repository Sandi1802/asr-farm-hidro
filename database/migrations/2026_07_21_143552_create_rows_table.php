<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rack_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rows');
    }
};
