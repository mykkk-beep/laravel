<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // No-op migration: intentionally left empty to avoid altering attendance_records in this environment.
        return;
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance_records', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
                $table->unique(['student_id', 'subject_id', 'date']);
            }
        });
    }
};
