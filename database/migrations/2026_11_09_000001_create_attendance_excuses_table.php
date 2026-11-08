<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_excuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_room_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('date');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'class_room_id', 'date']);
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE attendance_records MODIFY status VARCHAR(20) NOT NULL DEFAULT 'absent'");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_excuses');

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("UPDATE attendance_records SET status = 'absent' WHERE status = 'excused'");
            DB::statement("ALTER TABLE attendance_records MODIFY status ENUM('present', 'absent', 'late') NOT NULL DEFAULT 'absent'");
        }
    }
};
