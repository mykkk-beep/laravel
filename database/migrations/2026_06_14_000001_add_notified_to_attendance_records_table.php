<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendance_records', 'notified')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->boolean('notified')->default(false)->after('notes');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendance_records', 'notified')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropColumn('notified');
            });
        }
    }
};
