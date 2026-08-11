<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperAdminDashboardAndProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_dashboard_shows_active_teacher_summary_and_recent_activity(): void
    {
        $superadmin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
            'name' => 'Super Admin',
            'email' => 'super@example.com',
        ]);

        User::factory()->create([
            'name' => 'Active Teacher',
            'email' => 'active@example.com',
            'role' => User::ROLE_TEACHER,
            'active' => true,
        ]);

        User::factory()->create([
            'name' => 'Inactive Teacher',
            'email' => 'inactive@example.com',
            'role' => User::ROLE_TEACHER,
            'active' => false,
        ]);

        $this->actingAs($superadmin)
            ->post(route('superadmin.teachers.store'), [
                'name' => 'New Teacher',
                'email' => 'newteacher@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response = $this->actingAs($superadmin)->get(route('superadmin.dashboard'));

        $response->assertOk();
        $response->assertSee('Super Admin Dashboard');
        $response->assertSee('Active Teachers');
        $response->assertSee('2');
        $response->assertSee('Recent Activities');
        $response->assertSee('Created teacher account');
    }

    public function test_superadmin_can_view_and_update_profile_and_change_password(): void
    {
        $superadmin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
            'name' => 'Super Admin',
            'email' => 'super@example.com',
            'password' => Hash::make('secret123'),
        ]);

        $this->actingAs($superadmin)->get(route('superadmin.profile'))->assertOk();

        $this->actingAs($superadmin)
            ->put(route('superadmin.profile.update'), [
                'name' => 'Updated Super Admin',
                'email' => 'updated@example.com',
            ])
            ->assertRedirect(route('superadmin.profile'));

        $this->actingAs($superadmin)
            ->put(route('superadmin.profile.password.update'), [
                'current_password' => 'secret123',
                'new_password' => 'newsecret456',
                'new_password_confirmation' => 'newsecret456',
            ])
            ->assertRedirect(route('superadmin.profile'));

        $superadmin->refresh();

        $this->assertSame('Updated Super Admin', $superadmin->name);
        $this->assertSame('updated@example.com', $superadmin->email);
        $this->assertTrue(Hash::check('newsecret456', $superadmin->password));
    }
}
