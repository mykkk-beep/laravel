@extends('layouts.app')

@section('pageTitle', 'Notifications')
@section('pageSubtitle', 'Review messages from parents and respond directly.')

@section('content')
<div class="flex flex-col gap-4 rounded-[28px] border border-slate-200/80 bg-white/90 p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
    <div>
        <h2 class="text-2xl font-semibold text-slate-900">Parent Notifications</h2>
        <p class="mt-1 text-sm text-slate-500">View messages from parents and reply directly here.</p>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('teacher.dashboard') }}">Back to Dashboard</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="card-title mb-1">Recent conversations</h3>
                <p class="text-sm text-slate-500">Click a message to view replies and respond.</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-sm font-semibold text-indigo-700">{{ $messageThreads->count() }} threads</span>
        </div>

        <div class="space-y-3">
            @forelse($messageThreads as $thread)
                <details class="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                    <summary class="flex cursor-pointer items-start justify-between gap-2">
                        <div>
                            <div class="font-semibold text-slate-900">{{ $thread->title }}</div>
                            <div class="mt-1 text-sm text-slate-500">{{ $thread->student->name ?? 'Student' }} • {{ $thread->created_at->format('M d, Y h:i A') }}</div>
                        </div>
                        <span class="rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] {{ $thread->replies->last()?->sender === 'parent' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">{{ $thread->replies->last()?->sender === 'parent' ? 'Parent awaiting reply' : 'Conversation open' }}</span>
                    </summary>
                    <div class="mt-4 space-y-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-3 text-sm text-slate-700">
                            <div class="font-semibold text-slate-900">Message</div>
                            <div class="mt-1">{{ $thread->message }}</div>
                        </div>

                        <div class="border rounded-lg bg-white p-3" style="max-height: 320px; overflow-y: auto;">
                            <div class="flex flex-col gap-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">T</div>
                                    <div>
                                        <div class="text-xs text-slate-500">{{ $thread->created_at->format('M d, Y h:i A') }}</div>
                                        <div class="mt-1 inline-block rounded-xl bg-slate-100 text-slate-900 p-3 max-w-[75%] whitespace-pre-wrap">{{ $thread->message }}</div>
                                    </div>
                                </div>

                                @foreach($thread->replies as $reply)
                                    @if($reply->sender === 'parent')
                                        <div class="flex items-start justify-end gap-3">
                                            <div class="flex flex-col items-end">
                                                <div class="text-xs text-slate-500">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                                <div class="mt-1 inline-block rounded-xl bg-emerald-600 text-white p-3 max-w-[75%] whitespace-pre-wrap">{{ $reply->message }}</div>
                                            </div>
                                            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-semibold">P</div>
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

                        <form method="POST" action="{{ route('teacher.notifications.reply', $thread) }}" class="mt-3">
                            @csrf
                            <label for="reply-{{ $thread->id }}" class="sr-only">Send a reply</label>
                            <div class="flex gap-2">
                                <textarea id="reply-{{ $thread->id }}" name="reply" class="form-control flex-1" rows="2" placeholder="Write your reply here..." required></textarea>
                                <button type="submit" class="btn btn-primary">Reply</button>
                            </div>
                        </form>
                    </div>
                </details>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">No notifications or replies from parents or students yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
