<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_enroll_a_student_and_update_status(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $classRoom = ClassRoom::create([
            'name' => 'Science 101',
            'classroom' => 'Room 2',
            'date' => '2026-07-07',
            'time' => '09:00',
            'teacher_id' => $teacher->id,
        ]);

        $student = Student::create([
            'student_id' => 'ST-100',
            'name' => 'Alex Ray',
            'sex' => 'male',
            'mobile' => '2223334444',
            'email' => 'alex@example.com',
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
            'grade' => 0,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.students.enroll', $student))
            ->assertRedirect();

        $student->refresh();
        $enrollment->refresh();

        $this->assertSame('enrolled', $student->status);
        $this->assertSame('enrolled', $enrollment->status);
    }

    public function test_teacher_can_bulk_enroll_all_students_in_a_class(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $classRoom = ClassRoom::create([
            'name' => 'Math 101',
            'classroom' => 'Room 5',
            'date' => '2026-07-08',
            'time' => '10:00',
            'teacher_id' => $teacher->id,
        ]);

        $studentA = Student::create([
            'student_id' => 'ST-200',
            'name' => 'Kim Park',
            'sex' => 'female',
            'mobile' => '5556667777',
            'email' => 'kim@example.com',
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
        ]);

        $studentB = Student::create([
            'student_id' => 'ST-201',
            'name' => 'Leo Grant',
            'sex' => 'male',
            'mobile' => '5556667778',
            'email' => 'leo@example.com',
            'class_room_id' => $classRoom->id,
            'status' => 'not_enrolled',
        ]);

        Enrollment::create([
            'student_id' => $studentA->id,
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
            'grade' => 0,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.classes.students.enroll_all', $classRoom))
            ->assertRedirect();

        $studentA->refresh();
        $studentB->refresh();

        $this->assertSame('enrolled', $studentA->status);
        $this->assertSame('enrolled', $studentB->status);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $studentB->id,
            'class_room_id' => $classRoom->id,
            'status' => 'enrolled',
        ]);
    }
}
