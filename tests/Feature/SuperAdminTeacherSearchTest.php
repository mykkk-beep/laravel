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

    public function test_teacher_list_search_stays_on_the_teacher_list_page(): void
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
            ->get(route('superadmin.teachers.index', ['search' => 'alice']));

        $response->assertOk()
            ->assertViewIs('superadmin.teachers.index')
            ->assertSee('Alice Johnson')
            ->assertDontSee('Bob Smith')
            ->assertSee('action="' . route('superadmin.teachers.index') . '"', false)
            ->assertSee('href="' . route('superadmin.teachers.index') . '"', false);
    }

    public function test_teacher_list_can_sort_accounts_by_creation_date(): void
    {
        $superadmin = User::factory()->create([
            'role' => User::ROLE_SUPERADMIN,
        ]);

        User::factory()->create([
            'name' => 'Oldest Teacher',
            'role' => User::ROLE_TEACHER,
            'created_at' => now()->subDays(2),
        ]);

        User::factory()->create([
            'name' => 'Newest Teacher',
            'role' => User::ROLE_TEACHER,
            'created_at' => now(),
        ]);

        $this->actingAs($superadmin)
            ->get(route('superadmin.teachers.index', ['sort' => 'latest']))
            ->assertOk()
            ->assertSeeInOrder(['Newest Teacher', 'Oldest Teacher']);

        $this->get(route('superadmin.teachers.index', ['sort' => 'oldest']))
            ->assertOk()
            ->assertSeeInOrder(['Oldest Teacher', 'Newest Teacher']);
    }
}
