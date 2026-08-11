<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function send(Request $request, Student $student)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
        ]);

        StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => $request->user()->id ?? null,
            'title' => $data['title'],
            'message' => $data['message'],
            'recipient' => 'guardian',
        ]);

        return back()->with('status', 'Notification sent');
    }
}
