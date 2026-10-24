<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('tickets') && !Schema::hasColumn('tickets', 'origin_request_number')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('origin_request_number', 50)->nullable()->after('ticket_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'origin_request_number')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('origin_request_number');
            });
        }
    }
};
