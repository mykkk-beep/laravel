<?php

namespace App\Http\Controllers;

use App\Models\NotificationReply;
use App\Models\Student;
use App\Models\StudentNotification;
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

        $teacherMessages = StudentNotification::where('student_id', $student->id)
            ->where('recipient', 'student')
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $teacherMessages->count();

        return view('student.dashboard', [
            'student' => $student,
            'attendanceRecords' => $attendanceRecords,
            'totalClasses' => $totalClasses,
            'presentCount' => $presentCount,
            'attendancePercentage' => $attendancePercentage,
            'notificationCount' => $notificationCount,
        ]);
    }

    /**
     * Show student notifications
     */
    public function notifications(Request $request)
    {
        $studentId = Session::get('student_id');
        $student = Student::findOrFail($studentId);

        $teacherMessages = StudentNotification::where('student_id', $student->id)
            ->where('recipient', 'student')
            ->with('replies')
            ->orderBy('created_at', 'desc')
            ->get();

        $recentAttendance = $student->attendanceRecords()
            ->with('classRoom')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('student.notifications', [
            'student' => $student,
            'notifications' => $teacherMessages,
            'recentAttendance' => $recentAttendance,
        ]);
    }

    /**
     * Add reply to notification
     */
    public function reply(Request $request, StudentNotification $notification)
    {
        $studentId = Session::get('student_id');

        abort_unless($notification->student_id === $studentId, 403);

        $data = $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $notification->addReply(NotificationReply::SENDER_PARENT, $data['reply']);

        return redirect()->route('student.notifications')->with('success', 'Reply sent to your teacher.');
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, StudentNotification $notification)
    {
        $studentId = Session::get('student_id');

        abort_unless($notification->student_id === $studentId, 403);

        $notification->delete();

        return redirect()->route('student.notifications')->with('success', 'Notification deleted.');
    }

    /**
     * Show student profile
     */
    public function profile()
    {
        $studentId = Session::get('student_id');
        $student = Student::with(['classRoom', 'enrollments.classRoom', 'enrollments.subject'])->findOrFail($studentId);

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
