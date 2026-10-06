<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ModuleSystem;
use App\Models\PerformanceCategory;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Ticket::truncate();
        PerformanceCategory::truncate();
        ModuleSystem::truncate();

        // 1. Module Systems
        $modules = [
            ['name' => 'Front Office (FO)', 'description' => 'Modul Resepsionis & Reservasi'],
            ['name' => 'Point of Sales (POS)', 'description' => 'Modul Kasir & Resto'],
            ['name' => 'Accounting', 'description' => 'Modul Keuangan & Akuntansi'],
            ['name' => 'Inventory', 'description' => 'Modul Gudang & Stok'],
            ['name' => 'HRIS', 'description' => 'Modul Kepegawaian & Payroll'],
        ];
        foreach ($modules as $m) {
            ModuleSystem::create($m);
        }

        // 2. Performance Categories
        $categories = [
            ['name' => 'Produktivitas & Resolusi Tiket', 'weight' => 30.00, 'description' => 'Kuantitas penyelesaian masalah'],
            ['name' => 'Kualitas Solusi', 'weight' => 20.00, 'description' => 'Ketepatan pemecahan masalah'],
            ['name' => 'Kecepatan Respon', 'weight' => 20.00, 'description' => 'Waktu tanggap terhadap keluhan'],
            ['name' => 'Sikap & Komunikasi', 'weight' => 15.00, 'description' => 'Etika pelayanan'],
            ['name' => 'Kepuasan Klien (Survey)', 'weight' => 15.00, 'description' => 'Rating dari pengguna langsung'],
        ];
        foreach ($categories as $c) {
            PerformanceCategory::create($c);
        }

        // 3. Dummy Tickets
        $users = [3, 4, 6, 9]; // CS Users
        $statuses = ['OPEN', 'PROGRESS', 'CLOSED', 'URGENT'];
        $mod_ids = ModuleSystem::pluck('id')->toArray();

        for ($i = 0; $i < 30; $i++) {
            $created_at = Carbon::now()->subDays(rand(0, 30));
            $status = $statuses[array_rand($statuses)];
            
            Ticket::create([
                'ticket_number' => 'TCK-' . date('Ym') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'title' => 'Permasalahan error ' . Str::random(4),
                'description' => 'Terdapat kendala dari pihak klien. Segera ditangani.',
                'status' => $status,
                'priority' => ($status == 'URGENT') ? 'High' : 'Normal',
                'tipe_penanganan' => 'Remote',
                'user_id' => 2, // Dummy client
                'assignee_id' => $users[array_rand($users)], // CS
                'module_system_id' => $mod_ids[array_rand($mod_ids)],
                'due_date' => $created_at->copy()->addDays(2),
                'closed_at' => ($status == 'CLOSED') ? $created_at->copy()->addHours(rand(1, 48)) : null,
                'created_at' => $created_at,
                'updated_at' => $created_at,
            ]);
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
