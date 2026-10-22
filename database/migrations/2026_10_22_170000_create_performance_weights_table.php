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
        if (!Schema::hasTable('performance_weights')) {
            Schema::create('performance_weights', function (Blueprint $table) {
                $table->id();
                $table->string('key', 50)->unique();
                $table->string('name', 255);
                $table->decimal('bobot', 5, 2)->default(0); // persenan bobot (misal: 30.00, 40.00, 30.00)
                $table->text('deskripsi')->nullable();
                $table->integer('urutan')->default(1);
                $table->timestamps();
            });

            // Seed default master bobot sesuai tabel standar perhitungan
            DB::table('performance_weights')->insert([
                [
                    'key' => 'ticket',
                    'name' => 'Nilai Tiket (Σ bobot × qty)',
                    'bobot' => 30.00,
                    'deskripsi' => 'Akumulasi skor modul tiket pengerjaan staff dibandingkan dengan skor tertinggi tim.',
                    'urutan' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'soft_skill',
                    'name' => 'Nilai Soft Skill',
                    'bobot' => 40.00,
                    'deskripsi' => 'Rata-rata penilaian kriteria kompetensi perilaku dan profesionalisme staff oleh atasan/supervisor.',
                    'urutan' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'key' => 'client_review',
                    'name' => 'Kepuasan Klien (Survey)',
                    'bobot' => 30.00,
                    'deskripsi' => 'Tingkat kepuasan layanan dan rating bintang survey langsung dari ulasan klien.',
                    'urutan' => 3,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
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
        Schema::dropIfExists('performance_weights');
    }
};
