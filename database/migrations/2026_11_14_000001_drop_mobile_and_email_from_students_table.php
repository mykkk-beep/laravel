<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'mobile')) {
                $table->dropColumn('mobile');
            }

            if (Schema::hasColumn('students', 'email')) {
                $table->dropColumn('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'mobile')) {
                $table->string('mobile')->nullable()->after('sex');
            }

            if (! Schema::hasColumn('students', 'email')) {
                $table->string('email')->nullable()->after('mobile');
            }
        });
    }
};
