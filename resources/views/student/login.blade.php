@extends('layouts.app')

@section('content')
<div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-1 py-8 sm:px-0">
    <div class="w-full max-w-md">
        <div class="card overflow-hidden">
            <div class="bg-gradient-to-r from-slate-950 to-indigo-800 px-6 py-7 text-white sm:px-8">
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-200">QR Attendance</p>
                <h1 class="text-2xl font-semibold">Student Portal</h1>
                <p class="mt-1 text-sm text-indigo-100">Keep track of your attendance progress.</p>
            </div>
            <div class="p-6 sm:p-8">

                    @if($errors->any())
                        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700" role="alert">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('student.login.submit') }}">
                        @csrf
                        <div>
                            <label for="student_id" class="mb-2 block text-sm font-semibold text-slate-700">Student ID</label>
                            <input 
                                id="student_id" 
                                name="student_id" 
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" 
                                placeholder="Enter your student ID"
                                required 
                                autofocus />
                            @error('student_id')
                                <div class="mt-2 text-xs text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="mt-6 min-h-11 w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            Log in
                        </button>
                    </form>

                    <div class="mt-6 border-t border-slate-200 pt-4 text-center">
                        <p class="mb-0 text-xs text-slate-500">Having trouble? Contact your teacher or administration for assistance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
