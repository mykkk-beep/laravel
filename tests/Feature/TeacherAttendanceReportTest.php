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

        $lateStudent = Student::create([
            'student_id' => 'ST-1002',
            'name' => 'Noah Reed',
            'sex' => 'male',
            'mobile' => '1231231237',
            'email' => 'noah@example.com',
            'class_room_id' => $classRoom->id,
            'status' => 'enrolled',
        ]);

        AttendanceRecord::create([
            'student_id' => $lateStudent->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'status' => 'late',
            'date' => '2026-06-30',
            'qr_code' => 'ST-1002',
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'status' => 'absent',
            'date' => '2026-07-02',
            'qr_code' => 'ST-1001',
        ]);

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.attendance.records', ['class_room_id' => $classRoom->id, 'date' => '2026-06-30']));

        $response->assertOk()
            ->assertSee('Monthly Attendance Tracker')
            ->assertSee('Export Calendar Excel')
            ->assertSee('attendance-calendar-print')
            ->assertSee('attendance-day-dialog')
            ->assertSee('Save counts')
            ->assertSee('monthly-present')
            ->assertSee('localStorage')
            ->assertSee('@media print');

        $monthResponse = $this->get(route('teacher.attendance.records', [
            'class_room_id' => $classRoom->id,
            'month' => '2026-06',
            'status' => 'present',
        ]));

        $monthResponse->assertOk()
            ->assertSee('June 2026')
            ->assertSee('Monday')
            ->assertSee('Sunday')
            ->assertSee('2026-06-30')
            ->assertSee('Late: 1')
            ->assertDontSee('2026-07-02');

        $exportResponse = $this->get(route('teacher.attendance.export', ['format' => 'excel', 'class_room_id' => $classRoom->id, 'date' => '2026-06-30']));

        $exportResponse->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_teacher_can_export_a_class_wide_monthly_attendance_calendar(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $classRoom = ClassRoom::create([
            'name' => 'Science 101',
            'classroom' => 'Room 9',
            'date' => '2026-11-01',
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

        foreach ([
            ['2026-11-01', 'present'],
            ['2026-11-02', 'absent'],
            ['2026-11-03', 'late'],
            ['2026-11-04', 'excused'],
            ['2026-12-01', 'absent'],
        ] as [$date, $status]) {
            AttendanceRecord::create([
                'student_id' => $student->id,
                'class_room_id' => $classRoom->id,
                'teacher_id' => $teacher->id,
                'status' => $status,
                'date' => $date,
                'qr_code' => 'ST-1001',
            ]);
        }

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.attendance.calendar-export', [
            'month' => '2026-11',
            'class_room_id' => $classRoom->id,
        ]));

        $response->assertOk()
            ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename="attendance-calendar-2026-11.xls"');

        $content = $response->getContent();
        $this->assertStringContainsString('November 2026', $content);
        $this->assertStringContainsString('Sunday', $content);
        $this->assertStringContainsString('Saturday', $content);
        $this->assertStringContainsString('P: 1', $content);
        $this->assertStringContainsString('A: 1', $content);
        $this->assertStringContainsString('L: 1', $content);
        $this->assertStringContainsString('B: 1', $content);
        $this->assertStringContainsString('Excused (B)', $content);
        $this->assertStringContainsString('background-color: #dcfce7', $content);
        $this->assertStringContainsString('background-color: #fee2e2', $content);
        $this->assertStringContainsString('background-color: #fef3c7', $content);
        $this->assertStringContainsString('background-color: #dbeafe', $content);
        $this->assertStringNotContainsString('December 2026', $content);
    }
}
