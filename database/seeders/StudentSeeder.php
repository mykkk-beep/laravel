<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            ['name' => 'Test Teacher', 'password' => bcrypt('password'), 'role' => User::ROLE_TEACHER, 'active' => true]
        );

        $class = ClassRoom::firstOrCreate(
            ['name' => 'Test Class'],
            ['classroom' => 'A1', 'date' => now()->toDateString(), 'time' => now()->format('H:i'), 'teacher_id' => $teacher->id]
        );

        Student::firstOrCreate(
            ['student_id' => 'S1001'],
            ['name' => 'Test Student', 'email' => 'student@example.com', 'class_room_id' => $class->id]
        );
    }
}
