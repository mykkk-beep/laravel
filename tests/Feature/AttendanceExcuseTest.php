<?php

namespace Tests\Feature;

use App\Models\AttendanceExcuse;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceExcuseTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_and_parent_can_submit_excuses_for_their_assigned_student(): void
    {
        [$teacher, $classRoom, $student] = $this->createAttendanceGroup();

        $this->withSession([
            'authenticated_student' => true,
            'student_id' => $student->id,
        ])->post(route('student.attendance-excuses.store'), [
            'class_room_id' => $classRoom->id,
            'date' => '2026-11-09',
            'reason' => 'I have a medical appointment.',
        ])->assertRedirect(route('student.dashboard'));

        $this->assertDatabaseHas('attendance_excuses', [
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'status' => AttendanceExcuse::STATUS_PENDING,
        ]);
        $this->assertDatabaseHas('student_notifications', [
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'recipient' => 'teacher',
        ]);
        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Excuse requests');
        $this->actingAs($teacher)
            ->get(route('teacher.notifications'))
            ->assertOk()
            ->assertSee('Absence excuse submitted')
            ->assertSee('medical appointment.');

        $secondStudent = Student::create([
            'student_id' => 'EX-002',
            'name' => 'Parent Test Student',
            'class_room_id' => $classRoom->id,
        ]);

        $this->withSession(['parent_student_id' => $secondStudent->id])
            ->post(route('parent.attendance-excuses.store'), [
                'class_room_id' => $classRoom->id,
                'date' => '2026-11-10',
                'reason' => 'My child is unwell.',
            ])
            ->assertRedirect(route('parent.dashboard'));

        $this->assertDatabaseHas('attendance_excuses', [
            'student_id' => $secondStudent->id,
            'status' => AttendanceExcuse::STATUS_PENDING,
            'reason' => 'My child is unwell.',
        ]);
        $this->get(route('parent.dashboard'))
            ->assertOk()
            ->assertSee('Excuse requests');
    }

    public function test_teacher_approval_marks_attendance_excused_and_bulk_mark_does_not_overwrite_it(): void
    {
        [$teacher, $classRoom, $student] = $this->createAttendanceGroup();
        $excuse = AttendanceExcuse::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'teacher_id' => $teacher->id,
            'date' => now()->toDateString(),
            'reason' => 'Medical appointment.',
            'status' => AttendanceExcuse::STATUS_PENDING,
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.attendance.scan'))
            ->assertOk()
            ->assertSee('Absence excuses need review')
            ->assertSee('Medical appointment.');

        $this->actingAs($teacher)
            ->post(route('teacher.attendance.excuses.review', $excuse), ['decision' => 'approve'])
            ->assertRedirect();

        $this->assertDatabaseHas('attendance_records', [
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'date' => now()->toDateString(),
            'status' => 'excused',
            'notes' => 'Medical appointment.',
        ]);

        $this->postJson(route('teacher.attendance.mark-all-present'), [
            'class_room_id' => $classRoom->id,
        ])->assertOk()
            ->assertJsonPath('summary.excused', 1);
        $this->postJson(route('teacher.attendance.finalize-quick'), [
            'class_room_id' => $classRoom->id,
        ])->assertOk()
            ->assertJsonPath('summary.excused', 1);
        $this->get(route('teacher.attendance.records', [
            'class_room_id' => $classRoom->id,
            'date' => now()->toDateString(),
        ]))->assertOk()
            ->assertSee('Excused')
            ->assertSee('Medical appointment.');

        $this->assertDatabaseHas('attendance_records', [
            'student_id' => $student->id,
            'status' => 'excused',
        ]);
        $this->assertSame(AttendanceExcuse::STATUS_APPROVED, $excuse->fresh()->status);
    }

    private function createAttendanceGroup(): array
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $classRoom = ClassRoom::create([
            'name' => 'Excuse Test Class',
            'teacher_id' => $teacher->id,
        ]);
        $student = Student::create([
            'student_id' => 'EX-001',
            'name' => 'Excuse Test Student',
            'class_room_id' => $classRoom->id,
        ]);

        return [$teacher, $classRoom, $student];
    }
}
