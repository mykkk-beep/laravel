<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Auth::user()
            ->classes()
            ->withCount(['enrollments as enrolled_students_count' => function ($query) {
                $query->where('status', Enrollment::STATUS_ENROLLED);
            }])
            ->latest()
            ->get();

        return view('teacher.classes.index', compact('classes'));
    }

    public function create(Request $request, ?ClassRoom $classRoom = null)
    {
        $sourceClass = $classRoom;

        if (! $sourceClass && $request->filled('copy_from')) {
            $sourceClass = Auth::user()->classes()
                ->findOrFail($request->integer('copy_from'));
        }

        return view('teacher.classes.create', compact('sourceClass'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'classroom' => ['required', 'string', 'max:255'],
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'copy_from' => ['nullable', 'integer'],
        ]);

        $data['date'] = Carbon::now()->next($data['day_of_week'])->toDateString();
        unset($data['day_of_week']);

        if (!empty($data['end_time']) && strtotime($data['end_time']) <= strtotime($data['time'])) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        $sourceClassId = $data['copy_from'] ?? null;
        unset($data['copy_from']);

        $classRoom = DB::transaction(function () use ($data, $sourceClassId) {
            $classRoom = Auth::user()->classes()->create($data);

            if (! $sourceClassId) {
                return $classRoom;
            }

            $sourceClass = Auth::user()->classes()
                ->with('enrollments')
                ->findOrFail($sourceClassId);

            foreach ($sourceClass->enrollments as $enrollment) {
                $classRoom->enrollments()->firstOrCreate(
                    ['student_id' => $enrollment->student_id],
                    [
                        'status' => $enrollment->status,
                        'grade' => 0,
                    ]
                );
            }

            // Support older classes created before enrollments were introduced.
            foreach ($sourceClass->students()->whereDoesntHave('enrollments', function ($query) use ($sourceClass) {
                $query->where('class_room_id', $sourceClass->id);
            })->get() as $student) {
                $classRoom->enrollments()->firstOrCreate(
                    ['student_id' => $student->id],
                    [
                        'status' => $student->status === 'not_enrolled' ? 'not_enrolled' : 'enrolled',
                        'grade' => 0,
                    ]
                );
            }

            return $classRoom;
        });

        $message = $sourceClassId
            ? 'Class duplicated with the same students. Update its schedule as needed.'
            : 'Class created successfully.';

        return redirect()->route('teacher.classes.index')->with('success', $message);
    }


    public function edit(ClassRoom $classRoom)
    {
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        return view('teacher.classes.edit', compact('classRoom'));
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'classroom' => ['required', 'string', 'max:255'],
            'day_of_week' => ['required', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $data['date'] = Carbon::now()->next($data['day_of_week'])->toDateString();
        unset($data['day_of_week']);

        if (!empty($data['end_time']) && strtotime($data['end_time']) <= strtotime($data['time'])) {
            return back()->withErrors(['end_time' => 'End time must be after start time.'])->withInput();
        }

        $classRoom->update($data);

        return redirect()->route('teacher.classes.index')->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassRoom $classRoom)
    {
        abort_unless($classRoom->teacher_id === Auth::id(), 403);

        $classRoom->delete();

        return redirect()->route('teacher.classes.index')->with('success', 'Class deleted successfully.');
    }
}
