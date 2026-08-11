<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentAuthController extends Controller
{
    public function showLogin()
    {
        return view('student.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|string',
        ]);

        $student = Student::where('student_id', $data['student_id'])->first();

        if (!$student) {
            return back()->withErrors(['student_id' => 'Student ID not found.']);
        }

        $request->session()->put('student_id', $student->id);

        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('student_id');
        return redirect()->route('student.login');
    }
}
