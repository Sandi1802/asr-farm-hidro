<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('greenhouses', function (Blueprint $table) {
            $table->timestamp('last_sprayed_at')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('greenhouses', function (Blueprint $table) {
            $table->dropColumn('last_sprayed_at');
        });
    }
};
