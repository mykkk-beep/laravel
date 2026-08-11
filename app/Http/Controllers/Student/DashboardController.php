<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentNotification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::with('classRoom')->findOrFail($request->session()->get('student_id'));
        $notificationCount = StudentNotification::where('student_id', $student->id)->count();

        return view('student.dashboard', [
            'student' => $student,
            'notificationCount' => $notificationCount,
        ]);
    }

    public function notifications(Request $request)
    {
        $student = Student::findOrFail($request->session()->get('student_id'));

        $notifications = StudentNotification::where('student_id', $student->id)->orderBy('created_at', 'desc')->get();

        return view('student.notifications', [
            'student' => $student,
            'notifications' => $notifications,
        ]);
    }
}
