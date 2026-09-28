<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('racks', function (Blueprint $table) {
            $table->decimal('suhu', 5, 2)->nullable()->after('ph_level');
        });
    }

    public function down()
    {
        Schema::table('racks', function (Blueprint $table) {
            $table->dropColumn('suhu');
        });
    }
};
