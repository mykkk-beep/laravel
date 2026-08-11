<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Build a simple attendance summary for the selected class and date.
 */
function buildAttendanceSummary(int $classRoomId, string $date): array
{
    $records = AttendanceRecord::where('class_room_id', $classRoomId)
        ->where('date', $date)
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

        $query = AttendanceRecord::with(['student.classRoom'])
            ->where('teacher_id', $teacher->id);

        if ($request->filled('class_room_id')) {
            $query->where('class_room_id', $request->class_room_id);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->latest('created_at')->get();

        $total = $records->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $attendanceRate = $total > 0 ? round(($present / $total) * 100, 1) : 0;

        $summary = [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'attendance_rate' => $attendanceRate,
        ];

        return view('teacher.attendance.records', compact('records', 'classes', 'summary'));
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
                ->where('date', $date)
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
            ->where('date', $todayDate)
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
