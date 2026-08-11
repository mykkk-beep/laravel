@extends('layouts.app')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <h2 class="mb-1">Students in {{ $classRoom->name }}</h2>
    @if($students->contains('status', 'pending') || $students->contains('status', 'not_enrolled'))
        <form action="{{ route('teacher.classes.students.enroll_all', $classRoom) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Enroll All Students</button>
        </form>
    @endif
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>Sex</th>
                <th>Mobile</th>
                <th>Email</th>
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
                    <td>{{ $student->mobile ?? '---' }}</td>
                    <td>{{ $student->email ?? '---' }}</td>
                    <td>
                        <code>{{ $student->student_id }}</code>
                        <div class="mt-2">
                            <span class="badge bg-{{ $student->status === 'enrolled' ? 'success' : ($student->status === 'pending' ? 'warning' : 'danger') }} text-uppercase">
                                {{ $student->status === 'not_enrolled' ? 'Not Enrolled' : ucfirst($student->status) }}
                            </span>
                        </div>
                    </td>
                    <td class="text-center">
                        <div class="mb-2">
                            <span class="badge bg-{{ $student->status === 'enrolled' ? 'success' : ($student->status === 'pending' ? 'warning' : 'danger') }} text-uppercase">
                                {{ $student->status === 'not_enrolled' ? 'Not Enrolled' : ucfirst($student->status) }}
                            </span>
                        </div>
                        @if($student->status !== 'enrolled')
                            <form action="{{ route('teacher.students.enroll', $student) }}" method="POST" style="display:inline;">
                                @csrf
                                <input type="hidden" name="class_room_id" value="{{ $classRoom->id }}">
                                <button type="submit" class="btn btn-sm btn-success">Enroll</button>
                            </form>
                        @endif
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
