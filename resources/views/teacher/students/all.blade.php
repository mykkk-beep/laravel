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
        <form method="GET" action="{{ route('teacher.students.all') }}" class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center">
            <div class="w-full lg:w-64">
                <label for="class_id" class="sr-only">Class</label>
                <select name="class_id" id="class_id" class="form-select">
                    <option value="">All Classes</option>
                    @foreach($teacherClasses as $classRoom)
                        <option value="{{ $classRoom->id }}" {{ (string) $selectedClassId === (string) $classRoom->id ? 'selected' : '' }}>
                            {{ $classRoom->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex-1">
                <label for="search" class="sr-only">Search students</label>
                <input type="text" name="search" id="search" value="{{ $search }}" class="form-control" placeholder="Search by student name or ID">
            </div>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        @if($classGroups->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center text-slate-500">
                No students found for this filter. <a href="{{ route('teacher.students.all') }}" class="text-primary font-medium">Clear filters</a>.
            </div>
        @else
            @foreach($classGroups as $group)
                @php($classRoom = $group['classRoom'])
                @php($students = $group['students'])

                <div class="mb-6 rounded-[24px] border border-slate-200 bg-slate-50/80">
                    <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h3 class="text-xl font-semibold text-slate-900">{{ $classRoom->name }}</h3>
                            <p class="text-sm text-slate-500">{{ $students->count() }} student(s)</p>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="{{ route('teacher.classes.students.index', $classRoom) }}">View class</a>
                    </div>

                    <div class="overflow-x-auto p-4">
                        @if($students->isEmpty())
                            <div class="rounded-2xl border border-dashed border-slate-200 bg-white p-4 text-center text-sm text-slate-500">
                                No students found in this class.
                            </div>
                        @else
                            <table class="table mb-0">
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
                                    @foreach($students as $student)
                                        <tr>
                                            <td>{{ $student->student_id ?? '---' }}</td>
                                            <td>{{ trim(implode(' ', array_filter([$student->name, $student->middle_name ?? '', $student->last_name ?? ''], fn ($value) => $value !== null && $value !== ''))) ?: $student->name }}</td>
                                            <td>{{ ucfirst($student->sex ?? '---') }}</td>
                                            <td>{{ $student->classRoom?->name ?? '---' }}</td>
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
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
