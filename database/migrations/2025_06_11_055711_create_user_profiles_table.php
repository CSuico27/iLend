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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('birthdate')->nullable();
            $table->enum('gender' ,['Male', 'Female', 'Not Specified'])->nullable();
            $table->string('address')->nullable();
            $table->string('picture')->nullable();
            $table->string('brgy_clearance')->nullable();
            $table->string('valid_id')->nullable();
            $table->boolean('is_applied_for_membership')->default(0);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
