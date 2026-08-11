<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
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
}
