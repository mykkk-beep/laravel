<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateStudent
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('student_id')) {
            return redirect()->route('student.login');
        }

        return $next($request);
    }
}
