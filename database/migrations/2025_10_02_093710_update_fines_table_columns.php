<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ✅ Ensure 'breakdown' is not null before changing
        DB::table('fines')->whereNull('breakdown')->update(['breakdown' => '[]']);

        Schema::table('fines', function (Blueprint $table) {
            // ❌ Drop old columns
            if (Schema::hasColumn('fines', 'item_name')) {
                $table->dropColumn('item_name');
            }
            if (Schema::hasColumn('fines', 'amount')) {
                $table->dropColumn('amount');
            }

            // ✏️ Rename breakdown to details_json
            if (Schema::hasColumn('fines', 'breakdown')) {
                $table->renameColumn('breakdown', 'details_json');
            }
        });

        // ✅ Change type to JSON if not already
        DB::statement("ALTER TABLE fines MODIFY details_json JSON NOT NULL");
    }

    public function down(): void
    {
        Schema::table('fines', function (Blueprint $table) {
            // Revert name
            if (Schema::hasColumn('fines', 'details_json')) {
                $table->renameColumn('details_json', 'breakdown');
            }

            // Recreate the dropped columns
            $table->string('item_name')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
        });

        // Revert JSON back to TEXT
        DB::statement("ALTER TABLE fines MODIFY breakdown TEXT NULL");
    }
};
