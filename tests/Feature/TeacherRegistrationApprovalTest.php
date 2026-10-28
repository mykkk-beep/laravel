<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherRegistrationApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bell_displays_pending_teacher_registration_count(): void
    {
        $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        User::factory()->create([
            'name' => 'Pending Teacher',
            'role' => User::ROLE_TEACHER,
            'status' => User::STATUS_PENDING,
            'active' => false,
        ]);

        $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard'))
            ->assertOk()
            ->assertSee('Pending teacher registrations')
            ->assertSee('>1</span>', false);
    }

    public function test_teacher_list_shows_accept_and_decline_only_for_pending_accounts(): void
    {
        $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $pendingTeacher = User::factory()->create([
            'name' => 'Pending Teacher',
            'role' => User::ROLE_TEACHER,
            'status' => User::STATUS_PENDING,
            'active' => false,
        ]);
        $activeTeacher = User::factory()->create([
            'name' => 'Active Teacher',
            'role' => User::ROLE_TEACHER,
            'status' => User::STATUS_ACTIVE,
            'active' => true,
        ]);

        $this->actingAs($superadmin)
            ->get(route('superadmin.teachers.index'))
            ->assertOk()
            ->assertSee('action="' . route('superadmin.teachers.approve', $pendingTeacher) . '"', false)
            ->assertSee('action="' . route('superadmin.teachers.decline', $pendingTeacher) . '"', false)
            ->assertDontSee('href="' . route('superadmin.teachers.edit', $pendingTeacher) . '"', false)
            ->assertDontSee('action="' . route('superadmin.teachers.destroy', $pendingTeacher) . '"', false)
            ->assertDontSee('action="' . route('superadmin.teachers.approve', $activeTeacher) . '"', false)
            ->assertDontSee('action="' . route('superadmin.teachers.decline', $activeTeacher) . '"', false)
            ->assertSee('href="' . route('superadmin.teachers.edit', $activeTeacher) . '"', false)
            ->assertSee('action="' . route('superadmin.teachers.destroy', $activeTeacher) . '"', false)
            ->assertSee('Accept')
            ->assertSee('Decline')
            ->assertSee('Active Teacher');
    }

    public function test_superadmin_can_accept_pending_teacher_registration(): void
    {
        $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'status' => User::STATUS_PENDING,
            'active' => false,
        ]);

        $this->actingAs($superadmin)
            ->post(route('superadmin.teachers.approve', $teacher))
            ->assertRedirect(route('superadmin.teachers.index'))
            ->assertSessionHas('success', 'Teacher account approved successfully.');

        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'status' => User::STATUS_ACTIVE,
            'active' => true,
        ]);
    }

    public function test_superadmin_can_decline_pending_teacher_registration(): void
    {
        $superadmin = User::factory()->create(['role' => User::ROLE_SUPERADMIN]);
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'status' => User::STATUS_PENDING,
            'active' => false,
        ]);

        $this->actingAs($superadmin)
            ->post(route('superadmin.teachers.decline', $teacher))
            ->assertRedirect(route('superadmin.teachers.index'))
            ->assertSessionHas('success', 'Teacher account declined.');

        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'status' => User::STATUS_DISABLED,
            'active' => false,
        ]);
    }

    public function test_non_superadmin_cannot_approve_teacher_accounts(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
            'status' => User::STATUS_PENDING,
            'active' => false,
        ]);
        $anotherTeacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $this->actingAs($anotherTeacher)
            ->post(route('superadmin.teachers.approve', $teacher))
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'id' => $teacher->id,
            'status' => User::STATUS_PENDING,
            'active' => false,
        ]);
    }
}