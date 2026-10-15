<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MergeUrgentStatusIntoOpen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update all requests with status_id = 1 (urgent) to status_id = 2 (Open)
        DB::table('request')->where('status_id', 1)->update(['status_id' => 2]);

        // Update any requestlog records if any
        DB::table('requestlog')->where('status_id', 1)->update(['status_id' => 2]);

        // Clean up status with id 1 if no foreign key references remain
        $reqCount = DB::table('request')->where('status_id', 1)->count();
        $logCount = DB::table('requestlog')->where('status_id', 1)->count();
        if ($reqCount === 0 && $logCount === 0) {
            DB::table('status')->where('id', 1)->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
