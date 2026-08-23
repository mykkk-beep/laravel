@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="container-fluid px-3 px-md-4 py-3 py-md-4">
    <div class="d-flex justify-content-between align-items-start flex-column flex-md-row gap-3 mb-4">
        <div class="flex-grow-1">
            <h2 class="mb-2 h3 h2-md">Teacher Messages</h2>
            <p class="text-muted mb-0 small">For {{ $student->name }}</p>
            <p class="text-muted mb-0 small">Review teacher messages and send your replies here.</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-sm btn-outline-secondary mt-2 mt-md-0">Back to dashboard</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="list-group">
        @forelse($notifications as $notification)
            <div class="list-group-item p-3 p-md-4 mb-2">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div class="flex-grow-1">
                        <span class="badge bg-primary-subtle text-primary mb-2 d-inline-block">Teacher message</span>
                        <h5 class="mb-1 text-break">{{ $notification->title }}</h5>
                    </div>
                    <small class="text-muted text-nowrap">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                </div>

                <div class="border rounded p-3 p-md-4 bg-light mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <strong class="small">Conversation</strong>
                        <form method="POST" action="{{ route('student.notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>

                    <div class="border rounded-lg bg-white p-2 p-md-3 message-container">
                        <div class="flex flex-col gap-4">
                            <!-- Teacher's Initial Message -->
                            <div class="d-flex gap-2 gap-md-3 mb-3">
                                <div class="flex-shrink-0">
                                    <div class="badge bg-indigo-600 text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-weight: bold;">T</div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-xs text-slate-500 small mb-1">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                                    <div class="alert alert-light mb-0 p-2 p-md-3" style="word-break: break-word; white-space: pre-wrap;">{{ $notification->message }}</div>
                                </div>
                            </div>

                            <!-- Conversation Replies -->
                            @foreach($notification->replies as $reply)
                                @if($reply->sender === 'parent')
                                    <!-- Student Reply (Right aligned) -->
                                    <div class="d-flex gap-2 gap-md-3 mb-3 justify-content-end">
                                        <div class="flex-grow-1 text-end">
                                            <div class="text-xs text-slate-500 small mb-1">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                            <div class="alert alert-success mb-0 p-2 p-md-3" style="word-break: break-word; white-space: pre-wrap; text-align: left;">{{ $reply->message }}</div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="badge bg-success text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-weight: bold;">Y</div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Teacher Reply (Left aligned) -->
                                    <div class="d-flex gap-2 gap-md-3 mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="badge bg-indigo-600 text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 32px; height: 32px; font-weight: bold;">T</div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-xs text-slate-500 small mb-1">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                            <div class="alert alert-light mb-0 p-2 p-md-3" style="word-break: break-word; white-space: pre-wrap;">{{ $reply->message }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="mt-3 border-top pt-3">
                    <form method="POST" action="{{ route('student.notifications.reply', $notification) }}">
                        @csrf
                        <div class="mb-2">
                            <label for="reply-{{ $notification->id }}" class="form-label small">Reply to teacher</label>
                            <textarea 
                                id="reply-{{ $notification->id }}" 
                                name="reply" 
                                class="form-control form-control-sm" 
                                rows="3" 
                                placeholder="Write your reply here..." 
                                required
                                style="min-height: 80px;"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">Send Reply</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>No teacher messages yet.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforelse
    </div>
</div>

<style>
    .message-container {
        max-height: 400px;
        overflow-y: auto;
    }
    
    @media (max-width: 576px) {
        .h2-md {
            font-size: 1.5rem !important;
        }
        .message-container {
            max-height: 300px;
        }
        .badge {
            font-size: 0.8rem;
        }
    }
    
    @media (min-width: 577px) {
        .h2-md {
            font-size: 2rem !important;
        }
    }
    
    /* Ensure message boxes are responsive */
    .alert {
        margin-bottom: 0 !important;
        max-width: 100%;
        word-wrap: break-word;
    }
    
    /* Make badges circular */
    .rounded-circle {
        border-radius: 50% !important;
    }
</style>
@endsection

