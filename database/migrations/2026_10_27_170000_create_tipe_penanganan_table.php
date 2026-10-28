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
        if (!Schema::hasTable('tipe_penanganan')) {
            Schema::create('tipe_penanganan', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->unique();
                $table->text('keterangan')->nullable();
                $table->string('icon')->default('fa-solid fa-headset')->nullable();
                $table->string('warna')->default('blue')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Seed default records
            $defaults = [
                [
                    'nama' => 'On Site',
                    'keterangan' => 'Penanganan langsung di lokasi atau outlet client',
                    'icon' => 'fa-solid fa-location-dot',
                    'warna' => 'emerald',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Office',
                    'keterangan' => 'Penanganan dari kantor / internal support',
                    'icon' => 'fa-solid fa-building',
                    'warna' => 'blue',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Online',
                    'keterangan' => 'Penanganan secara remote / online session',
                    'icon' => 'fa-solid fa-headset',
                    'warna' => 'purple',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Piket',
                    'keterangan' => 'Penanganan di luar jam kantor / jadwal piket',
                    'icon' => 'fa-solid fa-calendar-check',
                    'warna' => 'amber',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'TRAINING / Retraining ONLINE',
                    'keterangan' => 'Sesi pelatihan / training online sistem pelanggan',
                    'icon' => 'fa-solid fa-chalkboard-user',
                    'warna' => 'indigo',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama' => 'Training 4 Traner',
                    'keterangan' => 'Sesi pelatihan khusus trainer / internal training',
                    'icon' => 'fa-solid fa-graduation-cap',
                    'warna' => 'teal',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('tipe_penanganan')->insert($defaults);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipe_penanganan');
    }
};
