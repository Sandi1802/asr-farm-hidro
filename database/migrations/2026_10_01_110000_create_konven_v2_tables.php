<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. konven_lahans ──────────────────────────────────────────────────
        if (!Schema::hasTable('konven_lahans')) {
            Schema::create('konven_lahans', function (Blueprint $table) {
                $table->id();
                $table->string('nama');           // Lahan 1, Lahan 2, …
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // ── 2. konven_posisis ─────────────────────────────────────────────────
        if (!Schema::hasTable('konven_posisis')) {
            Schema::create('konven_posisis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lahan_id')
                      ->constrained('konven_lahans')
                      ->onDelete('cascade');
                $table->string('nama');           // Atas, Bawah, Kiri, Kanan, …
                $table->char('prefix_kode', 1);  // A, B, C, …
                $table->timestamps();
            });
        }

        // ── 3. konven_kodes ───────────────────────────────────────────────────
        if (!Schema::hasTable('konven_kodes')) {
            Schema::create('konven_kodes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('posisi_id')
                      ->constrained('konven_posisis')
                      ->onDelete('cascade');
                $table->string('kode');           // A1, A2, B1, …
                $table->integer('nomor_urut');
                $table->timestamps();
            });
        }

        // ── 4. konven_zonas ───────────────────────────────────────────────────
        if (!Schema::hasTable('konven_zonas')) {
            Schema::create('konven_zonas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('kode_id')
                      ->constrained('konven_kodes')
                      ->onDelete('cascade');
                $table->string('nama');           // Zona 1, Zona 2, …
                $table->timestamps();
            });
        }

        // ── 5. konven_bedengans ───────────────────────────────────────────────
        if (!Schema::hasTable('konven_bedengans')) {
            Schema::create('konven_bedengans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('zona_id')
                      ->constrained('konven_zonas')
                      ->onDelete('cascade');
                $table->integer('nomor');
                $table->string('nama_display')->nullable();
                $table->integer('jumlah_lubang_rencana')->default(0);
                $table->timestamps();
            });
        }

        // ── 6. konven_lubang_tanams ───────────────────────────────────────────
        if (!Schema::hasTable('konven_lubang_tanams')) {
            Schema::create('konven_lubang_tanams', function (Blueprint $table) {
                $table->id();
                $table->foreignId('bedengan_id')
                      ->constrained('konven_bedengans')
                      ->onDelete('cascade');
                $table->integer('nomor_lubang');
                $table->string('status')->default('kosong'); // kosong|ditanam|panen|rusak
                $table->string('plant_name')->nullable();
                $table->date('planted_at')->nullable();
                $table->date('estimated_harvest_at')->nullable();
                $table->date('harvested_at')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        // ── 7. konven_tanam_logs ──────────────────────────────────────────────
        if (!Schema::hasTable('konven_tanam_logs')) {
            Schema::create('konven_tanam_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lubang_id')
                      ->constrained('konven_lubang_tanams')
                      ->onDelete('cascade');
                $table->string('action_type'); // tanam|panen|rusak
                $table->string('plant_name')->nullable();
                $table->jsonb('details')->nullable();
                $table->foreignId('created_by')
                      ->nullable()
                      ->constrained('users')
                      ->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('konven_tanam_logs');
        Schema::dropIfExists('konven_lubang_tanams');
        Schema::dropIfExists('konven_bedengans');
        Schema::dropIfExists('konven_zonas');
        Schema::dropIfExists('konven_kodes');
        Schema::dropIfExists('konven_posisis');
        Schema::dropIfExists('konven_lahans');
    }
};
