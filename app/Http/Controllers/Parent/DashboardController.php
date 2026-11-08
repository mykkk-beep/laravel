<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AttendanceExcuse;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\NotificationReply;
use App\Models\Student;
use App\Models\StudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = Student::with(['classRoom', 'attendanceRecords', 'enrollments.classRoom'])->findOrFail($request->session()->get('parent_student_id'));
        $classSchedules = $student->enrollments
            ->where('status', Enrollment::STATUS_ENROLLED)
            ->map(fn (Enrollment $enrollment) => $enrollment->classRoom)
            ->push($student->classRoom)
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->values();

        $today = now()->toDateString();
        $attendance = AttendanceRecord::where('student_id', $student->id)
            ->whereDate('date', $today)
            ->latest()
            ->first();

        $recentAttendance = AttendanceRecord::where('student_id', $student->id)
            ->orderByDesc('date')
            ->orderByDesc('time_in')
            ->take(5)
            ->get();

        $notifications = StudentNotification::where('student_id', $student->id)
            ->where('recipient', 'guardian')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $notificationCount = $notifications->count();
        $attendanceExcuses = AttendanceExcuse::with('classRoom')
            ->where('student_id', $student->id)
            ->latest('date')
            ->take(10)
            ->get();

        return view('parent.dashboard', [
            'student' => $student,
            'attendance' => $attendance,
            'recentAttendance' => $recentAttendance,
            'notifications' => $notifications,
            'notificationCount' => $notificationCount,
            'classSchedules' => $classSchedules,
            'attendanceExcuses' => $attendanceExcuses,
        ]);
    }

    public function submitAttendanceExcuse(Request $request)
    {
        $student = Student::findOrFail($request->session()->get('parent_student_id'));
        $data = $request->validate([
            'class_room_id' => ['required', 'exists:classes,id'],
            'date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);
        $classRoom = ClassRoom::findOrFail($data['class_room_id']);
        $isAssigned = (int) $student->class_room_id === (int) $classRoom->id
            || $student->enrollments()
                ->where('class_room_id', $classRoom->id)
                ->where('status', Enrollment::STATUS_ENROLLED)
                ->exists();
        abort_unless($isAssigned && $classRoom->teacher_id, 403);

        $excuse = AttendanceExcuse::firstOrNew([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'date' => $data['date'],
        ]);
        if ($excuse->exists && $excuse->status !== AttendanceExcuse::STATUS_REJECTED) {
            return back()->withErrors(['date' => 'An excuse for this class and date has already been submitted.']);
        }

        DB::transaction(function () use ($excuse, $student, $classRoom, $data) {
            $excuse->fill([
                'teacher_id' => $classRoom->teacher_id,
                'reason' => $data['reason'],
                'status' => AttendanceExcuse::STATUS_PENDING,
                'reviewed_at' => null,
            ])->save();

            StudentNotification::create([
                'student_id' => $student->id,
                'teacher_id' => $classRoom->teacher_id,
                'title' => 'Absence excuse submitted by guardian',
                'message' => $student->name.'\'s guardian submitted an absence excuse for '.$classRoom->name.' on '.$data['date'].'. Reason: '.$data['reason'],
                'recipient' => 'teacher',
            ]);
        });

        return redirect()->route('parent.dashboard')->with('success', 'The excuse was sent to the teacher for review.');
    }

    public function notifications(Request $request)
    {
        $student = Student::findOrFail($request->session()->get('parent_student_id'));
        $notifications = StudentNotification::where('student_id', $student->id)
            ->where('recipient', 'guardian')
            ->with('replies')
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

        $notification->addReply(NotificationReply::SENDER_PARENT, $data['reply']);

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
