<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payment_records', function (Blueprint $table) {
            // ✅ Drop old column if it's numeric
            if (Schema::hasColumn('payment_records', 'last_payment')) {
                $table->dropColumn('last_payment');
            }

            // ✅ Add new JSON column for months
            $table->json('paid_months')->nullable()->after('amt_paid');
        });
    }

    public function down(): void
    {
        Schema::table('payment_records', function (Blueprint $table) {
            $table->dropColumn('paid_months');

            // Optionally restore numeric column if needed
            $table->decimal('last_payment', 10, 2)->default(0)->after('amt_paid');
        });
    }
};
