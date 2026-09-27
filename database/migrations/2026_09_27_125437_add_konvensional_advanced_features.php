<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new columns to titik_tanam table
        Schema::table('titik_tanam', function (Blueprint $table) {
            $table->string('tanaman_sekunder')->nullable()->after('nama_tanaman');
            $table->timestamp('kosong_sejak')->nullable()->after('tanggal_panen');
        });

        // Create riwayat_panen_konvensional table
        Schema::create('riwayat_panen_konvensional', function (Blueprint $table) {
            $table->id();
            $table->foreignId('titik_tanam_id')->constrained('titik_tanam')->onDelete('cascade');
            $table->string('jenis_panen'); // 'petik' atau 'cabut'
            $table->string('jumlah_kg')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_panen_konvensional');

        Schema::table('titik_tanam', function (Blueprint $table) {
            $table->dropColumn(['tanaman_sekunder', 'kosong_sejak']);
        });
    }
};
