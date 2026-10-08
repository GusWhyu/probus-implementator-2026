<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TicketSlaTimer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:sla-timer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for OPEN tickets older than 1 hour and returns them to pool/sender';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $expiredTickets = \App\Models\Ticket::where('status', 'OPEN')
            ->whereNotNull('advisor_id')
            ->where('created_at', '<=', now()->subHour())
            ->get();

        foreach ($expiredTickets as $ticket) {
            $ticket->update([
                'advisor_id' => null
            ]);
            
            \App\Models\TicketLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->user_id, // System/Owner reset
                'action' => 'SLA_TIMEOUT',
                'description' => 'Tiket dikembalikan ke pengirim karena tidak ditangani dalam 1 jam.',
                'created_at' => now(),
            ]);
        }

        $this->info("Processed " . $expiredTickets->count() . " expired tickets.");
        return 0;
    }
}
