<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_notifications', function (Blueprint $table) {
            $table->text('teacher_reply')->nullable()->after('parent_reply');
        });
    }

    public function down(): void
    {
        Schema::table('student_notifications', function (Blueprint $table) {
            $table->dropColumn('teacher_reply');
        });
    }
};
