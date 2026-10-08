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
        \App\Models\DataImage::truncate();
        \App\Models\Komentar::truncate();
        \App\Models\UpdateSystem::truncate();
        \App\Models\RequestLog::truncate();

        // 1. Module Systems
        $modules = [
            ['name' => 'FO', 'description' => 'Front Office'],
            ['name' => 'POS', 'description' => 'Point of Sales'],
            ['name' => 'STORE', 'description' => 'Store / Gudang'],
            ['name' => 'Cost Control', 'description' => 'Cost Control'],
            ['name' => 'Accounting', 'description' => 'Keuangan & Akuntansi'],
            ['name' => 'Ezzy', 'description' => 'Ezzy'],
            ['name' => 'PR/PO Online', 'description' => 'Purchasing Online'],
            ['name' => 'OT', 'description' => 'Overtime'],
        ];
        foreach ($modules as $m) {
            ModuleSystem::create($m);
        }

        // 2. Performance Categories
        $categories = [
            ['name' => 'Jujur', 'weight' => 0.00, 'description' => 'Kejujuran dan integritas'],
            ['name' => 'Tanggung Jawab', 'weight' => 0.00, 'description' => 'Tanggung jawab terhadap tugas'],
            ['name' => 'Visioner', 'weight' => 0.00, 'description' => 'Kemampuan berinovasi dan berpikir maju'],
            ['name' => 'Disiplin', 'weight' => 0.00, 'description' => 'Ketaatan aturan dan kedisiplinan'],
            ['name' => 'Kerjasama', 'weight' => 0.00, 'description' => 'Bekerja sama dalam tim'],
            ['name' => 'Adil', 'weight' => 0.00, 'description' => 'Bersikap objektif dan tidak memihak'],
            ['name' => 'Peduli', 'weight' => 0.00, 'description' => 'Kepedulian dan empati'],
        ];
        foreach ($categories as $c) {
            PerformanceCategory::create($c);
        }

        // 3. Dummy Tickets
        $users = [3, 4, 6, 9]; // CS Users
        $statuses = ['OPEN', 'PROGRESS', 'CLOSED'];
        $mod_ids = ModuleSystem::pluck('id')->toArray();
        $sys_ids = \App\Models\Kategori::pluck('id')->toArray();
        
        $titles = [
            'Tidak bisa login ke sistem',
            'Data laporan tidak sinkron',
            'Printer kasir tidak merespon',
            'Fitur ekspor Excel error',
            'Sistem terasa lambat saat jam sibuk',
            'Lupa password akun administrator',
            'Error 500 saat simpan data',
            'Tampilan modul rusak di mobile',
            'Request penambahan hak akses',
            'Gagal cetak struk pembayaran'
        ];

        for ($i = 0; $i < 30; $i++) {
            $created_at = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23));
            $status = $statuses[array_rand($statuses)];
            $tipe = ['Remote', 'On Site', 'Office', 'Piket'];
            
            Ticket::create([
                'ticket_number' => 'TCK-' . date('Ym') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'title' => $titles[array_rand($titles)] . ' - ' . Str::random(3),
                'description' => 'Mohon bantuannya, saat ini pengguna melaporkan kendala pada modul terkait. Detail lebih lanjut akan disampaikan via remote atau telepon. Harap segera dicek karena mengganggu operasional.',
                'status' => $status,
                'tipe_penanganan' => $tipe[array_rand($tipe)],
                'user_id' => 2, // Dummy client
                'advisor_id' => $users[array_rand($users)], // CS
                'module_system_id' => empty($mod_ids) ? null : $mod_ids[array_rand($mod_ids)],
                'system' => empty($sys_ids) ? null : $sys_ids[array_rand($sys_ids)],
                'link_id' => Str::random(10),
                'due_date' => $created_at->copy()->addDays(2),
                'closed_at' => ($status == 'CLOSED') ? $created_at->copy()->addHours(rand(1, 48)) : null,
                'created_at' => $created_at,
                'updated_at' => $created_at,
            ]);
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
