@extends('layouts.app')

@section('pageTitle', 'Add Student')
@section('pageSubtitle', 'Create a new student profile and assign them to a class.')

@section('content')
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if(isset($classRoom))
            <h2 class="text-2xl font-semibold text-slate-900">Add Student to {{ $classRoom->name }}</h2>
            <form method="POST" action="{{ route('teacher.classes.students.store', $classRoom) }}" class="mt-6 space-y-4">
            <input type="hidden" name="class_room_id" value="{{ $classRoom->id }}">
        @else
            <h2 class="text-2xl font-semibold text-slate-900">Add Student</h2>
            <form method="POST" action="{{ route('teacher.students.store') }}" class="mt-6 space-y-4">
        @endif
            @csrf
            @if(!isset($classRoom) && isset($classes))
                <div>
                    <label class="form-label">Class <span class="text-sm text-slate-500">(optional)</span></label>
                    <select name="class_room_id" class="form-input">
                        <option value="">No class assigned</option>
                        @forelse($classes as $teacherClass)
                            <option value="{{ $teacherClass->id }}" {{ old('class_room_id') == $teacherClass->id ? 'selected' : '' }}>
                                {{ $teacherClass->name }} ({{ $teacherClass->classroom }})
                            </option>
                        @empty
                            <option value="" disabled>No classes available. Create a class first.</option>
                        @endforelse
                    </select>
                </div>
            @endif

            <div>
                <label class="form-label">Student ID</label>
                <input type="text" name="student_id" class="form-input" value="{{ old('student_id') }}" required>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label">First Name</label>
                    <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-input" value="{{ old('last_name') }}">
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="form-label">Gender</label>
                    <select name="sex" class="form-input" required>
                        <option value="">Select gender</option>
                        <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('sex') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="enrolled" {{ old('status') == 'enrolled' ? 'selected' : '' }}>Enrolled</option>
                        <option value="not_enrolled" {{ old('status') == 'not_enrolled' ? 'selected' : '' }}>Not Enrolled</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}">
            </div>
            <div class="flex flex-wrap gap-3">
                <button class="btn btn-primary" type="submit">Save Student</button>
                <a href="{{ route('teacher.students.all') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
