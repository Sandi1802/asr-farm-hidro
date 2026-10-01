<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('konven_perawatans')) {
            Schema::create('konven_perawatans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bedengan_id')->constrained('konven_bedengans')->onDelete('cascade');
                $table->string('jenis'); // pemupukan, penyemprotan
                $table->string('nama_bahan')->nullable();
                $table->string('dosis')->nullable();
                $table->date('tanggal');
                $table->text('keterangan')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('konven_perawatans');
    }
};
