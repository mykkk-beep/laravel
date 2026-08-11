<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\Student;
use App\Models\StudentNotification;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::with(['classRoom', 'attendanceRecords'])->findOrFail($request->session()->get('parent_student_id'));

        $today = now()->toDateString();
        $attendance = AttendanceRecord::where('student_id', $student->id)
            ->where('date', $today)
            ->latest()
            ->first();

        $recentAttendance = AttendanceRecord::where('student_id', $student->id)
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->take(5)
            ->get();

        $notifications = StudentNotification::where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $notificationCount = $notifications->count();

        return view('parent.dashboard', [
            'student' => $student,
            'attendance' => $attendance,
            'recentAttendance' => $recentAttendance,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
        ]);
    }

    public function notifications(Request $request)
    {
        $student = Student::findOrFail($request->session()->get('parent_student_id'));
        $notifications = StudentNotification::where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('parent.notifications', [
            'student' => $student,
            'notifications' => $notifications,
        ]);
    }

    public function reply(Request $request, StudentNotification $notification)
    {
        $studentId = $request->session()->get('parent_student_id');

        abort_unless($notification->student_id === $studentId, 403);

        $data = $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $notification->appendReply('parent', $data['reply']);

        return redirect()->route('parent.notifications')->with('success', 'Reply sent to the teacher.');
    }

    public function destroy(Request $request, StudentNotification $notification)
    {
        $studentId = $request->session()->get('parent_student_id');

        abort_unless($notification->student_id === $studentId, 403);

        $notification->delete();

        return redirect()->route('parent.notifications')->with('success', 'Notification deleted.');
    }
}
