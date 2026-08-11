@extends('layouts.app')

@section('pageTitle', 'Subjects')
@section('pageSubtitle', 'Manage the subjects taught across your classes.')

@section('content')
<div class="flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Subjects</h2>
        <p class="mt-1 text-sm text-slate-500">Create and review the subjects linked to your classes.</p>
    </div>
    <a class="btn btn-success" href="{{ route('teacher.subjects.create') }}">Add Subject</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->classRoom?->name ?? 'Not assigned' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-slate-500">No subjects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
