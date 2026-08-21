<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change the role_agri to support new roles
        // We will just map existing admin to it_admin, and pegawai to produksi
        DB::table('users')->where('role_agri', 'admin')->update(['role_agri' => 'it_admin']);
        DB::table('users')->where('role_agri', 'pegawai')->update(['role_agri' => 'produksi']);
        DB::table('users')->where('role_agri', 'manajer')->update(['role_agri' => 'atasan']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back
        DB::table('users')->where('role_agri', 'it_admin')->update(['role_agri' => 'admin']);
        DB::table('users')->where('role_agri', 'produksi')->update(['role_agri' => 'pegawai']);
        DB::table('users')->where('role_agri', 'atasan')->update(['role_agri' => 'manajer']);
    }
};
