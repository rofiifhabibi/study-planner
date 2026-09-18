<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('recurrence_frequency')->nullable()->after('color');
            $table->unsignedSmallInteger('recurrence_interval')->default(1)->after('recurrence_frequency');
            $table->string('recurrence_days')->nullable()->after('recurrence_interval');
            $table->date('recurrence_until')->nullable()->after('recurrence_days');
            $table->unsignedInteger('recurrence_count')->nullable()->after('recurrence_until');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn([
                'recurrence_frequency',
                'recurrence_interval',
                'recurrence_days',
                'recurrence_until',
                'recurrence_count',
            ]);
        });
    }
};
