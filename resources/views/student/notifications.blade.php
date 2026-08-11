@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Teacher Messages for {{ $student->name }}</h2>
            <p class="text-muted mb-0">Messages from your teacher and your recent attendance history.</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">Back to dashboard</a>
    </div>

    <div class="row g-4 mt-2">
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Teacher messages</h4>
                    <div class="list-group">
                        @forelse($notifications as $n)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <strong>{{ $n->title }}</strong>
                                        <div class="mt-2">{{ $n->message }}</div>
                                    </div>
                                    <small class="text-muted">{{ $n->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item">No teacher messages yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h4 class="mb-3">Recent attendance</h4>
                    <div class="list-group list-group-flush">
                        @forelse($recentAttendance as $record)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">{{ $record->created_at->format('M d, Y') }}</span>
                                    <span class="badge bg-{{ $record->status === 'present' ? 'success' : ($record->status === 'late' ? 'warning' : 'danger') }}">{{ ucfirst($record->status) }}</span>
                                </div>
                                <div class="small text-muted">{{ $record->classRoom?->name ?? 'No class recorded' }}</div>
                            </div>
                        @empty
                            <div class="list-group-item px-0">No recent attendance records yet.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
