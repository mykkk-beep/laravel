<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAttendanceScanTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_initialize_and_record_attendance_for_a_student_in_the_selected_class(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $classRoom = ClassRoom::create([
            'name' => 'Math 101',
            'classroom' => 'Room 1',
            'date' => '2026-06-30',
            'time' => '08:00',
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::create([
            'student_id' => 'ST-900',
            'name' => 'Mina Lee',
            'sex' => 'female',
            'mobile' => '1231231234',
            'email' => 'mina@example.com',
            'class_room_id' => $classRoom->id,
        ]);

        $subject = \App\Models\Subject::create([
            'name' => 'Algebra',
            'code' => 'ALG-900',
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
        ]);

        \App\Models\Enrollment::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'class_room_id' => $classRoom->id,
            'grade' => 0,
        ]);

        $this->actingAs($teacher);

        $initializeResponse = $this->postJson(route('teacher.attendance.initialize'), [
            'class_room_id' => $classRoom->id,
        ]);

        $initializeResponse->assertOk()
            ->assertJsonPath('message', 'Attendance initialized for selected class.');

        $student->update(['status' => 'enrolled']);

        $recordResponse = $this->postJson(route('teacher.attendance.record'), [
            'class_room_id' => $classRoom->id,
            'qr_code' => $student->student_id,
        ]);

        $recordResponse->assertOk()
            ->assertJsonPath('status', 'present')
            ->assertJsonPath('student.name', 'Mina Lee');

        $this->assertDatabaseHas('attendance_records', [
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'status' => 'present',
            'date' => now()->toDateString(),
        ]);
        $record = \App\Models\AttendanceRecord::where('student_id', $student->id)
            ->where('class_room_id', $classRoom->id)
            ->where('teacher_id', $teacher->id)
            ->whereDate('date', now()->toDateString())
            ->latest()
            ->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record->time_in);

        $duplicateResponse = $this->postJson(route('teacher.attendance.record'), [
            'class_room_id' => $classRoom->id,
            'qr_code' => $student->student_id,
        ]);

        $duplicateResponse->assertStatus(409)
            ->assertJsonFragment(['message' => 'Attendance already recorded for Mina Lee today.']);
    }

    public function test_teacher_scan_page_renders_the_scanner_script_for_manual_and_qr_submissions(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $this->actingAs($teacher);

        $response = $this->get(route('teacher.attendance.scan'));

        $response->assertOk();
        $response->assertSee('manual-input-form');
        $response->assertSee("processQRCode");
    }

    public function test_teacher_scan_page_displays_clickable_student_list_for_quick_attendance(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $classRoom = ClassRoom::create([
            'name' => 'Science 101',
            'classroom' => 'Room 2',
            'date' => '2026-06-30',
            'time' => '08:00',
            'teacher_id' => $teacher->id,
        ]);

        Student::create([
            'student_id' => 'ST-902',
            'name' => 'Liam Brown',
            'sex' => 'male',
            'mobile' => '1231231236',
            'email' => 'liam@example.com',
            'class_room_id' => $classRoom->id,
        ]);

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.attendance.scan'));

        $response->assertOk();
        $response->assertSee('Click a student below to mark attendance instantly.');
        $response->assertSee('student-selection-list');
    }

    public function test_teacher_cannot_record_attendance_for_a_student_that_is_not_enrolled(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $classRoom = ClassRoom::create([
            'name' => 'English 101',
            'classroom' => 'Room 7',
            'date' => '2026-06-30',
            'time' => '08:00',
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::create([
            'student_id' => 'ST-901',
            'name' => 'Nina Cole',
            'sex' => 'female',
            'mobile' => '1231231235',
            'email' => 'nina@example.com',
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
        ]);

        $subject = \App\Models\Subject::create([
            'name' => 'Grammar',
            'code' => 'ENG-901',
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
        ]);

        \App\Models\Enrollment::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'class_room_id' => $classRoom->id,
            'grade' => 0,
        ]);

        $this->actingAs($teacher);

        $response = $this->postJson(route('teacher.attendance.record'), [
            'class_room_id' => $classRoom->id,
            'qr_code' => $student->student_id,
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment(['message' => 'This student is not yet enrolled. Please enroll the student before scanning.']);
    }
}
