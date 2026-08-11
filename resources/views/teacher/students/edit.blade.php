@extends('layouts.app')

@section('pageTitle', 'Edit Student')
@section('pageSubtitle', 'Update the selected student profile.')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="text-2xl font-semibold text-slate-900">Edit Student</h2>
        <form method="POST" action="{{ route('teacher.students.update', $student) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="form-label">Class</label>
                <select name="class_room_id" class="form-input @error('class_room_id') border-rose-300 @enderror" required>
                    <option value="">Select class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ $student->class_room_id == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
                @error('class_room_id')
                    <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="form-label">Student ID</label>
                <input type="text" name="student_id" class="form-input @error('student_id') border-rose-300 @enderror" value="{{ old('student_id', $student->student_id) }}" required>
                @error('student_id')
                    <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <label class="form-label">Student Name</label>
                <input type="text" name="name" class="form-input @error('name') border-rose-300 @enderror" value="{{ old('name', $student->name) }}" required>
                @error('name')
                    <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Sex</label>
                    <select name="sex" class="form-input @error('sex') border-rose-300 @enderror" required>
                        <option value="">Select sex</option>
                        <option value="male" {{ old('sex', $student->sex) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('sex', $student->sex) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('sex', $student->sex) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('sex')
                        <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input @error('email') border-rose-300 @enderror" value="{{ old('email', $student->email) }}">
                @error('email')
                    <div class="mt-1 text-sm text-rose-600">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="btn btn-primary">Update Student</button>
                <a href="{{ route('teacher.students.all') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
