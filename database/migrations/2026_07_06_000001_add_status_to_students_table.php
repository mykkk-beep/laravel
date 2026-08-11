<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('students', 'status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->enum('status', ['pending', 'enrolled', 'not_enrolled'])
                    ->default('pending')
                    ->after('class_room_id');
            });
        } else {
            DB::statement("ALTER TABLE students MODIFY status ENUM('pending', 'enrolled', 'not_enrolled') DEFAULT 'pending'");
        }

        DB::table('students')->whereNull('status')->update(['status' => 'pending']);
    }

    public function down(): void
    {
        if (Schema::hasColumn('students', 'status')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
