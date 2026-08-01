<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role_agri')->default('pegawai')->after('role');
        });
        
        // Setup initial admin if table is not empty
        DB::table('users')->where('role', 'super_admin')->orWhere('role', 'admin')->update(['role_agri' => 'admin']);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role_agri');
        });
    }
};
