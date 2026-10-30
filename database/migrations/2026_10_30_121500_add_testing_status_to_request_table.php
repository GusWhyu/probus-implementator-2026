<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('request', function (Blueprint $table) {
            $table->string('testing_status')->nullable()->after('approval_note');
            $table->text('testing_note')->nullable()->after('testing_status');
            $table->foreignId('tested_by')->nullable()->after('testing_note')->constrained('users')->nullOnDelete();
            $table->timestamp('tested_at')->nullable()->after('tested_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('request', function (Blueprint $table) {
            $table->dropForeign(['tested_by']);
            $table->dropColumn(['testing_status', 'testing_note', 'tested_by', 'tested_at']);
        });
    }
};
