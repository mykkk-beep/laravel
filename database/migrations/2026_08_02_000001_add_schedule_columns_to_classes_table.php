<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (! Schema::hasColumn('classes', 'name')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('classes', 'classroom')) {
                $table->string('classroom')->nullable();
            }

            if (! Schema::hasColumn('classes', 'date')) {
                $table->date('date')->nullable();
            }

            if (! Schema::hasColumn('classes', 'time')) {
                $table->time('time')->nullable();
            }

            if (! Schema::hasColumn('classes', 'end_time')) {
                $table->time('end_time')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'end_time')) {
                $table->dropColumn('end_time');
            }

            if (Schema::hasColumn('classes', 'time')) {
                $table->dropColumn('time');
            }

            if (Schema::hasColumn('classes', 'date')) {
                $table->dropColumn('date');
            }

            if (Schema::hasColumn('classes', 'classroom')) {
                $table->dropColumn('classroom');
            }

            if (Schema::hasColumn('classes', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
