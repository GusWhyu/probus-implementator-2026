<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_review_module_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_review_id')->name('p_rev_id')->constrained('performance_reviews')->cascadeOnDelete();
            $table->foreignId('module_system_id')->constrained()->cascadeOnDelete();
            $table->integer('ticket_count')->default(0);
            $table->decimal('module_score', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_module_details');
    }
};