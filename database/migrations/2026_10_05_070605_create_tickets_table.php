<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['OPEN', 'PROGRESS', 'CLOSED'])->default('OPEN');

            $table->string('tipe_penanganan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Client
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete(); // CS
            $table->foreignId('module_system_id')->nullable()->constrained('module_systems')->nullOnDelete();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};