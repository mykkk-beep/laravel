@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold">{{ $notification->title }}</h1>
            <p class="text-sm text-slate-500">{{ $notification->created_at->translatedFormat('M d, Y H:i') }}</p>
        </div>
        <div>
            <a href="{{ route('teacher.recommendations') }}" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">Back</a>
        </div>
    </div>

    <div class="rounded-lg border border-slate-200 bg-white p-6">
        <p class="whitespace-pre-wrap text-sm text-slate-700">{{ $notification->message }}</p>
    </div>

    @if($notification->replies->isNotEmpty())
        <div class="rounded-lg border border-slate-200 bg-white p-4">
            <h3 class="text-lg font-semibold">Replies</h3>
            @foreach($notification->replies as $reply)
                <div class="mt-3 border-t pt-3">
                    <p class="text-sm text-slate-600">{{ $reply->message }}</p>
                    <p class="text-xs text-slate-400">{{ $reply->created_at->translatedFormat('M d, Y H:i') }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
