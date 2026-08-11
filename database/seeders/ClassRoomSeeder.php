<?php
// database/seeders/ClassRoomSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        // Create teachers in users table
        $teacher1 = DB::table('users')->insertGetId([
            'name'       => 'Alice Santos',
            'email'      => 'alice@school.edu',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $teacher2 = DB::table('users')->insertGetId([
            'name'       => 'Ben Reyes',
            'email'      => 'ben@school.edu',
            'password'   => Hash::make('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Classrooms for teacher_id = 2 (Ben Reyes)
        $roomB = DB::table('class_rooms')->insertGetId([
            'name'       => 'Section B',
            'subject'    => 'Science',
            'teacher_id' => $teacher2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $roomC = DB::table('class_rooms')->insertGetId([
            'name'       => 'Section C',
            'subject'    => 'English',
            'teacher_id' => $teacher2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Students for Section B
        DB::table('students')->insert([
            ['name' => 'Paolo Dela Cruz', 'email' => 'paolo@student.edu', 'class_room_id' => $roomB, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maria Garcia',    'email' => 'maria@student.edu', 'class_room_id' => $roomB, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Luis Torres',     'email' => 'luis@student.edu',  'class_room_id' => $roomB, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Students for Section C
        DB::table('students')->insert([
            ['name' => 'Anna Ramos',    'email' => 'anna@student.edu', 'class_room_id' => $roomC, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carl Bautista', 'email' => 'carl@student.edu', 'class_room_id' => $roomC, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}