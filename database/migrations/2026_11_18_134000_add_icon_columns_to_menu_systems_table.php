<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_systems', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_systems', 'icon_mode')) {
                $table->string('icon_mode', 20)->default('preset')->nullable()->after('description');
            }
            if (!Schema::hasColumn('menu_systems', 'icon')) {
                $table->string('icon', 255)->nullable()->after('icon_mode');
            }
        });

        // Migrasi data: ekstrak ikon lama dari komentar HTML di dalam deskripsi ke kolom baru
        $menus = DB::table('menu_systems')->get();
        foreach ($menus as $m) {
            $rawDesc = $m->description ?? '';
            $icon = null;
            $iconMode = 'preset';
            $cleanDesc = $rawDesc;

            if (preg_match('/<!--icon:([^>]+)-->/', $rawDesc, $matches)) {
                $icon = trim($matches[1]);
                $cleanDesc = trim(str_replace($matches[0], '', $rawDesc));

                if (str_starts_with($icon, '/uploads/') || preg_match('/\.(png|svg|jpg|jpeg|webp)$/i', $icon)) {
                    $iconMode = 'upload';
                } elseif (in_array(strtolower($icon), [
                    'settings', 'utensils-crossed', 'store', 'calculator', 'package', 
                    'credit-card', 'wallet', 'book-open', 'bar-chart-3', 'printer', 
                    'sparkles', 'laptop', 'shield', 'users', 'smartphone'
                ])) {
                    $iconMode = 'preset';
                } else {
                    $iconMode = 'custom';
                }
            }

            DB::table('menu_systems')->where('id', $m->id)->update([
                'icon_mode' => $iconMode,
                'icon' => $icon,
                'description' => !empty($cleanDesc) ? $cleanDesc : null,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_systems', function (Blueprint $table) {
            $columnsToDrop = [];
            if (Schema::hasColumn('menu_systems', 'icon')) {
                $columnsToDrop[] = 'icon';
            }
            if (Schema::hasColumn('menu_systems', 'icon_mode')) {
                $columnsToDrop[] = 'icon_mode';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
