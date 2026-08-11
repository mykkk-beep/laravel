@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Notifications for {{ $student->name }}</h2>
            <p class="text-muted mb-0">Review teacher messages and send your reply here.</p>
        </div>
        <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-secondary">Back to dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-3" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="list-group mt-3">
        @forelse($notifications as $notification)
            <div class="list-group-item p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <span class="badge bg-primary-subtle text-primary">Teacher message</span>
                        <h5 class="mt-2 mb-1">{{ $notification->title }}</h5>
                    </div>
                    <small class="text-muted">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                </div>

                <div class="border rounded p-3 bg-light mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong>Conversation</strong>
                        <form method="POST" action="{{ route('parent.notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                        </form>
                    </div>

                    <div class="border rounded-3 bg-white p-3" style="max-height: 280px; overflow-y: auto;">
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-start">
                                <div class="rounded-3 bg-primary text-black px-3 py-2" style="max-width: 75%; white-space: pre-wrap;">
                                    <div class="small fw-semibold mb-1">Teacher</div>
                                    <div>{{ $notification->message }}</div>
                                    <div class="small text-black-50 mt-2">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                                </div>
                            </div>

                            @foreach($notification->replies as $reply)
                                <div class="d-flex {{ $reply->sender === 'parent' ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="rounded-3 px-3 py-2 {{ $reply->sender === 'parent' ? 'bg-success text-black' : 'bg-primary text-black' }}" style="max-width: 75%; white-space: pre-wrap;">
                                        <div class="small fw-semibold mb-1">{{ $reply->sender === 'parent' ? 'You' : 'Teacher' }}</div>
                                        <div>{{ $reply->message }}</div>
                                        <div class="small text-black-50 mt-2">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-3 border-top pt-3">
                    <form method="POST" action="{{ route('parent.notifications.reply', $notification) }}">
                        @csrf
                        <label for="reply-{{ $notification->id }}" class="form-label">Reply to teacher</label>
                        <textarea id="reply-{{ $notification->id }}" name="reply" class="form-control" rows="3" placeholder="Write your reply here..." required></textarea>
                        <button class="btn btn-sm btn-primary mt-3">Send reply</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="list-group-item">No notifications yet.</div>
        @endforelse
    </div>
</div>
@endsection
 