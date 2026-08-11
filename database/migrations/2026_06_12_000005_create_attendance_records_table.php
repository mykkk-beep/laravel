<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_room_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->enum('status', ['present', 'absent'])->default('absent');
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('notified')->default(false);
            $table->string('qr_code')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'class_room_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
