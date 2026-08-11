<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Build a simple attendance summary for the selected class and date.
 */
function buildAttendanceSummary(int $classRoomId, string $date): array
{
    $records = AttendanceRecord::where('class_room_id', $classRoomId)
        ->whereDate('date', $date)
        ->get();

    $total = $records->count();
    $present = $records->where('status', 'present')->count();
    $absent = $records->where('status', 'absent')->count();

    return [
        'total' => $total,
        'present' => $present,
        'absent' => $absent,
        'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
    ];
}

class AttendanceController extends Controller
{
    private function getFilteredRecords(Request $request)
    {
        $teacher = Auth::user();

        $query = AttendanceRecord::with(['student.classRoom'])
            ->whereHas('classRoom', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            });

        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->class_room_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->latest('created_at')->get();
    }

    private function buildSummary($records): array
    {
        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $late = $records->where('status', 'late')->count();
        $absent = $records->where('status', 'absent')->count();
        $attendanceRate = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'attendance_rate' => $attendanceRate,
        ];
    }

    private function getWeekDaysForDate(string $date): array
    {
        $startOfWeek = Carbon::parse($date)->startOfWeek();
        $weekDays = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $startOfWeek->copy()->addDays($i);

            $weekDays[] = [
                'label' => $day->format('D'),
                'date' => $day->toDateString(),
                'is_selected' => $day->toDateString() === $date,
            ];
        }

        return $weekDays;
    }

    private function buildStudentAttendanceStatuses(?ClassRoom $classRoom, string $date)
    {
        if (! $classRoom) {
            return collect();
        }

        $studentIds = $classRoom->students()->pluck('id');
        $records = AttendanceRecord::where('class_room_id', $classRoom->id)
            ->whereDate('date', $date)
            ->whereIn('student_id', $studentIds)
            ->get()
            ->keyBy('student_id');

        return $classRoom->students()
            ->orderBy('name')
            ->get()
            ->map(function (Student $student) use ($records) {
                $record = $records->get($student->id);

                return [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'name' => $student->name,
                    'status' => $record?->status ?? 'absent',
                    'notes' => $record?->notes,
                ];
            });
    }

    public function scan()
    {
        $classes = Auth::user()
            ->classes()
            ->with(['students' => function ($query) {
                $query->orderBy('name')->orderBy('student_id');
            }])
            ->orderBy('name')
            ->get();

        $classStudents = $classes->mapWithKeys(function ($class) {
            return [$class->id => $class->students->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'student_id' => $student->student_id,
                    'status' => $student->status,
                ];
            })->values()];
        })->all();

        return view('teacher.attendance.scan', compact('classes', 'classStudents'));
    }

    public function records(Request $request)
    {
        $teacher = Auth::user();
        $classes = $teacher->classes()->orderBy('name')->get();
        $records = $this->getFilteredRecords($request);
        $summary = $this->buildSummary($records);
        $selectedDate = $request->date ?: today()->toDateString();
        $weekDays = $this->getWeekDaysForDate($selectedDate);
        $selectedClass = $classes->firstWhere('id', $request->class_room_id);
        $studentStatuses = $this->buildStudentAttendanceStatuses($selectedClass, $selectedDate);

        return view('teacher.attendance.records', compact('records', 'classes', 'summary', 'weekDays', 'selectedDate', 'selectedClass', 'studentStatuses'));
    }

    public function report(Request $request)
    {
        $teacher = Auth::user();
        $classes = $teacher->classes()->orderBy('name')->get();
        $records = $this->getFilteredRecords($request);
        $summary = $this->buildSummary($records);

        return view('teacher.attendance.report', compact('records', 'classes', 'summary'));
    }

    public function export(Request $request, string $format)
    {
        $records = $this->getFilteredRecords($request);
        $summary = $this->buildSummary($records);

        if ($format === 'excel') {
            $headers = ['Date', 'Student', 'Class', 'Status', 'Scanned At'];

            $callback = function () use ($headers, $records): void {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, $headers);

                foreach ($records as $record) {
                    fputcsv($handle, [
                        $record->date?->format('Y-m-d') ?? '',
                        $record->student?->name ?? '',
                        optional($record->student?->classRoom)->name ?? '',
                        ucfirst((string) $record->status),
                        $record->created_at?->format('Y-m-d H:i:s') ?? '',
                    ]);
                }

                fclose($handle);
            };

            return response()->streamDownload($callback, 'attendance-report.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8',
            ]);
        }

        if ($format === 'pdf') {
            if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                abort(500, 'PDF export is not available.');
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('teacher.attendance.pdf', compact('records', 'summary'));

            return $pdf->download('attendance-report.pdf');
        }

        abort(404);
    }

    public function initialize(Request $request)
    {
        $data = $request->validate([
            'class_room_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
        ]);

        $classRoom = ClassRoom::findOrFail($data['class_room_id']);
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $date = now()->toDateString();
        $created = 0;

        foreach ($classRoom->students as $student) {
            $record = AttendanceRecord::where('student_id', $student->id)
                ->where('class_room_id', $classRoom->id)
                ->whereDate('date', $date)
                ->first();

            if (! $record) {
                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $data['subject_id'] ?? null,
                    'teacher_id' => Auth::id(),
                    'status' => 'absent',
                    'date' => $date,
                    'qr_code' => $student->student_id,
                ]);
                $created++;
            }
        }

        return response()->json([
            'message' => 'Attendance initialized for selected class.',
            'created' => $created,
            'summary' => buildAttendanceSummary($classRoom->id, $date),
        ]);
    }

    public function markAllPresent(Request $request)
    {
        $data = $request->validate([
            'class_room_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
        ]);

        $classRoom = ClassRoom::findOrFail($data['class_room_id']);
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $todayDate = now()->toDateString();

        foreach ($classRoom->students as $student) {
            $record = AttendanceRecord::where('student_id', $student->id)
                ->where('class_room_id', $classRoom->id)
                ->whereDate('date', $todayDate)
                ->first();

            if ($record) {
                $record->update([
                    'status' => 'present',
                    'teacher_id' => Auth::id(),
                    'subject_id' => $data['subject_id'] ?? $record->subject_id,
                    'qr_code' => $student->student_id,
                    'time_in' => $record->time_in ?? now()->toTimeString(),
                ]);
            } else {
                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $data['subject_id'] ?? null,
                    'teacher_id' => Auth::id(),
                    'status' => 'present',
                    'date' => $todayDate,
                    'time_in' => now()->toTimeString(),
                    'qr_code' => $student->student_id,
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'All students marked present for the selected class.',
                'summary' => buildAttendanceSummary($classRoom->id, $todayDate),
            ]);
        }

        return back()->with('success', 'All students marked present for the selected class.');
    }

    public function finalizeQuick(Request $request)
    {
        $data = $request->validate([
            'class_room_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
        ]);

        $classRoom = ClassRoom::findOrFail($data['class_room_id']);
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $todayDate = now()->toDateString();
        $records = AttendanceRecord::where('class_room_id', $classRoom->id)
            ->whereDate('date', $todayDate)
            ->get();

        $presentStudentIds = $records->where('status', 'present')->pluck('student_id')->all();

        foreach ($classRoom->students as $student) {
            $record = $records->firstWhere('student_id', $student->id);

            if (! $record) {
                AttendanceRecord::create([
                    'student_id' => $student->id,
                    'class_room_id' => $classRoom->id,
                    'subject_id' => $data['subject_id'] ?? null,
                    'teacher_id' => Auth::id(),
                    'status' => in_array($student->id, $presentStudentIds, true) ? 'present' : 'absent',
                    'date' => $todayDate,
                    'qr_code' => $student->student_id,
                ]);

                continue;
            }

            if ($record->status !== 'present' && in_array($student->id, $presentStudentIds, true)) {
                $record->update([
                    'status' => 'present',
                    'teacher_id' => Auth::id(),
                    'subject_id' => $data['subject_id'] ?? $record->subject_id,
                    'qr_code' => $student->student_id,
                ]);
            } elseif ($record->status === 'present' && ! in_array($student->id, $presentStudentIds, true)) {
                $record->update([
                    'status' => 'absent',
                    'teacher_id' => Auth::id(),
                    'subject_id' => $data['subject_id'] ?? $record->subject_id,
                    'qr_code' => $student->student_id,
                ]);
            } elseif ($record->status !== 'present') {
                $record->update([
                    'status' => 'absent',
                    'teacher_id' => Auth::id(),
                    'subject_id' => $data['subject_id'] ?? $record->subject_id,
                    'qr_code' => $student->student_id,
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Quick attendance finalized for the selected class.',
                'summary' => buildAttendanceSummary($classRoom->id, $todayDate),
            ]);
        }

        return back()->with('success', 'Quick attendance finalized for the selected class.');
    }

    public function record(Request $request)
    {
        $data = $request->validate([
            'class_room_id' => ['required', 'exists:classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'qr_code' => ['required', 'string'],
        ]);

        $classRoom = ClassRoom::findOrFail($data['class_room_id']);

        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $qrCode = trim($data['qr_code']);

        $student = Student::where('class_room_id', $classRoom->id)
            ->where(function ($query) use ($qrCode) {
                $query->where('student_id', $qrCode)
                    ->orWhere('uuid', $qrCode);
            })
            ->first();

        if (! $student) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Student not found in the selected class.'], 422);
            }

            return back()->withErrors(['qr_code' => 'Student not found in the selected class.']);
        }

        $hasEnrollment = $student->currentEnrollmentForClass($classRoom->id) !== null;
        $isEligibleForScan = in_array($student->status, [null, '', Student::STATUS_ENROLLED], true)
            || ($student->status === Student::STATUS_PENDING && ! $hasEnrollment);

        if (! $isEligibleForScan || $student->status === Student::STATUS_NOT_ENROLLED) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'This student is not yet enrolled. Please enroll the student before scanning.',
                ], 422);
            }

            return back()->withErrors(['qr_code' => 'This student is not yet enrolled. Please enroll the student before scanning.']);
        }

        $todayDate = now()->toDateString();
        $todayAttendance = AttendanceRecord::where('student_id', $student->id)
            ->where('class_room_id', $classRoom->id)
            ->whereDate('date', $todayDate)
            ->first();

        if ($todayAttendance) {
            if ($todayAttendance->status === 'present') {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Attendance already recorded for {$student->name} today.",
                        'status' => 'already_present',
                        'student' => $student->only(['id', 'name', 'student_id']),
                        'summary' => buildAttendanceSummary($classRoom->id, $todayDate),
                    ], 409);
                }

                return back()->withErrors(['qr_code' => "Attendance already recorded for {$student->name} today."]);
            }

            $todayAttendance->update([
                'status' => 'present',
                'teacher_id' => Auth::id(),
                'subject_id' => $data['subject_id'] ?? $todayAttendance->subject_id,
                'qr_code' => $data['qr_code'],
                'time_in' => now()->toTimeString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Attendance recorded for {$student->name}.",
                    'status' => 'present',
                    'student' => $student->only(['id', 'name', 'student_id']),
                    'summary' => buildAttendanceSummary($classRoom->id, $todayDate),
                ]);
            }

            return back()->with('success', "Attendance recorded for {$student->name}.");
        }

        AttendanceRecord::create([
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'subject_id' => $data['subject_id'] ?? null,
            'teacher_id' => Auth::id(),
            'status' => 'present',
            'date' => $todayDate,
            'time_in' => now()->toTimeString(),
            'qr_code' => $data['qr_code'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => "Attendance recorded for {$student->name}.",
                'status' => 'present',
                'student' => $student->only(['id', 'name', 'student_id']),
                'summary' => buildAttendanceSummary($classRoom->id, $todayDate),
            ]);
        }

        return back()->with('success', "Attendance recorded for {$student->name}.");
    }
}
