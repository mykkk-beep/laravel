@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2 class="mb-1">Students in {{ $classRoom->name }}</h2>
    <a class="btn btn-success" href="{{ route('teacher.students.create') }}">Add Student</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Sex</th>
                <th>QR Code</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $student)
                <tr>
                    <td>{{ $student->student_id ?? '---' }}</td>
                    <td>{{ trim(implode(' ', array_filter([$student->name, $student->middle_name ?? '', $student->last_name ?? ''], fn ($value) => $value !== null && $value !== ''))) ?: ($student->name ?? '---') }}</td>
                    <td>{{ ucfirst($student->sex ?? '---') }}</td>
                    <td>
                        <code>{{ $student->student_id }}</code>
                        <div class="mt-2">
                        </div>
                    </td>
                    <td class="text-center">
                        <a class="btn btn-sm btn-warning" href="{{ route('teacher.students.edit', $student) }}">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No students in this class yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
