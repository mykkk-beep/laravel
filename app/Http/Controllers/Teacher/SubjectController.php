<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Auth::user()->subjects()->latest()->get();

        return view('teacher.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $classes = Auth::user()->classes()->orderBy('name')->get();

        return view('teacher.subjects.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:64'],
            'class_room_id' => ['nullable', 'exists:classes,id'],
        ]);

        Auth::user()->subjects()->create($data);

        return redirect()->route('teacher.subjects.index')->with('success', 'Subject created successfully.');
    }
}
