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
    public function up()
    {
        if (!Schema::hasTable('asset_damage_notes')) {
            Schema::create('asset_damage_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('asset_name');
            $table->string('location')->nullable();
            $table->text('description');
            $table->string('severity')->default('sedang'); // ringan, sedang, berat
            $table->timestamp('damaged_at')->useCurrent();
            $table->text('action_taken')->nullable();
            $table->string('status')->default('open'); // open, handling, resolved
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_damage_notes');
    }
};
