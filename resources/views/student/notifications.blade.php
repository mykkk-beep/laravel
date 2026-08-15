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
                    <div class="space-y-4">
                        @forelse($notifications as $n)
                            <div class="border rounded-lg p-4 bg-white">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">T</div>
                                    <div>
                                        <div class="text-sm font-semibold">{{ $n->title }}</div>
                                        <div class="text-xs text-slate-500">{{ $n->created_at->format('M d, Y h:i A') }}</div>
                                        <div class="mt-2 inline-block rounded-xl bg-slate-100 text-slate-900 p-3 max-w-[75%] whitespace-pre-wrap">{{ $n->message }}</div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-slate-500">No teacher messages yet.</div>
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
