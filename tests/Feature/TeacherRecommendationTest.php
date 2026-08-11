<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\StudentNotification;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherRecommendationTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_the_recommendations_dashboard(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.recommendations'));

        $response->assertOk();
        $response->assertSee('Recommendations');
        $response->assertSee('New Recommendation');
        $response->assertSee('No recommendations yet.');
    }

    public function test_teacher_recommendations_page_offers_a_direct_parent_sending_action(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $this->actingAs($teacher);

        $response = $this->get(route('teacher.recommendations'));

        $response->assertOk();
        $response->assertSee('Send to parent', false);
    }

    public function test_teacher_can_generate_a_recommendation_and_send_parent_letter(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $classRoom = ClassRoom::create([
            'name' => 'Grade 8A',
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::create([
            'name' => 'Ana Lopez',
            'student_id' => '2026-001',
            'class_room_id' => $classRoom->id,
        ]);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH101',
            'teacher_id' => $teacher->id,
            'class_room_id' => $classRoom->id,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'status' => Enrollment::STATUS_ENROLLED,
            'grade' => 68.50,
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'status' => 'absent',
            'date' => now()->subDays(2)->toDateString(),
            'notified' => false,
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'status' => 'absent',
            'date' => now()->subDays(1)->toDateString(),
            'notified' => false,
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'status' => 'present',
            'date' => now()->toDateString(),
            'notified' => false,
        ]);

        $this->actingAs($teacher);

        $response = $this->post(route('teacher.recommendations.generate', $student));

        $response->assertRedirect();
        $this->assertDatabaseHas('student_notifications', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
        ]);

        $notification = StudentNotification::where('student_id', $student->id)->latest()->first();
        $this->assertStringContainsString('Student Name:', $notification->message);
        $this->assertStringContainsString('Dear Parent/Guardian', $notification->message);
        $this->assertStringContainsString('Ana Lopez', $notification->message);
    }
}
