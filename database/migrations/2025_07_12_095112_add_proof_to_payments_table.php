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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('proof_of_billing')->nullable()->after('amount');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending')->after('proof_of_billing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('proof_of_billing');
            $table->dropColumn('status');
        });
    }
};
