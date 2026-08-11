<?php

namespace Tests\Unit;

use App\Models\AttendanceRecord;
use Tests\TestCase;

class AttendanceRecordTest extends TestCase
{
    public function test_classroom_relation_uses_class_room_id_foreign_key(): void
    {
        $record = new AttendanceRecord();

        $relation = $record->classRoom();

        $this->assertSame('class_room_id', $relation->getForeignKeyName());
    }
}
