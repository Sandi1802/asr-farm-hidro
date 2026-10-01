<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update holes table
        DB::table('holes')
            ->where('plant_name', 'Caisim/Sawi')
            ->update(['plant_name' => 'Caisim']);

        // Update semais table
        DB::table('semais')
            ->where('plant_name', 'Caisim/Sawi')
            ->update(['plant_name' => 'Caisim']);

        // Update damage_notes table
        DB::table('damage_notes')
            ->where('plant_name', 'Caisim/Sawi')
            ->update(['plant_name' => 'Caisim']);

        // Update activities table
        DB::table('activities')
            ->where('description', 'LIKE', '%Caisim/Sawi%')
            ->update([
                'description' => DB::raw("REPLACE(description, 'Caisim/Sawi', 'Caisim')")
            ]);

        // Update maintenance_logs details JSON
        // Since details is stored as JSON text, we can use simple REPLACE for the exact string
        DB::table('maintenance_logs')
            ->whereRaw("details::text LIKE '%\"Caisim/Sawi\"%'")
            ->update([
                'details' => DB::raw("REPLACE(details::text, '\"Caisim/Sawi\"', '\"Caisim\"')::json")
            ]);
            
        // Update maintenance_logs notes
        DB::table('maintenance_logs')
            ->where('notes', 'LIKE', '%Caisim/Sawi%')
            ->update([
                'notes' => DB::raw("REPLACE(notes, 'Caisim/Sawi', 'Caisim')")
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reversing this accurately is not easily possible since "Caisim" could have been native Caisim.
    }
};
