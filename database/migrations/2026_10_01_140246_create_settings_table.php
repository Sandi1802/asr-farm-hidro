<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });

            // Insert default marquee text
            DB::table('settings')->insert([
                'key' => 'marquee_text',
                'value' => 'Pantau perkembangbiakan, produksi, dan operasional ASR FARM dengan mudah di sini.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
