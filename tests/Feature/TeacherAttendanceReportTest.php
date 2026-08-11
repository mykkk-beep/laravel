<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAttendanceReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_attendance_summary_and_export_excel_report(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $classRoom = ClassRoom::create([
            'name' => 'Science 101',
            'classroom' => 'Room 9',
            'date' => '2026-06-30',
            'time' => '09:00',
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::create([
            'student_id' => 'ST-1001',
            'name' => 'Ava Stone',
            'sex' => 'female',
            'mobile' => '1231231236',
            'email' => 'ava@example.com',
            'class_room_id' => $classRoom->id,
            'status' => 'enrolled',
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'status' => 'present',
            'date' => '2026-06-30',
            'qr_code' => 'ST-1001',
        ]);

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.attendance.records', ['class_room_id' => $classRoom->id, 'date' => '2026-06-30']));

        $response->assertOk()
            ->assertSee('Attendance Summary')
            ->assertSee('Export Excel');

        $exportResponse = $this->get(route('teacher.attendance.export', ['format' => 'excel', 'class_room_id' => $classRoom->id, 'date' => '2026-06-30']));

        $exportResponse->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
