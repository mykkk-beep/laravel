<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_initialize_and_record_attendance_via_json_requests(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $classRoom = ClassRoom::create([
            'name' => 'Grade 10A',
            'teacher_id' => $teacher->id,
        ]);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
        ]);

        $student = Student::create([
            'name' => 'Jane Doe',
            'student_id' => 'ST-001',
            'class_room_id' => $classRoom->id,
        ]);

        $this->actingAs($teacher);

        $initializeResponse = $this->postJson(route('teacher.attendance.initialize'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
        ]);

        $initializeResponse->assertOk()
            ->assertJsonPath('created', 1);

        $this->assertDatabaseHas('attendance_records', [
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'date' => now()->toDateString(),
            'status' => 'absent',
        ]);

        $recordResponse = $this->postJson(route('teacher.attendance.record'), [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'qr_code' => 'ST-001',
        ]);

        $recordResponse->assertOk()
            ->assertJsonFragment(['message' => 'Attendance recorded for Jane Doe.']);

        $this->assertDatabaseHas('attendance_records', [
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'date' => now()->toDateString(),
            'status' => 'present',
        ]);
    }
}
