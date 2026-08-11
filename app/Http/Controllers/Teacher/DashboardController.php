<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\NotificationReply;
use App\Models\Student;
use App\Models\StudentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $classes = $teacher->classes()->withCount('students')->latest()->get();

        $today = now()->toDateString();
        $todayAttendance = AttendanceRecord::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->whereDate('date', $today)->get();

        $todayClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->orderBy('time')
            ->get();

        $totalStudents = Student::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();

        $totalEnrolledStudents = Enrollment::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->where('status', Enrollment::STATUS_ENROLLED)->count();

        $presentCount = $todayAttendance->where('status', 'present')->count();
        $lateCount = $todayAttendance->where('status', 'late')->count();
        $absentCount = $todayAttendance->where('status', 'absent')->count();
        $attendanceRate = $todayAttendance->count() > 0
            ? round(($presentCount / $todayAttendance->count()) * 100, 1)
            : 0;

        $recentActivities = AttendanceRecord::with('student')
            ->whereHas('classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->latest('created_at')
            ->take(8)
            ->get();

        $upcomingClasses = ClassRoom::where('teacher_id', $teacher->id)
            ->whereDate('date', '>=', $today)
            ->orderBy('date')
            ->orderBy('time')
            ->take(5)
            ->get();

        $recommendations = $this->getRecommendations();
        $recommendationsCount = $recommendations->count();

        $messageThreads = StudentNotification::where('teacher_id', $teacher->id)
            ->with('student')
            ->orderByDesc('created_at')
            ->get();

        $startDate = now()->subDays(6)->toDateString();
        $records = AttendanceRecord::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->whereBetween('date', [$startDate, $today])->get()
            ->groupBy(function ($item) {
                return $item->date->toDateString();
            });

        $labels = [];
        $presentData = [];
        $absentData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = $date;
            $dayRecords = $records->get($date) ?? collect();
            $presentData[] = $dayRecords->where('status', 'present')->count();
            $absentData[] = $dayRecords->where('status', 'absent')->count();
        }

        $dailyAttendance = [
            'labels' => $labels,
            'present' => $presentData,
            'absent' => $absentData,
        ];

        return view('teacher.dashboard', compact(
            'classes',
            'todayClasses',
            'totalStudents',
            'totalEnrolledStudents',
            'presentCount',
            'lateCount',
            'absentCount',
            'attendanceRate',
            'recentActivities',
            'upcomingClasses',
            'recommendations',
            'recommendationsCount',
            'messageThreads',
            'dailyAttendance',
            'today'
        ));
    }

    public function notifications()
    {
        $teacher = Auth::user();
        $classes = $teacher->classes()->withCount('students')->latest()->get();
        $totalStudents = Student::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->count();

        $totalEnrolledStudents = Enrollment::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->where('status', Enrollment::STATUS_ENROLLED)->count();

        $messageThreads = StudentNotification::where('teacher_id', $teacher->id)
            ->with(['student', 'replies'])
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.notifications', compact(
            'classes',
            'totalStudents',
            'totalEnrolledStudents',
            'messageThreads'
        ));
    }

    public function recommendations()
    {
        $recommendations = $this->getRecommendations();

        $teacher = Auth::user();
        $students = Student::whereHas('classRoom', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })->get();
        $recommendationItems = $this->getRecommendationItems();

        return view('teacher.recommendations.index', compact('recommendations', 'students', 'recommendationItems'));
    }

    public function generateRecommendation(Request $request, Student $student)
    {
        $teacher = Auth::user();

        abort_unless($student->classRoom && $student->classRoom->teacher_id === $teacher->id, 403);

        $data = $request->validate([
            'message' => 'nullable|string|max:2000',
            'student_message' => 'nullable|string|max:2000',
            'priority_level' => 'nullable|string|in:Low,Medium,High',
            'save_draft' => 'nullable|boolean',
            'send_to_parent' => 'nullable|boolean',
            'send_to_student' => 'nullable|boolean',
        ]);

        $payload = $this->buildRecommendationPayload($student, $teacher, $data['priority_level'] ?? null);
        $customMessage = trim((string) ($data['message'] ?? ''));
        $studentMessage = trim((string) ($data['student_message'] ?? ''));
        $message = $this->buildRecommendationMessage($payload, $teacher, $customMessage ?: null);
        $isDraft = (bool) ($data['save_draft'] ?? false);
        $sendToParent = (bool) ($data['send_to_parent'] ?? true);
        $sendToStudent = (bool) ($data['send_to_student'] ?? false);

        if ($sendToParent) {
            StudentNotification::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'title' => $isDraft ? 'Draft Recommendation for '.$student->name : 'Recommendation for '.$student->name,
                'message' => $isDraft ? ($customMessage !== '' ? $customMessage : 'Draft recommendation saved for later review.') : $message,
                'recipient' => 'guardian',
            ]);
        }

        if ($sendToStudent) {
            StudentNotification::create([
                'student_id' => $student->id,
                'teacher_id' => $teacher->id,
                'title' => 'Student Recommendation',
                'message' => $studentMessage !== '' ? $studentMessage : ($customMessage !== '' ? $customMessage : $message),
                'recipient' => 'student',
            ]);
        }

        AttendanceRecord::where('student_id', $student->id)
            ->whereHas('classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereDate('date', '>=', now()->subDays(7)->toDateString())
            ->where('status', 'absent')
            ->update(['notified' => true]);

        return back()->with('success', "Created a recommendation for {$student->name} and sent a parent letter.");
    }

    public function notifyStudent(Student $student)
    {
        $teacher = Auth::user();

        abort_unless($student->classRoom && $student->classRoom->teacher_id === $teacher->id, 403);

        $absentRecords = AttendanceRecord::where('student_id', $student->id)
            ->whereHas('classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('status', 'absent')
            ->whereDate('date', '>=', now()->subDays(7)->toDateString())
            ->update(['notified' => true]);

        StudentNotification::create([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'title' => 'Attendance notice',
            'message' => 'Your child has recent unexcused absences and a teacher has sent this notice for review.',
            'recipient' => 'guardian',
        ]);

        if (! $absentRecords) {
            return back()->with('success', "No absent records found for {$student->name}.");
        }

        return back()->with('success', "Sent a parent notification for {$student->name}.");
    }

    public function replyToParent(Request $request, StudentNotification $notification)
    {
        $teacher = Auth::user();

        abort_unless($notification->teacher_id === $teacher->id, 403);

        $data = $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $notification->addReply(NotificationReply::SENDER_TEACHER, $data['reply']);

        return redirect()->route('teacher.notifications')->with('success', 'Reply sent to the parent.');
    }

    private function buildRecommendationPayload(Student $student, $teacher, ?string $priorityOverride = null): array
    {
        $recentRecords = AttendanceRecord::where('student_id', $student->id)
            ->whereHas('classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->whereDate('date', '>=', now()->subDays(7)->toDateString())
            ->get();

        $present = $recentRecords->where('status', 'present')->count();
        $late = $recentRecords->where('status', 'late')->count();
        $absent = $recentRecords->where('status', 'absent')->count();

        $consecutiveAbsent = 0;
        foreach ($recentRecords->sortByDesc('date')->values() as $record) {
            if ($record->status === 'absent') {
                $consecutiveAbsent++;
            } else {
                break;
            }
        }

        $enrollment = $student->enrollments()->where('status', Enrollment::STATUS_ENROLLED)->latest()->first();
        $subject = $enrollment?->subject?->name ?? ($student->classRoom?->subjects()->first()?->name ?? 'General Studies');
        $averageGrade = (float) ($student->enrollments()->where('status', Enrollment::STATUS_ENROLLED)->avg('grade') ?? 0);

        $teacherNote = $this->buildTeacherObservation($absent, $averageGrade);
        $category = $this->resolveCategory($absent, $averageGrade);
        $priority = $priorityOverride ?? $this->resolvePriority($absent, $averageGrade, $consecutiveAbsent);

        return [
            'student_name' => $student->name,
            'subject' => $subject,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'consecutive_absent' => $consecutiveAbsent,
            'average_grade' => number_format($averageGrade, 2),
            'teacher_note' => $teacherNote,
            'category' => $category,
            'priority' => $priority,
        ];
    }

    private function buildRecommendationMessage(array $payload, $teacher, ?string $customMessage = null): string
    {
        $message = <<<MESSAGE
Student Recommendation Letter

Teacher Observation
{$payload['teacher_note']}

Concern Category
{$payload['category']}

Priority Level
{$payload['priority']}

Dear Parent/Guardian,

This letter is to inform you that {$payload['student_name']} is currently experiencing a {$payload['category']} issue. Based on the teacher's observations, {$payload['teacher_note']}

We encourage you to speak with {$payload['student_name']} and provide support to improve both attendance and academic performance. Please contact the school if additional guidance or assistance is needed.

Sincerely,
{$teacher->name}
MESSAGE;

        if (! empty($customMessage)) {
            $message .= PHP_EOL.PHP_EOL.'Additional Teacher Note'.PHP_EOL.$customMessage;
        }

        return $message;
    }

    private function buildStudentPerformanceMessage(array $payload): string
    {
        $lines = [];
        $lines[] = 'Performance Summary';
        $lines[] = '';
        $lines[] = 'Student: '.$payload['student_name'];
        $lines[] = 'Subject: '.$payload['subject'];
        $lines[] = 'Present: '.$payload['present'];
        $lines[] = 'Late: '.$payload['late'];
        $lines[] = 'Absent: '.$payload['absent'];
        $lines[] = 'Average Grade: '.$payload['average_grade'];
        $lines[] = 'Priority: '.$payload['priority'];
        $lines[] = '';
        $lines[] = 'Teacher Note:';
        $lines[] = $payload['teacher_note'];

        return implode(PHP_EOL, $lines);
    }

    private function buildTeacherObservation(int $absent, float $averageGrade): string
    {
        if ($absent >= 3 && $averageGrade < 70) {
            return 'The student has missed multiple classes and is performing below expected academic standards.';
        }

        if ($absent >= 3) {
            return 'The student has shown repeated absences that are affecting class participation.';
        }

        if ($averageGrade < 70) {
            return 'The student is struggling academically and would benefit from additional support.';
        }

        return 'The student is showing a need for continued encouragement and monitoring in class.';
    }

    private function resolveCategory(int $absent, float $averageGrade): string
    {
        if ($absent >= 3 && $averageGrade < 70) {
            return 'Attendance and Academic Concern';
        }

        if ($absent >= 3) {
            return 'Attendance Concern';
        }

        if ($averageGrade < 70) {
            return 'Academic Concern';
        }

        return 'Support Needed';
    }

    private function resolvePriority(int $absent, float $averageGrade, int $consecutiveAbsent): string
    {
        if ($absent >= 3 || $consecutiveAbsent >= 2 || $averageGrade < 60) {
            return 'High';
        }

        if ($absent > 0 || $averageGrade < 75) {
            return 'Medium';
        }

        return 'Low';
    }

    private function getRecommendations()
    {
        $teacher = Auth::user();

        return Student::with(['classRoom', 'attendanceRecords'])
            ->whereHas('classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->get()
            ->filter(function (Student $student) use ($teacher) {
                $absentCount = AttendanceRecord::where('student_id', $student->id)
                    ->whereHas('classRoom', function ($query) use ($teacher) {
                        $query->where('teacher_id', $teacher->id);
                    })
                    ->where('status', 'absent')
                    ->whereDate('date', '>=', now()->subDays(7)->toDateString())
                    ->count();

                return $absentCount >= 3;
            });
    }

    private function getRecommendationItems(): Collection
    {
        $teacher = Auth::user();

        return StudentNotification::where('teacher_id', $teacher->id)
            ->where('recipient', 'guardian')
            ->where(function ($query) {
                $query->where('title', 'like', 'Recommendation for %')
                    ->orWhere('title', 'like', 'Draft Recommendation for %');
            })
            ->with(['student.classRoom'])
            ->latest('created_at')
            ->get()
            ->map(function (StudentNotification $notification) use ($teacher) {
                $student = $notification->student;
                $attendanceRecords = $student?->attendanceRecords ?? collect();
                $absentCount = $attendanceRecords->where('status', 'absent')->count();
                $lateCount = $attendanceRecords->where('status', 'late')->count();
                $presentCount = $attendanceRecords->where('status', 'present')->count();
                $attendancePercentage = $attendanceRecords->count() > 0
                    ? round(($presentCount / $attendanceRecords->count()) * 100)
                    : 100;
                $grade = optional($student?->enrollments()->where('status', Enrollment::STATUS_ENROLLED)->latest()->first())->grade;
                $category = $absentCount >= 3 ? 'Attendance' : ($grade !== null && $grade < 75 ? 'Academic Performance' : 'Behavior');
                $status = $notification->replies->isNotEmpty() ? 'Viewed' : (str_starts_with($notification->title, 'Draft Recommendation') ? 'Draft' : 'Sent');

                return [
                    'id' => $notification->id,
                    'student_name' => $student?->name ?? 'Unknown Student',
                    'class_name' => optional($student?->classRoom)->name ?? 'Unassigned',
                    'category' => $category,
                    'message' => trim((string) $notification->message) !== '' ? trim((string) $notification->message) : 'Recommendation was sent to the parent.',
                    'teacher_name' => $teacher->name ?? 'Teacher',
                    'date_sent' => $notification->created_at?->translatedFormat('M d, Y') ?? now()->translatedFormat('M d, Y'),
                    'status' => $status,
                    'attendance_percentage' => $attendancePercentage,
                    'absences' => $absentCount,
                    'late_arrivals' => $lateCount,
                    'grade' => $grade,
                ];
            })
            ->values();
    }

    public function showRecommendation(StudentNotification $notification)
    {
        $teacher = Auth::user();
        abort_unless($notification->teacher_id === $teacher->id, 403);

        return view('teacher.recommendations.show', compact('notification'));
    }

    public function editRecommendation(StudentNotification $notification)
    {
        $teacher = Auth::user();
        abort_unless($notification->teacher_id === $teacher->id, 403);

        return view('teacher.recommendations.edit', compact('notification'));
    }

    public function updateRecommendation(Request $request, StudentNotification $notification)
    {
        $teacher = Auth::user();
        abort_unless($notification->teacher_id === $teacher->id, 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'nullable|string|max:2000',
        ]);

        $notification->update($data);

        return redirect()->route('teacher.recommendations')->with('success', 'Recommendation updated.');
    }

    public function destroyRecommendation(StudentNotification $notification)
    {
        $teacher = Auth::user();
        abort_unless($notification->teacher_id === $teacher->id, 403);

        $notification->delete();

        return redirect()->route('teacher.recommendations')->with('success', 'Recommendation deleted.');
    }

    public function resendRecommendation(StudentNotification $notification)
    {
        $teacher = Auth::user();
        abort_unless($notification->teacher_id === $teacher->id, 403);

        StudentNotification::create([
            'student_id' => $notification->student_id,
            'teacher_id' => $notification->teacher_id,
            'title' => $notification->title,
            'message' => $notification->message,
            'recipient' => $notification->recipient,
        ]);

        return redirect()->route('teacher.recommendations')->with('success', 'Recommendation resent to parent.');
    }

    public function recommendationData(StudentNotification $notification)
    {
        $teacher = Auth::user();
        abort_unless($notification->teacher_id === $teacher->id, 403);

        return response()->json([
            'id' => $notification->id,
            'title' => $notification->title,
            'message' => $notification->message,
        ]);
    }
}
