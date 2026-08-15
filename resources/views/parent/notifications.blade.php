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

                    <div class="border rounded-lg bg-white p-3" style="max-height: 320px; overflow-y: auto;">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">T</div>
                                <div>
                                    <div class="text-xs text-slate-500">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                                    <div class="mt-1 inline-block rounded-xl bg-slate-100 text-slate-900 p-3 max-w-[75%] whitespace-pre-wrap">{{ $notification->message }}</div>
                                </div>
                            </div>

                            @foreach($notification->replies as $reply)
                                @if($reply->sender === 'parent')
                                    <div class="flex items-start justify-end gap-3">
                                        <div class="flex flex-col items-end">
                                            <div class="text-xs text-slate-500">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                            <div class="mt-1 inline-block rounded-xl bg-emerald-600 text-white p-3 max-w-[75%] whitespace-pre-wrap">{{ $reply->message }}</div>
                                        </div>
                                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold">Y</div>
                                    </div>
                                @else
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">T</div>
                                        <div>
                                            <div class="text-xs text-slate-500">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                            <div class="mt-1 inline-block rounded-xl bg-slate-100 text-slate-900 p-3 max-w-[75%] whitespace-pre-wrap">{{ $reply->message }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="mt-3 border-t pt-3">
                    <form method="POST" action="{{ route('parent.notifications.reply', $notification) }}">
                        @csrf
                        <label for="reply-{{ $notification->id }}" class="sr-only">Reply to teacher</label>
                        <div class="flex gap-2 items-start">
                            <textarea id="reply-{{ $notification->id }}" name="reply" class="form-control flex-1" rows="3" placeholder="Write your reply here..." required></textarea>
                            <button class="btn btn-sm btn-primary">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="list-group-item">No notifications yet.</div>
        @endforelse
    </div>
</div>
@endsection
 