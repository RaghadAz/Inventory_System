<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            if (!Schema::hasColumn('debts', 'type')) {
                $table->string('type')->default('customer')->after('person_name');
            }
            if (!Schema::hasColumn('debts', 'reason')) {
                $table->string('reason')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('debts', 'notes')) {
                $table->text('notes')->nullable()->after('reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->dropColumn(['type', 'reason', 'notes']);
        });
    }
};