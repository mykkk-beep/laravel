@extends('layouts.app')

@section('pageTitle', 'Add Subject')
@section('pageSubtitle', 'Create a new subject and link it to a class if needed.')

@section('content')
<div class="card">
    <div class="card-body">
        <h2 class="text-2xl font-semibold text-slate-900">Add Subject</h2>
        <form method="POST" action="{{ route('teacher.subjects.store') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="form-label">Subject Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
            </div>
            <div>
                <label class="form-label">Subject Code</label>
                <input type="text" name="code" class="form-input" value="{{ old('code') }}" required>
            </div>
            <div>
                <label class="form-label">Class</label>
                <select name="class_room_id" class="form-input">
                    <option value="">Unassigned</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary">Create Subject</button>
        </form>
    </div>
</div>
@endsection
