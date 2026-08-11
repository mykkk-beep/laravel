<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAdditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_be_added_from_dashboard(): void
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
            ->post(route('teacher.students.store'), [
                'student_id' => 'S001',
                'name' => 'Jane Doe',
                'sex' => 'female',
                'mobile' => '1234567890',
                'email' => 'jane@example.com',
                'class_room_id' => $classRoom->id,
            ])
            ->assertRedirect(route('teacher.students.all'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'name' => 'Jane Doe',
            'student_id' => 'S001',
            'class_room_id' => $classRoom->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.students.all'))
            ->assertOk()
            ->assertSee('Jane Doe')
            ->assertSee('successfully');
    }

    public function test_student_full_name_is_shown_in_manage_students_list(): void
    {
        $teacher = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_TEACHER,
            'active' => true,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.students.store'), [
                'student_id' => 'S002',
                'name' => 'John',
                'middle_name' => 'A',
                'last_name' => 'Smith',
                'sex' => 'male',
                'mobile' => '0987654321',
                'email' => 'john@example.com',
                'class_room_id' => null,
            ])
            ->assertRedirect(route('teacher.students.all'))
            ->assertSessionHas('success');

        $this->actingAs($teacher)
            ->get(route('teacher.students.all'))
            ->assertOk()
            ->assertSee('John A Smith');
    }

    public function test_student_can_be_added_without_class(): void
    {
        $teacher = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_TEACHER,
            'active' => true,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.students.store'), [
                'student_id' => 'S003',
                'name' => 'John Smith',
                'sex' => 'male',
                'mobile' => '0987654321',
                'email' => 'john@example.com',
                'class_room_id' => null,
            ])
            ->assertRedirect(route('teacher.students.all'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'name' => 'John Smith',
            'student_id' => 'S003',
            'class_room_id' => null,
        ]);
    }

    public function test_student_can_be_added_without_mobile_number(): void
    {
        $teacher = User::create([
            'name' => 'Teacher One',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role' => User::ROLE_TEACHER,
            'active' => true,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.students.store'), [
                'student_id' => 'S004',
                'name' => 'Alice Brown',
                'sex' => 'female',
                'email' => 'alice@example.com',
                'class_room_id' => null,
            ])
            ->assertRedirect(route('teacher.students.all'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('students', [
            'name' => 'Alice Brown',
            'student_id' => 'S004',
            'mobile' => null,
        ]);
    }
}
