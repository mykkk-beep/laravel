<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('student_id')->nullable();
            $table->string('name');
            $table->string('sex')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('class_room_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->timestamps();

            $table->index(['class_room_id', 'name']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
