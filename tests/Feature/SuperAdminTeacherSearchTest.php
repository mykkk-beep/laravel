<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTeacherSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_search_teachers_by_name_or_email(): void
    {
        $superadmin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
        ]);

        User::factory()->create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'role' => User::ROLE_TEACHER,
        ]);

        User::factory()->create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'role' => User::ROLE_TEACHER,
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard', ['search' => 'alice']));

        $response->assertOk();
        $response->assertSee('Alice Johnson');
        $response->assertDontSee('Bob Smith');
    }
}
