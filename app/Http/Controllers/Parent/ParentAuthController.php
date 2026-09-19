<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class ParentAuthController extends Controller
{
    public function showLogin()
    {
        return view('parent.login');
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

        $request->session()->put('parent_student_id', $student->id);
        return redirect()->route('parent.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('parent.login');
    }
}
