@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="mx-auto max-w-6xl px-1 py-2 sm:px-0">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Student inbox</p>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Teacher Messages</h1>
            <p class="mt-1 text-sm text-slate-500">For {{ $student->name }}. Review messages and send your replies here.</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Back to dashboard</a>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mt-5 space-y-4">
        @forelse($notifications as $notification)
            <div class="card p-4 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <div class="flex-grow-1">
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Teacher message</span>
                        <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $notification->title }}</h2>
                    </div>
                    <small class="text-muted text-nowrap">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                </div>

                <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-3 sm:p-4">
                    <div class="flex items-center justify-between gap-3">
                        <strong class="text-sm text-slate-900">Conversation</strong>
                        <form method="POST" action="{{ route('student.notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete</button>
                        </form>
                    </div>

                    <div class="message-container mt-3 rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
                        <div class="flex flex-col gap-4">
                            <!-- Teacher's Initial Message -->
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">T</div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="text-xs text-slate-500">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                                    <div class="mt-1 max-w-full whitespace-pre-wrap break-words rounded-xl bg-slate-100 p-3 text-sm text-slate-900">{{ $notification->message }}</div>
                                </div>
                            </div>

                            <!-- Conversation Replies -->
                            @foreach($notification->replies as $reply)
                                @if($reply->sender === 'parent')
                                    <!-- Student Reply (Right aligned) -->
                                    <div class="flex items-start justify-end gap-3">
                                        <div class="flex min-w-0 flex-col items-end">
                                            <div class="text-xs text-slate-500">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                            <div class="mt-1 max-w-full whitespace-pre-wrap break-words rounded-xl bg-emerald-600 p-3 text-left text-sm text-white">{{ $reply->message }}</div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-600 text-sm font-bold text-white">Y</div>
                                        </div>
                                    </div>
                                @else
                                    <!-- Teacher Reply (Left aligned) -->
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-sm font-bold text-white">T</div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="text-xs text-slate-500">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                            <div class="mt-1 max-w-full whitespace-pre-wrap break-words rounded-xl bg-slate-100 p-3 text-sm text-slate-900">{{ $reply->message }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Reply Form -->
                <div class="mt-4 border-t border-slate-200 pt-4">
                    <form method="POST" action="{{ route('student.notifications.reply', $notification) }}">
                        @csrf
                        <div>
                            <label for="reply-{{ $notification->id }}" class="mb-2 block text-sm font-semibold text-slate-700">Reply to teacher</label>
                            <textarea 
                                id="reply-{{ $notification->id }}" 
                                name="reply" 
                                class="min-h-24 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" 
                                rows="3" 
                                placeholder="Write your reply here..." 
                                required
                                ></textarea>
                        </div>
                            <button type="submit" class="mt-2 min-h-11 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">Send Reply</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-sm text-slate-500">
                No teacher messages yet.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endforelse
    </div>
</div>

<style>.message-container { max-height: 400px; overflow-y: auto; }</style>
@endsection

