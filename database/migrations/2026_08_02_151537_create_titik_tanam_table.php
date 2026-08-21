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
    public function up(): void
    {
        Schema::create('titik_tanam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bedengan_id')->constrained('bedengan')->onDelete('cascade');
            $table->string('nama_titik');
            $table->string('nama_tanaman')->nullable();
            $table->string('status')->default('kosong'); // kosong, persiapan, ditanam, siap_panen, panen, rusak
            $table->timestamp('tanggal_tanam')->nullable();
            $table->timestamp('tanggal_panen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('titik_tanam');
    }
};
