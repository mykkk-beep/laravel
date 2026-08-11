<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticateParent
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->session()->has('parent_student_id')) {
            return redirect()->route('parent.login');
        }

        return $next($request);
    }
}
