<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Lahan (Atas, Bawah)
        Schema::create('konven_lahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // "Atas", "Bawah"
            $table->timestamps();
        });

        // 2. Zona (Atas hanya 1 zona, Bawah punya Zona A & Zona B)
        Schema::create('konven_zona', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lahan_id')->constrained('konven_lahan')->onDelete('cascade');
            $table->string('nama'); // "Atas", "Zona A", "Zona B"
            $table->timestamps();
        });

        // 3. Pola (A, B, C, ... J)
        Schema::create('konven_pola', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zona_id')->constrained('konven_zona')->onDelete('cascade');
            $table->string('nama'); // "A", "B", ...
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // 4. Bedeng
        Schema::create('konven_bedeng', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pola_id')->constrained('konven_pola')->onDelete('cascade');
            $table->string('kode')->unique(); // "ATAS-A-01", "BAWAH-ZA-A-01"
            $table->integer('nomor');
            $table->decimal('luas_m2', 8, 2)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 5. Master Tanaman
        Schema::create('konven_tanaman', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('varietas')->nullable();
            $table->integer('lama_hari_ke_panen'); // hari dari tanam sampai panen pertama
            $table->string('satuan_hasil')->default('kg'); // kg / ikat / buah
            $table->decimal('rata2_hasil_per_tanaman', 10, 3)->default(0); // untuk estimasi
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // 6. Tanam (satu siklus tanam pada satu bedeng)
        Schema::create('konven_tanam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bedeng_id')->constrained('konven_bedeng')->onDelete('cascade');
            $table->foreignId('tanaman_id')->constrained('konven_tanaman')->onDelete('cascade');
            $table->uuid('batch_id')->nullable(); // mengelompokkan tanam yang diinput bersamaan
            $table->date('tanggal_tanam');
            $table->integer('jumlah_tanam'); // populasi
            $table->string('jarak_tanam')->nullable();
            $table->string('sumber_benih')->nullable();
            $table->date('estimasi_tanggal_panen');
            $table->decimal('estimasi_hasil', 10, 2)->default(0);
            $table->enum('status', ['tumbuh', 'panen_sebagian', 'selesai', 'gagal'])->default('tumbuh');
            $table->text('catatan')->nullable();
            $table->string('alasan_gagal')->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('id')->on('users')->nullOnDelete();
        });

        // 7. Panen
        Schema::create('konven_panen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanam_id')->constrained('konven_tanam')->onDelete('cascade');
            $table->date('tanggal_panen');
            $table->decimal('jumlah_hasil', 10, 2);
            $table->string('satuan')->default('kg');
            $table->enum('kualitas', ['A', 'B', 'C'])->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('konven_panen');
        Schema::dropIfExists('konven_tanam');
        Schema::dropIfExists('konven_tanaman');
        Schema::dropIfExists('konven_bedeng');
        Schema::dropIfExists('konven_pola');
        Schema::dropIfExists('konven_zona');
        Schema::dropIfExists('konven_lahan');
    }
};
