@extends('layouts.app')

@section('pageTitle', 'Students')
@section('pageSubtitle', 'View and manage student records for your classes.')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Manage Students</h2>
        <p class="mt-1 text-sm text-slate-500">Review student profiles and keep class records up to date.</p>
    </div>
    <a class="btn btn-success" href="{{ route('teacher.students.create') }}">Add Student</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Sex</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->student_id ?? '---' }}</td>
                            <td>{{ trim(implode(' ', array_filter([$student->name, $student->middle_name ?? '', $student->last_name ?? ''], fn ($value) => $value !== null && $value !== ''))) ?: $student->name }}</td>
                            <td>{{ ucfirst($student->sex ?? '---') }}</td>
                            <td>{{ $student->classRoom->name ?? '---' }}</td>
                            <td>
                                <div class="flex flex-wrap gap-2">
                                    <a class="btn btn-sm btn-warning" href="{{ route('teacher.students.edit', $student) }}">Edit</a>
                                    <form action="{{ route('teacher.students.destroy', $student) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-500">No students found. <a href="{{ route('teacher.students.create') }}">Add one now</a>.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
