@extends('layouts.app')

@section('pageTitle', 'Edit Class')
@section('pageSubtitle', 'Update the class details and schedule.')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="text-2xl font-semibold text-slate-900">Edit Class</h2>
        <form method="POST" action="{{ route('teacher.classes.update', $classRoom) }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="form-label">Class Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $classRoom->name) }}" required>
            </div>
            <div>
                <label class="form-label">Classroom</label>
                <input type="text" name="classroom" class="form-input" value="{{ old('classroom', $classRoom->classroom) }}" required>
            </div>
            <div>
                <label class="form-label">Day of the Week</label>
                <select name="day_of_week" class="form-input" required>
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                        <option value="{{ $day }}" {{ old('day_of_week', $classRoom->date ? \Carbon\Carbon::parse($classRoom->date)->format('l') : '') === $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>
                <div class="mt-2 text-sm text-slate-500">Select the day to recalculate the next date for this class.</div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Time</label>
                    <input type="time" name="time" class="form-input" value="{{ old('time', $classRoom->time) }}" required>
                </div>
                <div>
                    <label class="form-label">End Time (optional)</label>
                    <input type="time" name="end_time" class="form-input" value="{{ old('end_time', $classRoom->end_time) }}">
                </div>
            </div>
            <button class="btn btn-primary">Update Class</button>
        </form>
    </div>
</div>
@endsection
