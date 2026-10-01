<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('konven_kodes', function (Blueprint $table) {
            if (!Schema::hasColumn('konven_kodes', 'lahan_id')) {
                $table->foreignId('lahan_id')
                      ->nullable()
                      ->constrained('konven_lahans')
                      ->onDelete('cascade');
            }

            if (!Schema::hasColumn('konven_kodes', 'label_posisi')) {
                $table->string('label_posisi')->nullable()->after('kode'); // e.g. Atas, Bawah
            }
        });
    }

    public function down(): void
    {
        Schema::table('konven_kodes', function (Blueprint $table) {
            if (Schema::hasColumn('konven_kodes', 'lahan_id')) {
                $table->dropForeign(['lahan_id']);
                $table->dropColumn('lahan_id');
            }

            if (Schema::hasColumn('konven_kodes', 'label_posisi')) {
                $table->dropColumn('label_posisi');
            }
        });
    }
};
