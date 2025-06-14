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
        Schema::create('seminar_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->json('user_ids');
            $table->string('seminar_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['Scheduled', 'Completed'])->default('Scheduled');
            $table->string('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seminar_schedules');
    }
};
