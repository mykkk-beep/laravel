<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'student_id')) {
                $table->string('student_id')->nullable()->after('uuid');
            }

            if (! Schema::hasColumn('students', 'sex')) {
                $table->string('sex')->nullable()->after('name');
            }

            if (! Schema::hasColumn('students', 'mobile')) {
                $table->string('mobile')->nullable()->after('sex');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'student_id')) {
                $table->dropColumn('student_id');
            }

            if (Schema::hasColumn('students', 'sex')) {
                $table->dropColumn('sex');
            }

            if (Schema::hasColumn('students', 'mobile')) {
                $table->dropColumn('mobile');
            }
        });
    }
};
