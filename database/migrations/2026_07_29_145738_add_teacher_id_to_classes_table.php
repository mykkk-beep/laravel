<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (! Schema::hasColumn('classes', 'teacher_id')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('cascade');
                // or ->constrained('teachers') if you have a separate teachers table
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('classes', 'teacher_id')) {
            Schema::table('classes', function (Blueprint $table) {
                $table->dropForeign(['teacher_id']);
                $table->dropColumn('teacher_id');
            });
        }
    }
};
