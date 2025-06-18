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
        Schema::table('loans', function (Blueprint $table) {
            $table->decimal('interest_amount', 10, 2)->after('payment_frequency');
            $table->decimal('total_payment', 10, 2)->after('interest_amount');
            $table->decimal('payment_per_term', 10, 2)->after('total_payment');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending')->after('payment_per_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['interest_amount', 'total_payment', 'payment_per_term', 'status']);
        });
    }
};
