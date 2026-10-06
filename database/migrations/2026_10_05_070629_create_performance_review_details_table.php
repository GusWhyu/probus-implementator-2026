<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_review_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('performance_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performance_category_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->comment('Score from 1 to 100 or 1 to 5 depending on the system');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_review_details');
    }
};