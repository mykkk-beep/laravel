<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherClassManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_update_and_delete_a_class(): void
    {
        $teacher = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_TEACHER,
            'active' => true,
        ]);

        $classRoom = ClassRoom::create([
            'name' => 'Math 101',
            'classroom' => 'Room 1',
            'date' => '2026-07-05',
            'time' => '09:00',
            'end_time' => '10:00',
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->put(route('teacher.classes.update', $classRoom), [
                'name' => 'Math 102',
                'classroom' => 'Room 2',
                'day_of_week' => 'Tuesday',
                'time' => '10:00',
                'end_time' => '11:00',
            ])
            ->assertRedirect(route('teacher.classes.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('classes', [
            'id' => $classRoom->id,
            'name' => 'Math 102',
            'classroom' => 'Room 2',
        ]);

        $this->actingAs($teacher)
            ->delete(route('teacher.classes.destroy', $classRoom))
            ->assertRedirect(route('teacher.classes.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('classes', [
            'id' => $classRoom->id,
        ]);
    }

    public function test_teacher_edit_page_preserves_the_current_class_day_selection(): void
    {
        $teacher = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher-edit@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_TEACHER,
            'active' => true,
        ]);

        $classRoom = ClassRoom::create([
            'name' => 'Math 101',
            'classroom' => 'Room 1',
            'date' => '2026-07-05',
            'time' => '09:00',
            'end_time' => '10:00',
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.classes.edit', $classRoom))
            ->assertOk()
            ->assertSee('value="Sunday" selected', false);
    }

    public function test_teacher_can_duplicate_a_class_with_its_students_and_a_new_schedule(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $sourceClass = ClassRoom::create([
            'name' => 'Math 101', 'classroom' => 'Room 1', 'date' => '2026-07-06',
            'time' => '09:00', 'end_time' => '10:00', 'teacher_id' => $teacher->id,
        ]);
        $student = Student::create([
            'student_id' => 'ST-100', 'name' => 'Alex Ray', 'sex' => 'male',
            'class_room_id' => $sourceClass->id, 'status' => 'enrolled',
        ]);
        Enrollment::create([
            'student_id' => $student->id, 'class_room_id' => $sourceClass->id,
            'status' => 'enrolled', 'grade' => 88,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.classes.store'), [
                'name' => 'Math 101 - Tuesday', 'classroom' => 'Room 2',
                'day_of_week' => 'Tuesday', 'time' => '13:00', 'end_time' => '14:00',
                'copy_from' => $sourceClass->id,
            ])
            ->assertRedirect(route('teacher.classes.index'));

        $copy = ClassRoom::where('name', 'Math 101 - Tuesday')->firstOrFail();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id, 'class_room_id' => $copy->id,
            'status' => 'enrolled', 'grade' => 0,
        ]);

        $this->get(route('teacher.classes.students.index', $copy))
            ->assertOk()
            ->assertSee('Alex Ray');
    }
}
