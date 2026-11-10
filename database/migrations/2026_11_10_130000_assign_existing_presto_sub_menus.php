<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\MenuSystem;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Hubungkan Order Taker & Kasir (id: 2) dan QRIS (id: 3) sebagai sub-menu di bawah Transaksi (id: 35)
        $transaksi = MenuSystem::find(35);
        if ($transaksi && $transaksi->kategori_id == 2) {
            $orderTaker = MenuSystem::find(2);
            if ($orderTaker && $orderTaker->kategori_id == 2) {
                $orderTaker->update([
                    'parent_id' => 35,
                    'order' => 1,
                ]);
            }

            $qris = MenuSystem::find(3);
            if ($qris && $qris->kategori_id == 2) {
                $qris->update([
                    'parent_id' => 35,
                    'order' => 2,
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
        MenuSystem::whereIn('id', [2, 3])->update([
            'parent_id' => null,
        ]);
    }
};

