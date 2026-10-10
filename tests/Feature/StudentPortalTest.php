<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_login_page_is_available(): void
    {
        $response = $this->get('/student/login');

        $response->assertStatus(200);
        $response->assertViewIs('student.auth.login');
    }

    public function test_student_dashboard_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/student/dashboard');

        $response->assertRedirect('/student/login');
    }

    public function test_student_dashboard_counts_all_classes_when_student_is_enrolled_in_multiple_classes(): void
    {
        $teacher = User::factory()->create();

        $mathClass = ClassRoom::create([
            'name' => 'Mathematics',
            'teacher_id' => $teacher->id,
        ]);

        $scienceClass = ClassRoom::create([
            'name' => 'Science',
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::create([
            'student_id' => 'STU-100',
            'name' => 'Jordan Lee',
            'class_room_id' => $mathClass->id,
            'status' => Student::STATUS_ENROLLED,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'class_room_id' => $scienceClass->id,
            'status' => Enrollment::STATUS_ENROLLED,
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $scienceClass->id,
            'teacher_id' => $teacher->id,
            'status' => 'present',
            'date' => now()->toDateString(),
            'time_in' => '08:15',
        ]);

        $response = $this->withSession([
            'student_id' => $student->id,
            'student_name' => $student->name,
            'authenticated_student' => true,
        ])->get(route('student.dashboard'));

        $response->assertOk();
        $response->assertViewHas('totalClasses', 2);
        $response->assertSee('Recent attendance in other classes');
        $response->assertSee('Science');
    }

    public function test_student_portal_includes_attendance_and_classes_from_matching_student_records(): void
    {
        $firstTeacher = User::factory()->create();
        $secondTeacher = User::factory()->create();

        $firstClass = ClassRoom::create([
            'name' => 'Mathematics',
            'teacher_id' => $firstTeacher->id,
        ]);

        $secondClass = ClassRoom::create([
            'name' => 'English',
            'teacher_id' => $secondTeacher->id,
        ]);

        $student = Student::create([
            'student_id' => 'STU-200',
            'name' => 'Taylor Morgan',
            'class_room_id' => $firstClass->id,
            'status' => Student::STATUS_ENROLLED,
        ]);

        $matchingStudent = Student::create([
            'student_id' => 'STU-200',
            'name' => 'Taylor Morgan',
            'class_room_id' => $secondClass->id,
            'status' => Student::STATUS_ENROLLED,
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $firstClass->id,
            'teacher_id' => $firstTeacher->id,
            'status' => 'present',
            'date' => now()->toDateString(),
            'time_in' => '08:00',
        ]);

        $secondClassAttendance = AttendanceRecord::create([
            'student_id' => $matchingStudent->id,
            'class_room_id' => $secondClass->id,
            'teacher_id' => $secondTeacher->id,
            'status' => 'present',
            'date' => now()->toDateString(),
            'time_in' => '09:00',
        ]);

        $session = [
            'student_id' => $student->id,
            'student_name' => $student->name,
            'authenticated_student' => true,
        ];

        $dashboardResponse = $this->withSession($session)->get(route('student.dashboard'));

        $dashboardResponse->assertOk();
        $dashboardResponse->assertViewHas('totalClasses', 2);
        $dashboardResponse->assertViewHas('attendanceRecords', function ($records) use ($secondClassAttendance) {
            return $records->getCollection()->contains('id', $secondClassAttendance->id);
        });
        $dashboardResponse->assertViewHas('otherClassAttendance', function ($records) use ($secondClassAttendance) {
            return $records->contains('id', $secondClassAttendance->id);
        });

        $notificationsResponse = $this->withSession($session)->get(route('student.notifications'));

        $notificationsResponse->assertOk();
        $notificationsResponse->assertViewHas('recentAttendance', function ($records) use ($secondClassAttendance) {
            return $records->contains('id', $secondClassAttendance->id);
        });
    }
}
