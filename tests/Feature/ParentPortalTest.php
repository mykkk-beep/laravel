<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParentPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_login_with_student_id_and_reply_to_notification(): void
    {
        $student = Student::create([
            'student_id' => 'STU-001',
            'name' => 'Alice Johnson',
            'status' => Student::STATUS_ENROLLED,
        ]);

        $notification = StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => null,
            'title' => 'Attendance update',
            'message' => 'Your child was marked absent today.',
        ]);

        $response = $this->post(route('parent.login.submit'), [
            'student_id' => 'STU-001',
        ]);

        $response->assertRedirect(route('parent.dashboard'));
        $this->assertSame($student->id, session('parent_student_id'));

        $replyResponse = $this->withSession(['parent_student_id' => $student->id])
            ->post(route('parent.notifications.reply', $notification), [
                'reply' => 'Thank you for the update.',
            ]);

        $replyResponse->assertRedirect(route('parent.notifications'));
        $this->assertSame('Thank you for the update.', $notification->fresh()->parent_reply);
    }

    public function test_parent_can_send_multiple_replies_to_the_same_notification(): void
    {
        $student = Student::create([
            'student_id' => 'STU-003',
            'name' => 'Carol Johnson',
            'status' => Student::STATUS_ENROLLED,
        ]);

        $notification = StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => null,
            'title' => 'Attendance update',
            'message' => 'Please review your child’s attendance.',
        ]);

        $this->withSession(['parent_student_id' => $student->id])
            ->post(route('parent.notifications.reply', $notification), [
                'reply' => 'Thank you for the update.',
            ]);

        $this->withSession(['parent_student_id' => $student->id])
            ->post(route('parent.notifications.reply', $notification), [
                'reply' => 'I will follow up tomorrow.',
            ]);

        $notification->refresh();

        $this->assertSame("Thank you for the update.\n\nI will follow up tomorrow.", $notification->parent_reply);
    }

    public function test_parent_can_delete_a_notification(): void
    {
        $student = Student::create([
            'student_id' => 'STU-003',
            'name' => 'Carol Johnson',
            'status' => Student::STATUS_ENROLLED,
        ]);

        $notification = StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => null,
            'title' => 'Attendance update',
            'message' => 'Please review your child’s attendance.',
        ]);

        $response = $this->withSession(['parent_student_id' => $student->id])
            ->delete(route('parent.notifications.destroy', $notification));

        $response->assertRedirect(route('parent.notifications'));
        $this->assertDatabaseMissing('student_notifications', ['id' => $notification->id]);
    }

    public function test_parent_dashboard_shows_recent_attendance_and_teacher_notifications(): void
    {
        $teacher = User::factory()->create();
        $classRoom = ClassRoom::create([
            'name' => 'Grade 10 Science',
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::create([
            'student_id' => 'STU-002',
            'name' => 'Bob Johnson',
            'class_room_id' => $classRoom->id,
            'status' => Student::STATUS_ENROLLED,
        ]);

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'status' => 'present',
            'date' => now()->toDateString(),
            'time_in' => '08:10',
            'notes' => 'On time',
        ]);

        StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => null,
            'title' => 'Teacher recommendation',
            'message' => 'Please review your child’s progress with the teacher.',
        ]);

        $response = $this->withSession(['parent_student_id' => $student->id])
            ->get(route('parent.dashboard'));

        $response->assertOk();
        $response->assertSee('Attendance Overview');
        $response->assertSee('Teacher Updates and Recommendations');
        $response->assertSee('Teacher recommendation');
        $response->assertSee('Present');
    }
}
