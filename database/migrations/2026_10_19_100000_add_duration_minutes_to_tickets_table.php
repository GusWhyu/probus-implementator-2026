<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->integer('duration_minutes')->nullable()->after('closed_at');
        });

        // Initialize duration_minutes for all closed tickets
        $closedTickets = DB::table('tickets')
            ->where('status', 'CLOSED')
            ->whereNotNull('closed_at')
            ->get();

        foreach ($closedTickets as $ticket) {
            $start = $ticket->handle_at ?? $ticket->created_at;
            if ($start && $ticket->closed_at) {
                $mins = Carbon::parse($ticket->closed_at)->diffInMinutes(Carbon::parse($start));
                DB::table('tickets')->where('id', $ticket->id)->update([
                    'duration_minutes' => $mins
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
        });
    }
};
