<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StudentPortalController extends Controller
{
    /**
     * Show the student login form
     */
    public function showLogin()
    {
        return view('student.auth.login');
    }

    /**
     * Handle student login via student ID
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'student_id' => ['required', 'string'],
        ]);

        $student = Student::where('student_id', $credentials['student_id'])->first();

        if (!$student) {
            return back()->withErrors(['student_id' => 'Student ID not found.']);
        }

        // Store student in session
        Session::put('student_id', $student->id);
        Session::put('student_name', $student->name);
        Session::put('authenticated_student', true);

        $request->session()->regenerate();

        return redirect()->route('student.dashboard');
    }

    /**
     * Show student dashboard
     */
    public function dashboard()
    {
        $studentId = Session::get('student_id');
        $student = Student::findOrFail($studentId);

        // Get attendance records
        try {
            $attendanceRecords = $student->attendanceRecords()
                ->with('classRoom')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            // Calculate attendance statistics
            $totalClasses = $student->attendanceRecords()
                ->distinct('class_room_id')
                ->count();

            $presentCount = $student->attendanceRecords()
                ->where('status', 'present')
                ->count();

            $attendancePercentage = $totalClasses > 0 
                ? round(($presentCount / $totalClasses) * 100, 2) 
                : 0;
        } catch (\Exception $e) {
            // If there's an error querying attendance, use default values
            $attendanceRecords = collect();
            $totalClasses = 0;
            $presentCount = 0;
            $attendancePercentage = 0;
        }

        return view('student.dashboard', [
            'student' => $student,
            'attendanceRecords' => $attendanceRecords,
            'totalClasses' => $totalClasses,
            'presentCount' => $presentCount,
            'attendancePercentage' => $attendancePercentage,
        ]);
    }

    /**
     * Show student profile
     */
    public function profile()
    {
        $studentId = Session::get('student_id');
        $student = Student::findOrFail($studentId);

        return view('student.profile', ['student' => $student]);
    }

    /**
     * Logout student
     */
    public function logout(Request $request)
    {
        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('student.login');
    }
}
