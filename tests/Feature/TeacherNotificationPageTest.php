<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherNotificationPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_their_notification_page(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $student = Student::create([
            'name' => 'Test Student',
            'student_id' => 'STU-001',
        ]);

        StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => 'Attendance notice',
            'message' => 'Please review this student.',
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.notifications'));

        $response->assertOk();
        $response->assertSee('Parent Notifications');
        $response->assertSee('Attendance notice');
    }
}
