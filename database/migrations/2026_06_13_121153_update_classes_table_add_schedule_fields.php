<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'description')) {
                $table->dropColumn('description');
            }

            if (! Schema::hasColumn('classes', 'date')) {
                $table->date('date')->nullable();
            }

            if (! Schema::hasColumn('classes', 'time')) {
                $table->time('time')->nullable();
            }

            if (! Schema::hasColumn('classes', 'classroom')) {
                $table->string('classroom')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            if (! Schema::hasColumn('classes', 'description')) {
                $table->text('description')->nullable();
            }

            if (Schema::hasColumn('classes', 'date')) {
                $table->dropColumn('date');
            }

            if (Schema::hasColumn('classes', 'time')) {
                $table->dropColumn('time');
            }

            if (Schema::hasColumn('classes', 'classroom')) {
                $table->dropColumn('classroom');
            }
        });
    }
};
