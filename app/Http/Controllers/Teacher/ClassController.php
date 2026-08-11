<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function create()
    {
        return view('teacher.classes.create');
    }

    public function store(Request $request)
    {
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

        Auth::user()->classes()->create($data);

        return redirect()->route('teacher.classes.index')->with('success', 'Class created successfully.');
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
