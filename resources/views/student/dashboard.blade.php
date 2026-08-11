<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div>
        <a href="{{ route('student.profile') }}" class="btn btn-primary">View Profile</a>
        <form method="POST" action="{{ route('student.logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-danger">Logout</button>
        </form>
    </div>
    <h2>Student Dashboard</h2>
    <p>Welcome, {{ $student->name }}</p>
    <p>Student ID: {{ $student->student_id }}</p>

    <h3 class="mt-4">Attendance Statistics</h3>
    <p>Total Classes: {{ $totalClasses }}</p>
    <p>Present: {{ $presentCount }}</p>
    <p>Attendance %: {{ $attendancePercentage }}%</p>

    <h3 class="mt-4">Recent Attendance</h3>
    @if(count($attendanceRecords) > 0)
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Class</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceRecords as $record)
                    <tr>
                        <td>{{ $record->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $record->classRoom?->name ?? 'N/A' }}</td>
                        <td><span class="badge bg-{{ $record->status === 'present' ? 'success' : 'danger' }}">{{ ucfirst($record->status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info">No attendance records yet</div>
    @endif
</div>
</body>
</html>
