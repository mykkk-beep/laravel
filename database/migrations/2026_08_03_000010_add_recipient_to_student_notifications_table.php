<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('student_notifications', function (Blueprint $table) {
            $table->string('recipient')->default('guardian')->after('message');
        });
    }

    public function down()
    {
        Schema::table('student_notifications', function (Blueprint $table) {
            $table->dropColumn('recipient');
        });
    }
};
