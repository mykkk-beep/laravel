<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private function buildFullName(array $data): string
    {
        return trim(implode(' ', array_filter([
            $data['name'] ?? '',
            $data['middle_name'] ?? '',
            $data['last_name'] ?? '',
        ], static function ($value): bool {
            return $value !== null && $value !== '';
        })));
    }

    public function allStudents()
    {
        $teacherClassIds = Auth::user()->classes()->pluck('classes.id');

        $students = Student::with('classRoom')
            ->whereIn('class_room_id', $teacherClassIds)
            ->orderBy('name')
            ->get();

        return view('teacher.students.all', compact('students'));
    }

    public function index(ClassRoom $classRoom)
    {
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $students = $classRoom->students()
            ->orderBy('name')
            ->get();

        return view('teacher.students.index', compact('classRoom', 'students'));
    }

    public function createFromDashboard()
    {
        $classes = Auth::user()->classes()->orderBy('name')->get();

        return view('teacher.students.create', compact('classes'));
    }

    public function create(ClassRoom $classRoom)
    {
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $classes = Auth::user()->classes()->orderBy('name')->get();

        return view('teacher.students.create', compact('classRoom', 'classes'));
    }

    public function storeFromDashboard(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', 'in:male,female,other'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'class_room_id' => ['nullable', 'exists:classes,id'],
        ]);

        $classRoomId = null;

        if (! empty($data['class_room_id'])) {
            $classRoom = Auth::user()->classes()
                ->where('id', $data['class_room_id'])
                ->first();

            if ($classRoom) {
                $classRoomId = $classRoom->id;
            }
        }

        $fullName = $this->buildFullName($data);

        $student = Student::create([
            'student_id' => $data['student_id'],
            'name' => $fullName,
            'sex' => $data['sex'],
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'class_room_id' => $classRoomId,
            'status' => $classRoomId ? 'pending' : 'not_enrolled',
        ]);

        if ($classRoomId) {
            $student->enrollments()->create([
                'class_room_id' => $classRoomId,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('teacher.students.all')
            ->with('success', "Student {$student->name} added successfully.");
    }

    public function store(Request $request, ClassRoom $classRoom)
    {
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $data = $request->validate([
            'student_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', 'in:male,female,other'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        $fullName = $this->buildFullName($data);

        $student = Student::create([
            'student_id' => $data['student_id'],
            'name' => $fullName,
            'sex' => $data['sex'],
            'mobile' => $data['mobile'] ?? null,
            'email' => $data['email'] ?? null,
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
        ]);

        $student->enrollments()->create([
            'class_room_id' => $classRoom->id,
            'status' => 'pending',
        ]);

        return redirect()->route('teacher.classes.students.index', $classRoom)
            ->with('success', "Student {$student->name} added successfully.");
    }

    public function enroll(Request $request, Student $student)
    {
        $classRoomId = $request->input('class_room_id', $student->class_room_id);
        $classRoom = Auth::user()->classes()
            ->where('id', $classRoomId)
            ->first();

        abort_unless($classRoom, 403);

        $enrollment = $student->enrollments()->where('class_room_id', $classRoom->id)->first();

        if (! $enrollment) {
            $enrollment = $student->enrollments()->create([
                'class_room_id' => $classRoom->id,
                'status' => Enrollment::STATUS_ENROLLED,
                'grade' => 0,
            ]);
        } else {
            $enrollment->update(['status' => Enrollment::STATUS_ENROLLED]);
        }

        $student->update([
            'status' => Student::STATUS_ENROLLED,
            'class_room_id' => $student->class_room_id ?? $classRoom->id,
        ]);

        return back()->with('success', "Student {$student->name} has been enrolled in {$classRoom->name}.");
    }

    public function enrollAll(ClassRoom $classRoom)
    {
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $students = $classRoom->students()->get();
        $updatedCount = 0;

        foreach ($students as $student) {
            if ($student->status === Student::STATUS_ENROLLED) {
                continue;
            }

            $student->update(['status' => Student::STATUS_ENROLLED]);

            $enrollment = $student->enrollments()->where('class_room_id', $classRoom->id)->first();

            if (! $enrollment) {
                $student->enrollments()->create([
                    'class_room_id' => $classRoom->id,
                    'status' => Enrollment::STATUS_ENROLLED,
                    'grade' => 0,
                ]);
            } else {
                $enrollment->update(['status' => Enrollment::STATUS_ENROLLED]);
            }

            $updatedCount++;
        }

        return back()->with('success', "{$updatedCount} student(s) have been enrolled in {$classRoom->name}.");
    }

    public function destroy(Student $student)
    {
        $classRoom = $student->classRoom;
        
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $student->delete();

        return redirect()->route('teacher.students.all')->with('success', 'Student deleted successfully.');
    }

    public function edit(Student $student)
    {
        $classRoom = $student->classRoom;

        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $classes = Auth::user()->classes()->orderBy('name')->get();

        return view('teacher.students.edit', compact('student', 'classRoom', 'classes'));
    }

    public function update(Request $request, Student $student)
    {
        $classRoom = $student->classRoom;

        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $data = $request->validate([
            'student_id' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', 'in:male,female,other'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'class_room_id' => ['required', 'exists:classes,id'],
        ]);

        // Verify new class belongs to teacher
        $newClass = Auth::user()->classes()
            ->where('id', $data['class_room_id'])
            ->firstOrFail();

        $data['name'] = $this->buildFullName($data);

        $student->update($data);

        return redirect()->route('teacher.students.all')->with('success', 'Student updated successfully.');
    }
}
