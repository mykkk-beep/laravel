<?php

namespace Tests\Feature;

use App\Models\NotificationReply;
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

    public function test_teacher_notification_bell_counts_pending_replies_to_recommendations(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $student = Student::create([
            'name' => 'Test Student',
            'student_id' => 'STU-001',
        ]);

        $parentRecommendation = StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => 'Recommendation for Test Student',
            'message' => 'Parent recommendation.',
            'recipient' => 'guardian',
        ]);
        $parentRecommendation->addReply(NotificationReply::SENDER_PARENT, 'Parent reply.');

        $studentRecommendation = StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => 'Student Recommendation',
            'message' => 'Student recommendation.',
            'recipient' => 'student',
        ]);
        $studentRecommendation->addReply(NotificationReply::SENDER_PARENT, 'Student reply.');

        $answeredRecommendation = StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => 'Recommendation for Test Student',
            'message' => 'Answered recommendation.',
            'recipient' => 'guardian',
        ]);
        $answeredRecommendation->addReply(NotificationReply::SENDER_PARENT, 'Another parent reply.');
        $answeredRecommendation->addReply(NotificationReply::SENDER_TEACHER, 'Teacher response.');

        $unrelatedNotification = StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => 'Attendance notice',
            'message' => 'Unrelated notification.',
            'recipient' => 'guardian',
        ]);
        $unrelatedNotification->addReply(NotificationReply::SENDER_PARENT, 'Attendance reply.');

        $response = $this->actingAs($teacher)->get(route('teacher.notifications'));

        $response->assertOk();
        $response->assertSee('Notifications, 2 recommendation replies awaiting your response');
    }
}
