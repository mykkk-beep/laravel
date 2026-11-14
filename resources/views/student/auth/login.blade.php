@extends('layouts.guest')

@section('content')
<div class="w-full">
    <div class="mb-6 text-center sm:mb-8">
        <h1 class="mb-2 text-2xl font-bold text-gray-900 sm:text-3xl">Student Portal</h1>
        <p class="text-sm text-gray-600 sm:text-base">Sign in with your Student ID</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 break-words rounded-lg border border-red-200 bg-red-50 p-4" role="alert">
            <ul class="list-inside list-disc text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('student.login.submit') }}" class="space-y-5">
        @csrf

        <div>
            <label for="student_id" class="mb-2 block text-sm font-medium text-gray-700">
                Student ID
            </label>
            <input
                type="text"
                id="student_id"
                name="student_id"
                value="{{ old('student_id') }}"
                class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base focus:border-transparent focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Enter your student ID"
                required
                autofocus
            >
        </div>

        <button
            type="submit"
            class="min-h-11 w-full rounded-lg bg-blue-600 px-4 py-3 font-medium text-white transition duration-200 hover:bg-blue-700">
            Login
        </button>
    </form>

    <div class="mt-6 border-t border-gray-200 pt-4">
        <p class="text-center text-xs text-gray-500">QR Attendance System</p>
    </div>
</div>
@endsection
