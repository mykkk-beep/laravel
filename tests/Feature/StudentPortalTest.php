<?php

namespace Tests\Feature;

use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    public function test_student_login_page_is_available(): void
    {
        $response = $this->get('/student/login');

        $response->assertStatus(200);
        $response->assertViewIs('student.auth.login');
    }

    public function test_student_dashboard_redirects_to_login_when_not_authenticated(): void
    {
        $response = $this->get('/student/dashboard');

        $response->assertRedirect('/student/login');
    }
}
