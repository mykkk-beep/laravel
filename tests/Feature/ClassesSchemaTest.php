<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClassesSchemaTest extends TestCase
{
    public function test_classes_table_has_teacher_id_column(): void
    {
        $this->assertTrue(Schema::hasColumn('classes', 'teacher_id'));
    }
}
