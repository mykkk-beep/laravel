@extends('layouts.app')

@section('showNavigation', 'false')

@section('content')
<div class="mx-auto w-full max-w-5xl px-3 py-4 sm:px-4 sm:py-6 md:px-6">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-semibold uppercase tracking-[0.16em] text-indigo-600">Student inbox</p>
            <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl md:text-3xl">Teacher Messages</h1>
            <p class="mt-2 text-xs sm:text-sm text-slate-500">For <span class="font-semibold text-slate-700">{{ $student->name }}</span></p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="inline-flex min-h-10 w-full items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs sm:text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 active:scale-95 sm:w-auto">
            ← Back to dashboard
        </a>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-xs sm:text-sm text-emerald-800" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <!-- Messages List -->
    <div class="space-y-4">
        @forelse($notifications as $notification)
            <div class="card overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <!-- Message Header -->
                <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-indigo-50 px-4 py-3 sm:px-6 sm:py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <span class="inline-block rounded-full bg-indigo-100 px-2 sm:px-3 py-1 text-xs font-semibold text-indigo-700 mb-2">Teacher message</span>
                            <h2 class="text-base sm:text-lg font-bold text-slate-900 line-clamp-2">{{ $notification->title }}</h2>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:shrink-0">
                            <small class="text-xs sm:text-sm text-slate-500 whitespace-nowrap">{{ $notification->created_at->format('M d, Y') }}</small>
                            <form method="POST" action="{{ route('student.notifications.destroy', $notification) }}" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this message?')" class="min-h-10 rounded-lg border border-rose-200 bg-white px-3 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Message Container -->
                <div class="p-4 sm:p-6">
                    <!-- Conversation Thread -->
                    <div class="message-container mb-6 space-y-4 rounded-lg bg-slate-50 p-4">
                        <!-- Teacher's Initial Message -->
                        <div class="flex gap-3">
                            <div class="flex-shrink-0">
                                <div class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-indigo-600 text-xs sm:text-sm font-bold text-white">T</div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs text-slate-500 mb-1">{{ $notification->created_at->format('M d, Y h:i A') }}</div>
                                <div class="w-full rounded-lg bg-white border border-slate-200 p-3 text-xs sm:text-sm text-slate-900 break-words whitespace-pre-wrap">{{ $notification->message }}</div>
                            </div>
                        </div>

                        <!-- Conversation Replies -->
                        @foreach($notification->replies as $reply)
                            @if($reply->sender === 'parent')
                                <!-- Your Reply (Right aligned) -->
                                <div class="flex gap-3 justify-end">
                                    <div class="min-w-0 max-w-[calc(100%-2.75rem)] sm:max-w-md">
                                        <div class="text-xs text-slate-500 mb-1 text-right">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                        <div class="rounded-lg bg-emerald-600 p-3 text-xs sm:text-sm text-white break-words whitespace-pre-wrap">{{ $reply->message }}</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-emerald-600 text-xs sm:text-sm font-bold text-white">S</div>
                                    </div>
                                </div>
                            @else
                                <!-- Teacher Reply (Left aligned) -->
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="flex h-8 w-8 sm:h-10 sm:w-10 items-center justify-center rounded-full bg-indigo-600 text-xs sm:text-sm font-bold text-white">T</div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs text-slate-500 mb-1">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                                        <div class="rounded-lg bg-white border border-slate-200 p-3 text-xs sm:text-sm text-slate-900 break-words whitespace-pre-wrap">{{ $reply->message }}</div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Reply Form -->
                    <form method="POST" action="{{ route('student.notifications.reply', $notification) }}" class="border-t border-slate-200 pt-4">
                        @csrf
                        <label for="reply-{{ $notification->id }}" class="mb-2 block text-xs sm:text-sm font-bold text-slate-700">Reply to teacher</label>
                        <textarea 
                            id="reply-{{ $notification->id }}" 
                            name="reply" 
                            class="w-full rounded-lg border border-slate-300 px-3 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 resize-none"
                            rows="3" 
                            placeholder="Write your reply here..." 
                            required
                        ></textarea>
                        <button type="submit" class="mt-3 min-h-10 w-full rounded-lg bg-indigo-600 px-4 py-2 text-xs sm:text-sm font-semibold text-white transition hover:bg-indigo-700 active:scale-95">
                            Send Reply
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="card p-8 text-center">
                <div class="text-sm text-slate-500">No teacher messages yet.</div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .message-container { 
        max-height: 400px; 
        overflow-y: auto;
    }
    
    @media (max-width: 640px) {
        .message-container {
            max-height: 300px;
        }
    }
</style>
@endsection
