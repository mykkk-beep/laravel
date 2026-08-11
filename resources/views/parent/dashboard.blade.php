@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Parent Portal</h2>
            <p class="text-muted mb-0">Student: {{ $student->name }} ({{ $student->student_id }})</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('parent.notifications') }}" class="btn btn-outline-primary position-relative">
                Notifications
                @if(!empty($notificationCount) && $notificationCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $notificationCount }}</span>
                @endif
            </a>
            <form method="POST" action="{{ route('parent.logout') }}">@csrf<button class="btn btn-outline-secondary">Logout</button></form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="mb-0">Attendance Overview</h4>
                        <span class="badge bg-info text-dark">Latest updates</span>
                    </div>

                    @if($attendance)
                        <div class="alert {{ $attendance->status === 'present' ? 'alert-success' : ($attendance->status === 'late' ? 'alert-warning' : 'alert-danger') }} mb-3">
                            <div class="fw-semibold">Today: {{ ucfirst($attendance->status) }}</div>
                            <div class="small">Recorded at {{ $attendance->time_in ?? $attendance->updated_at->format('H:i') }}</div>
                            @if($attendance->notes)
                                <div class="mt-2">Teacher note: {{ $attendance->notes }}</div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-warning mb-3">No attendance recorded yet for today.</div>
                    @endif

                    <h6 class="fw-semibold">Recent attendance history</h6>
                    <div class="list-group list-group-flush">
                        @forelse($recentAttendance as $record)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($record->date)->format('M d, Y') }}</span>
                                    <span class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'late' ? 'warning' : 'danger') }}">{{ ucfirst($record->status) }}</span>
                                </div>
                                <div class="small text-muted">{{ $record->time_in ?? 'No time recorded' }}</div>
                            </div>
                        @empty
                            <div class="list-group-item px-0">No attendance history yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Current Class Schedule</h4>
                    <div class="list-group">
                        @if($student->classRoom)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $student->classRoom->name }}</strong>
                                        <div class="text-muted">{{ $student->classRoom->classroom ?? '' }}</div>
                                    </div>
                                    <span class="badge bg-secondary">{{ $student->classRoom->date ?? '' }}</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    {{ $student->classRoom->time ?? '' }}
                                    @if(!empty($student->classRoom->end_time))
                                        - {{ $student->classRoom->end_time }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="list-group-item">No schedule available.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
