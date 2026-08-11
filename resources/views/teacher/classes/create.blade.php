@extends('layouts.app')

@section('pageTitle', 'Create Class')
@section('pageSubtitle', 'Add a new class schedule for your teaching plan.')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="text-2xl font-semibold text-slate-900">Create Class</h2>
        <form method="POST" action="{{ route('teacher.classes.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="form-label">Class Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="form-label">Classroom</label>
                <input type="text" name="classroom" class="form-input" value="{{ old('classroom') }}" required>
            </div>
            <div>
                <label class="form-label">Day of the Week</label>
                <select name="day_of_week" class="form-input" required>
                    <option value="">Select a day</option>
                    @foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)
                        <option value="{{ $day }}" {{ old('day_of_week') === $day ? 'selected' : '' }}>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label">Time</label>
                    <input type="time" name="time" class="form-input" value="{{ old('time') }}" required>
                </div>
                <div>
                    <label class="form-label">End Time (optional)</label>
                    <input type="time" name="end_time" class="form-input" value="{{ old('end_time') }}">
                </div>
            </div>
            <button class="btn btn-primary">Create Class</button>
        </form>
    </div>
</div>
@endsection
