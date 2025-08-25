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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->string('action');                
            $table->string('model');                 
            $table->unsignedBigInteger('model_id'); 
            $table->json('changes')->nullable();     
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('status')->nullable();    
            $table->unsignedBigInteger('loan_id')->nullable();   
            $table->unsignedBigInteger('ledger_id')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
