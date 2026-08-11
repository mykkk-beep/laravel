@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Notifications for {{ $student->name }}</h2>
        <a href="{{ route('student.dashboard') }}" class="btn btn-link">Back to dashboard</a>
    </div>

    <div class="list-group mt-3">
        @forelse($notifications as $n)
            <div class="list-group-item">
                <strong>{{ $n->title }}</strong>
                <div>{{ $n->message }}</div>
                <div class="small text-muted">{{ $n->created_at->diffForHumans() }}</div>
            </div>
        @empty
            <div class="list-group-item">No notifications.</div>
        @endforelse
    </div>
</div>
@endsection
