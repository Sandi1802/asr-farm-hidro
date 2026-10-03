<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('paprika_ghs')) {
            Schema::create('paprika_ghs', function (Blueprint $table) {
                $table->id();
                $table->string('nama_gh');
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('paprika_baris')) {
            Schema::create('paprika_baris', function (Blueprint $table) {
                $table->id();
                $table->foreignId('gh_id')->constrained('paprika_ghs')->onDelete('cascade');
                $table->string('nama_baris');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('paprika_pots')) {
            Schema::create('paprika_pots', function (Blueprint $table) {
                $table->id();
                $table->foreignId('baris_id')->constrained('paprika_baris')->onDelete('cascade');
                $table->integer('nomor_pot');
                $table->string('status')->default('kosong'); // kosong, ditanam, panen, rusak
                $table->string('plant_name')->nullable();
                $table->date('planted_at')->nullable();
                $table->date('estimated_harvest_at')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('paprika_logs')) {
            Schema::create('paprika_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pot_id')->constrained('paprika_pots')->onDelete('cascade');
                $table->string('action_type'); // tanam, panen, rusak, pupuk, semprot
                $table->text('details')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('paprika_logs');
        Schema::dropIfExists('paprika_pots');
        Schema::dropIfExists('paprika_baris');
        Schema::dropIfExists('paprika_ghs');
    }
};
