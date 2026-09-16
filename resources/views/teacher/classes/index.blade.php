@extends('layouts.app')

@section('pageTitle', 'Classes')
@section('pageSubtitle', 'Manage class schedules and student enrollment.')

@section('content')
<div class="flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Classes</h2>
        <p class="mt-1 text-sm text-slate-500">Organize class rooms, schedules, and student groups.</p>
    </div>
    <a class="btn btn-success" href="{{ route('teacher.classes.create') }}">Add Class</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Class Name</th>
                        <th>Classroom</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Students</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                        <tr>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->classroom }}</td>
                            <td>{{ $class->date }}</td>
                            <td>
                                @if(!empty($class->end_time))
                                    {{ $class->time }} - {{ $class->end_time }}
                                @else
                                    {{ $class->time }}
                                @endif
                            </td>
                            <td><span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">{{ $class->enrolled_students_count ?? 0 }}</span></td>
                            <td>
                                <div class="flex flex-wrap gap-2" role="group">
                                    <a class="btn btn-sm btn-primary" href="{{ route('teacher.classes.students.index', $class) }}">Students</a>
                                    <a class="btn btn-sm btn-success" href="{{ route('teacher.classes.duplicate', ['classRoom' => $class, 'copy_from' => $class->id]) }}">Duplicate &amp; Change Schedule</a>
                                    <a class="btn btn-sm btn-secondary" href="{{ route('teacher.classes.edit', $class) }}">Edit</a>
                                    <form method="POST" action="{{ route('teacher.classes.destroy', $class) }}" onsubmit="return confirm('Delete this class?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-slate-500">No classes yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
