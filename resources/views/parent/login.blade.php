@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="flex min-h-[calc(100vh-4rem)] items-center justify-center px-2 py-4 sm:px-4 sm:py-8">
    <div class="w-full max-w-sm sm:max-w-md">
        <div class="card overflow-hidden">
            <div class="bg-gradient-to-r from-slate-950 to-indigo-800 px-5 py-6 text-white sm:px-8 sm:py-7">
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-200">QR Attendance</p>
                <h1 class="text-xl font-semibold sm:text-2xl">Parent Portal</h1>
                <p class="mt-1 text-sm text-indigo-100">Stay close to your child&apos;s attendance.</p>
            </div>
            <div class="p-4 sm:p-8">

    @if($errors->any())
        <div class="mb-5 break-words rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700" role="alert">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('parent.login.submit') }}" class="space-y-5">
        @csrf
        <div>
            <label for="student_id" class="mb-2 block text-sm font-semibold text-slate-700">Student ID</label>
            <input id="student_id" name="student_id" value="{{ old('student_id') }}" class="w-full min-w-0 rounded-xl border border-slate-200 px-4 py-3 text-base outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" placeholder="Enter your child's student ID" required autofocus />
            <p class="mt-2 text-xs leading-relaxed text-slate-500">Use your child&apos;s student ID to view attendance and teacher notifications.</p>
        </div>
        <button type="submit" class="min-h-11 w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">Log in</button>
    </form>
            </div>
        </div>
    </div>
</div>
@endsection
