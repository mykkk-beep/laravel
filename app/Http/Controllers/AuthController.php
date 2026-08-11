<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user?->role === User::ROLE_TEACHER) {
                if ($user->status === User::STATUS_PENDING) {
                    Auth::logout();

                    return back()->withErrors(['email' => 'Account is pending, wait for the administrator verification.']);
                }

                if (! $user->active) {
                    Auth::logout();

                    return back()->withErrors(['email' => 'This account has been disabled by the administrator.']);
                }
            }

            $request->session()->regenerate();

            if ($user->role === User::ROLE_SUPERADMIN) {
                return redirect()->route('superadmin.dashboard');
            }

            return redirect()->route('teacher.dashboard');
        }

        return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
