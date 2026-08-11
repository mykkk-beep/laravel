<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClassScheduleSchemaTest extends TestCase
{
    public function test_classes_table_has_required_schedule_columns(): void
    {
        $this->assertTrue(Schema::hasColumn('classes', 'name'));
        $this->assertTrue(Schema::hasColumn('classes', 'classroom'));
        $this->assertTrue(Schema::hasColumn('classes', 'date'));
        $this->assertTrue(Schema::hasColumn('classes', 'time'));
        $this->assertTrue(Schema::hasColumn('classes', 'end_time'));
    }
}
