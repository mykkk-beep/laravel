<?php

namespace Tests\Feature;

use Tests\TestCase;

class PwaInstallTargetTest extends TestCase
{
    public function test_pwa_opens_the_teacher_dashboard_after_install(): void
    {
        $this->assertSame('/teacher/dashboard', config('laravelpwa.manifest.start_url'));
        $this->assertSame('/teacher/dashboard', json_decode(file_get_contents(public_path('manifest.json')), true)['start_url']);
    }
}
