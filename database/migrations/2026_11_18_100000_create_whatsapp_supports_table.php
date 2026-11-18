<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateWhatsappSupportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whatsapp_supports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('badge')->nullable();
            $table->string('description')->nullable();
            $table->string('phone_number');
            $table->string('icon')->default('fa-solid fa-headset');
            $table->string('badge_color')->default('emerald');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed 3 kontak default dari data awal
        DB::table('whatsapp_supports')->insert([
            [
                'name' => 'CS Probussystem',
                'badge' => 'CS Support',
                'description' => 'Customer Service & Bantuan Teknis',
                'phone_number' => '+62 819-1087-5514',
                'icon' => 'fa-solid fa-headset',
                'badge_color' => 'emerald',
                'order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'E Commerce Probussystem',
                'badge' => 'E-Commerce',
                'description' => 'Online Booking, Channel & Integrasi',
                'phone_number' => '+62 878-6144-6124',
                'icon' => 'fa-solid fa-cart-shopping',
                'badge_color' => 'sky',
                'order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Trainer Probussystem',
                'badge' => 'Trainer',
                'description' => 'Panduan Operasional & Training Modul',
                'phone_number' => '+62 819-1087-5514',
                'icon' => 'fa-solid fa-graduation-cap',
                'badge_color' => 'indigo',
                'order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('whatsapp_supports');
    }
}
