@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 py-md-4">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <h2 class="h3 h2-md mb-2">Student Portal</h2>
            <p class="text-muted mb-0 text-truncate small">Welcome, {{ $student->name }} ({{ $student->student_id }})</p>
        </div>
        <div class="col-12 col-md-6 d-flex flex-wrap gap-2 justify-content-md-end">
            <a href="{{ route('student.notifications') }}" class="btn btn-sm btn-outline-primary position-relative flex-grow-1 flex-md-grow-0">
                🔔 Notifications
                @if(!empty($notificationCount) && $notificationCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">{{ $notificationCount }}</span>
                @endif
            </a>
            <a href="{{ route('student.profile') }}" class="btn btn-sm btn-outline-secondary flex-grow-1 flex-md-grow-0">👤 Profile</a>
            <form method="POST" action="{{ route('student.logout') }}" class="d-inline w-100 w-md-auto">
                @csrf
                <button class="btn btn-sm btn-outline-secondary w-100 w-md-auto">Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content Cards -->
    <div class="row g-3 g-md-4">
        <!-- Attendance Overview Card -->
        <div class="col-12 col-lg-8 col-xl-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <h4 class="mb-0 me-2">Attendance Overview</h4>
                        <span class="badge bg-info text-dark">Latest updates</span>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row g-2 g-md-3 mb-4">
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-2 p-md-3 text-center h-100">
                                <div class="small text-muted mb-2">Total Classes</div>
                                <div class="fs-5 fs-4-md fw-bold">{{ $totalClasses }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-2 p-md-3 text-center h-100">
                                <div class="small text-muted mb-2">Present</div>
                                <div class="fs-5 fs-4-md fw-bold">{{ $presentCount }}</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="border rounded p-2 p-md-3 text-center h-100">
                                <div class="small text-muted mb-2">Attendance %</div>
                                <div class="fs-5 fs-4-md fw-bold text-success">{{ $attendancePercentage }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance History -->
                    <h6 class="fw-semibold mb-3 mt-4">Recent attendance history</h6>
                    <div class="list-group list-group-flush">
                        @forelse($attendanceRecords as $record)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                    <div class="flex-grow-1">
                                        <span class="fw-semibold d-block mb-1">{{ $record->created_at->format('M d, Y') }}</span>
                                        <div class="small text-muted">{{ $record->classRoom?->name ?? 'No class recorded' }}</div>
                                    </div>
                                    <span class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'late' ? 'warning' : 'danger') }} text-nowrap">
                                        {{ ucfirst($record->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item px-0 py-3">
                                <p class="text-muted mb-0">No attendance history yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule Card -->
        <div class="col-12 col-lg-4 col-xl-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-3 p-md-4">
                    <h4 class="mb-4">Current Class Schedule</h4>
                    <div class="list-group">
                        @if($student->classRoom)
                            <div class="list-group-item px-0 py-3">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="flex-grow-1">
                                        <strong class="d-block mb-1">{{ $student->classRoom->name }}</strong>
                                        <div class="text-muted small">{{ $student->classRoom->classroom ?? '' }}</div>
                                    </div>
                                    <span class="badge bg-secondary text-nowrap">{{ $student->classRoom->date ?? '' }}</span>
                                </div>
                                <div class="small text-muted mt-2">
                                    {{ $student->classRoom->time ?? '' }}
                                    @if(!empty($student->classRoom->end_time))
                                        - {{ $student->classRoom->end_time }}
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="list-group-item px-0 py-3">
                                <p class="text-muted mb-0">No schedule available.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media (max-width: 767.98px) {
        .fs-4-md {
            font-size: 1.25rem !important;
        }
        .h2-md {
            font-size: 1.5rem !important;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.875rem;
        }
        .w-md-auto {
            width: auto !important;
        }
    }
    
    @media (min-width: 768px) {
        .fs-4-md {
            font-size: 1.5rem !important;
        }
        .h2-md {
            font-size: 2rem !important;
        }
    }
</style>
@endsection
