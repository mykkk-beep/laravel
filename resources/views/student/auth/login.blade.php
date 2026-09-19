@extends('layouts.guest')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md rounded-lg bg-white p-5 shadow-lg sm:p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Student Portal</h1>
            <p class="text-gray-600">Sign in with your Student ID</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <ul class="list-disc list-inside text-red-700 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('student.login.submit') }}" class="space-y-6">
            @csrf

            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Student ID
                </label>
                <input 
                    type="text" 
                    id="student_id" 
                    name="student_id" 
                    value="{{ old('student_id') }}"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your student ID"
                    required
                    autofocus
                >
            </div>

            <button 
                type="submit" 
                class="min-h-11 w-full rounded-lg bg-blue-600 px-4 py-3 font-medium text-white transition duration-200 hover:bg-blue-700"
            >
                Login
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Are you a teacher? <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">Click here</a></p>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center">QR Attendance System</p>
        </div>
    </div>
</div>
@endsection
