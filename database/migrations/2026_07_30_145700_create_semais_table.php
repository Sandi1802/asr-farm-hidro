<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plant_type_id')->nullable()->constrained('plant_types')->nullOnDelete();
            $table->string('plant_name');
            $table->integer('quantity')->default(1)->comment('Jumlah lubang/bibit yang disemai');
            $table->date('semai_date');
            $table->date('estimated_transfer_date')->nullable()->comment('Estimasi pindah ke GH');
            $table->foreignId('target_greenhouse_id')->nullable()->constrained('greenhouses')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->enum('status', ['aktif', 'sudah_pindah', 'gagal'])->default('aktif');
            $table->date('transferred_date')->nullable()->comment('Tanggal aktual pindah ke GH');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semais');
    }
};
