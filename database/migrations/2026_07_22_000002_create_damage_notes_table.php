<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('damage_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hole_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('plant_name')->nullable();       // tanaman yang rusak
            $table->string('damage_type')->default('umum'); // umum, hama, penyakit, kekeringan, dll
            $table->text('description');                    // catatan kerusakan
            $table->string('severity')->default('sedang');  // ringan, sedang, berat
            $table->string('location')->nullable();         // GH > Rak > Lubang (denormalized for speed)
            $table->timestamp('damaged_at')->useCurrent();  // waktu kerusakan
            $table->text('action_taken')->nullable();       // tindakan yang sudah diambil
            $table->string('status')->default('open');      // open, handling, resolved
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('damage_notes');
    }
};
