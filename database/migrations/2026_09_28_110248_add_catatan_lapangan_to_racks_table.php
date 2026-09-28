<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('racks', function (Blueprint $table) {
            $table->text('catatan_lapangan')->nullable();
            $table->timestamp('catatan_lapangan_updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('racks', function (Blueprint $table) {
            $table->dropColumn(['catatan_lapangan', 'catatan_lapangan_updated_at']);
        });
    }
};
