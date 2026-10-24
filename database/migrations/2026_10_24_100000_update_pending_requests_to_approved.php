<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
        // Update all pending requests to approved since requests do not require admin approval
        if (Schema::hasTable('request') && Schema::hasColumn('request', 'approval_status')) {
            DB::table('request')
                ->where('approval_status', 'pending')
                ->orWhereNull('approval_status')
                ->where('status_id', '!=', 4) // Don't alter closed if any
                ->update([
                    'approval_status' => 'approved',
                    'approval_date' => now(),
                    'approval_note' => 'Otomatis disetujui (tidak memerlukan approval admin)'
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No reverse operation needed
    }
};
