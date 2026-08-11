<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherPendingStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_mark_teacher_account_as_pending_and_login_shows_pending_message(): void
    {
        $superadmin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
            'name' => 'Super Admin',
            'email' => 'super@example.com',
        ]);

        $teacher = User::factory()->create([
            'name' => 'Pending Teacher',
            'email' => 'teacher@example.com',
            'role' => User::ROLE_TEACHER,
            'active' => true,
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($superadmin)
            ->put(route('superadmin.teachers.update', $teacher), [
                'name' => 'Pending Teacher',
                'email' => 'teacher@example.com',
                'status' => 'pending',
            ])
            ->assertRedirect(route('superadmin.teachers.index'));

        $teacher->refresh();

        $this->assertSame('pending', $teacher->status);
        $this->assertFalse($teacher->active);

        $response = $this->post(route('login'), [
            'email' => 'teacher@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors(['email' => 'Account is pending, wait for the administrator verification.']);
    }
}
