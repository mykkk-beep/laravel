<?php

namespace Tests\Feature;

use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherAttendanceGraphTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_attendance_graph_includes_late_and_excused_counts(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $classRoom = ClassRoom::create([
            'name' => 'Graph Test Class',
            'teacher_id' => $teacher->id,
        ]);

        foreach (['late', 'excused'] as $index => $status) {
            $student = Student::create([
                'student_id' => 'GRAPH-00'.($index + 1),
                'name' => 'Graph Test Student '.($index + 1),
                'class_room_id' => $classRoom->id,
            ]);

            AttendanceRecord::create([
                'student_id' => $student->id,
                'class_room_id' => $classRoom->id,
                'teacher_id' => $teacher->id,
                'status' => $status,
                'date' => now()->toDateString(),
            ]);
        }

        $response = $this->withoutExceptionHandling()
            ->actingAs($teacher)
            ->get(route('teacher.dashboard'));

        $response->assertOk()
            ->assertSee("label: 'Late'", false)
            ->assertSee("label: 'Excused'", false)
            ->assertSee('"late":[0,0,0,0,0,0,1]', false)
            ->assertSee('"excused":[0,0,0,0,0,0,1]', false);
    }
}
