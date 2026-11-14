<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_dashboard_renders_sidebar_navigation(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $response = $this->actingAs($teacher)->get(route('teacher.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Students');
        $response->assertSee('Attendance');
    }

    public function test_number_of_classes_links_to_the_teacher_classes_page(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);
        ClassRoom::create([
            'name' => 'Math 101',
            'teacher_id' => $teacher->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertSee(
                '<a href="'.route('teacher.classes.index').'" aria-label="View all classes"',
                false
            )
            ->assertSee('>1</a>', false);
    }

    public function test_total_students_links_to_the_teacher_students_page(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);
        $classRoom = ClassRoom::create([
            'name' => 'Math 101',
            'teacher_id' => $teacher->id,
        ]);
        Student::create([
            'student_id' => 'ST-100',
            'name' => 'Alex Ray',
            'sex' => 'male',
            'class_room_id' => $classRoom->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertSee(
                '<a href="'.route('teacher.students.all').'" aria-label="View all students"',
                false
            )
            ->assertSee('>1</a>', false);
    }

    public function test_superadmin_dashboard_renders_sidebar_navigation(): void
    {
        $superadmin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
        ]);

        $response = $this->actingAs($superadmin)->get(route('superadmin.dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Teachers');
    }
}
