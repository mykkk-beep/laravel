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
        $response->assertSee('Priority level', false);
        $response->assertDontSee('Parent information', false);
    }

    public function test_teacher_can_send_a_student_recommendation_only_when_enabled(): void
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

        $this->actingAs($teacher);

        $response = $this->post(route('teacher.recommendations.generate', $student), [
            'message' => 'Please support regular attendance at home.',
            'student_message' => 'You are doing well and should keep building on your progress.',
            'send_to_parent' => false,
            'send_to_student' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_notifications', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'recipient' => 'student',
            'message' => 'You are doing well and should keep building on your progress.',
        ]);
        $this->assertDatabaseMissing('student_notifications', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'recipient' => 'guardian',
        ]);
    }

    public function test_sent_recommendations_appear_on_the_recommendations_page(): void
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

        $this->actingAs($teacher);

        $this->post(route('teacher.recommendations.generate', $student), [
            'message' => 'Please support regular attendance at home.',
            'priority_level' => 'High',
        ]);

        $response = $this->get(route('teacher.recommendations'));

        $response->assertOk();
        $response->assertSee('Ana Lopez');
        $response->assertSee('Please support regular attendance at home.');
        $response->assertSee('Sent');
    }

    public function test_teacher_can_save_a_draft_recommendation_and_see_it_on_the_page(): void
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

        $this->actingAs($teacher);

        $response = $this->post(route('teacher.recommendations.generate', $student), [
            'message' => 'Please support regular attendance at home.',
            'save_draft' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_notifications', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => 'Draft Recommendation for Ana Lopez',
        ]);

        $pageResponse = $this->get(route('teacher.recommendations'));
        $pageResponse->assertOk();
        $pageResponse->assertSee('Draft');
        $pageResponse->assertSee('Please support regular attendance at home.');
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

        $response = $this->post(route('teacher.recommendations.generate', $student), [
            'priority_level' => 'High',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('student_notifications', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
        ]);

        $notification = StudentNotification::where('student_id', $student->id)->latest()->first();
        $this->assertStringContainsString('Student Recommendation Letter', $notification->message);
        $this->assertStringContainsString('Dear Parent/Guardian', $notification->message);
        $this->assertStringContainsString('Ana Lopez', $notification->message);
        $this->assertStringContainsString('High', $notification->message);
    }
}
