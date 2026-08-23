@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="mx-auto max-w-6xl px-1 py-2 sm:px-0">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Family inbox</p>
            <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">Messages for {{ $student->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">Review teacher messages and send your reply here.</p>
        </div>
        <a href="{{ route('parent.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50">Back to dashboard</a>
    </div>

    @if(session('success'))
        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="mt-5 space-y-4">
        @forelse($notifications as $notification)
            <div class="card p-4 sm:p-6">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Teacher message</span>
                        <h2 class="mt-3 text-lg font-semibold text-slate-900">{{ $notification->title }}</h2>
                    </div>
                    <small class="text-muted">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                </div>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-3 sm:p-4">
                    <div class="flex items-center justify-between gap-3">
                        <strong class="text-sm text-slate-900">Conversation</strong>
                        <form method="POST" action="{{ route('parent.notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-lg border border-rose-200 bg-white px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete</button>
                        </form>
                    </div>

                    <div class="mt-3 max-h-80 overflow-y-auto rounded-xl border border-slate-200 bg-white p-3 sm:p-4">
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

                <div class="mt-4 border-t border-slate-200 pt-4">
                    <form method="POST" action="{{ route('parent.notifications.reply', $notification) }}">
                        @csrf
                        <label for="reply-{{ $notification->id }}" class="sr-only">Reply to teacher</label>
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start">
                            <textarea id="reply-{{ $notification->id }}" name="reply" class="min-h-24 w-full flex-1 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" rows="3" placeholder="Write your reply here..." required></textarea>
                            <button class="min-h-11 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 sm:shrink-0">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="card p-8 text-center text-sm text-slate-500">No notifications yet.</div>
        @endforelse
    </div>
</div>
@endsection
 