<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('attendance_records', 'qr_code')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->string('qr_code')->after('notified');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('attendance_records', 'qr_code')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropColumn('qr_code');
            });
        }
    }
};
