<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TicketSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $users = [3, 4, 6, 9]; // CS Users
        
        $statuses = ['OPEN', 'PROGRESS', 'CLOSED', 'URGENT'];

        // Generate tickets for the last 30 days
        for ($i = 0; $i < 25; $i++) {
            $created_at = Carbon::now()->subDays(rand(0, 30));
            $status = $statuses[array_rand($statuses)];
            
            Ticket::create([
                'ticket_number' => 'TCK-' . date('Ym') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'title' => 'Permasalahan koneksi / error fitur ' . Str::random(4),
                'description' => 'Pelanggan melaporkan kendala pada aplikasi. Mohon segera dicek dan diselesaikan.',
                'status' => $status,
                'priority' => ($status == 'URGENT') ? 'High' : 'Normal',
                'tipe_penanganan' => 'Remote',
                'user_id' => 2, // Client user ID (dummy)
                'assignee_id' => $users[array_rand($users)], // Assigned to CS
                'outlet_id' => \App\Models\Outlet::first()->id ?? null,
                'kategori_id' => \App\Models\Kategori::first()->id ?? null,
                'tag_id' => \App\Models\Tag::first()->id ?? null,
                'due_date' => $created_at->copy()->addDays(2),
                'closed_at' => ($status == 'CLOSED') ? $created_at->copy()->addHours(rand(1, 48)) : null,
                'created_at' => $created_at,
                'updated_at' => $created_at,
            ]);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
